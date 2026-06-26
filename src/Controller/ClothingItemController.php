<?php

declare(strict_types=1);

namespace App\Controller;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\MessageHandler\AnalyzeClothingItemHandler;
use App\ClothingItem\Service\BatchReviewProgressService;
use App\ClothingItem\Service\ClothingItemDeleter;
use App\ClothingItem\Service\ClothingItemPhotoService;
use App\ClothingItem\Service\ClothingItemPhotoUploader;
use App\Color\Normalizer\ColorReductionService;
use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\User;
use App\Form\ClothingItemType;
use App\Repository\ClothingItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/clothing-item')]
#[IsGranted('ROLE_USER')]
final class ClothingItemController extends AbstractController
{
    public function __construct(
        private readonly AnalyzeClothingItemHandler $handler,
        private readonly ClothingItemDeleter $deleter,
        private readonly ClothingItemPhotoUploader $photoUploader,
        private readonly ClothingItemRepository $repository,
        private readonly ColorReductionService $colorReduction,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
        private readonly ClothingItemPhotoService $photoService,
        private readonly BatchReviewProgressService $batchReviewProgress,
    ) {
    }

    #[Route('/', name: 'app_clothing_item_index', methods: ['GET'])]
    public function index(): Response
    {
        $owner = $this->getUser();

        if (!$owner instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $items = $this->repository->findByOwner($owner);

        return $this->render('clothing_item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/new', name: 'app_clothing_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $item = new ClothingItem();
        $owner = $this->getUser();

        if ($owner instanceof User) {
            $item->setOwner($owner);
        }

        $form = $this->createForm(ClothingItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile !== null) {
                try {
                    $fileName = $this->photoUploader->uploadAnalysis($photoFile);
                    $item->setPhotoPath($fileName);
                } catch (InvalidArgumentException $e) {
                    $this->addFlash('error', $this->translator->trans('clothing_item.form.error.invalid_image'));
                    return $this->redirectToRoute('app_clothing_item_new', ['form' => $form]);
                }
            }

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('flash.clothing_item.created'));

            /** @var int $id */
            $id = $item->getId();

            if ($item->getPhotoPath() !== null) {
                $this->handler->__invoke(new AnalyzeClothingItemMessage($id));
            } else {
                $item->setStatus(ClothingItemStatus::COMPLETE);
                $this->entityManager->flush();
            }

            return $this->redirectToRoute('app_clothing_item_edit', [
                'id' => $id,
                'form' => $form
            ]);
        }

        return $this->render('clothing_item/form.html.twig', [
            'form' => $form->createView(),
            'from' => null,
            'title' => 'clothing_item.add.title',
            'isEdit' => false,
            'list_path' => 'app_clothing_item_index'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clothing_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClothingItem $item): Response
    {
        $this->denyAccessUnlessGranted('edit', $item);

        $owner = $this->getUser();

        if (!$owner instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $from = $request->query->get('from');
        $form = $this->createForm(ClothingItemType::class, $item, ['isEdit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Color $primaryColor */
            $primaryColor = $form->get('primaryColor')->getData();

            /** @var list<Color> $secondaryColors */
            $secondaryColors = $form->get('secondaryColors')->getData();

            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photo')->getData();
            $newFile = false;

            /** @var int $id */
            $id = $item->getId();

            if ($photoFile !== null) {
                try {
                    $this->photoService->replacePhoto($item, $photoFile);
                    $newFile = true;
                } catch (InvalidArgumentException $e) {
                    $this->addFlash('error', $this->translator->trans('clothing_item.form.error.invalid_image'));
                    return $this->redirectToRoute('app_clothing_item_edit', ['item' => $item, 'form' => $form]);
                }
            } else {
                $item->setStatus(ClothingItemStatus::COMPLETE);
            }

            $this->colorReduction->reduction($item, $primaryColor, $secondaryColors);
            $this->colorReduction->normalize($item);

            /** @var UploadedFile|null $displayPhotoFile */
            $displayPhotoFile = $form->get('displayPhoto')->getData();

            if ($displayPhotoFile !== null) {
                // Replace display photo
                try {
                    $this->photoService->replaceDisplayPhoto($item, $displayPhotoFile);
                } catch (Exception $e) {
                    $this->addFlash('warning', $this->translator->trans('clothing_item.form.error.background_removal'));
                }
            }

            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('flash.clothing_item.updated'));

            if ($newFile) {
                return $this->redirectToRoute('app_clothing_item_edit', [
                    'id' => $id,
                    'form' => $form
                ]);
            }

            return $from === 'batch_review'
                ? $this->redirectToRoute('app_clothing_item_batch_review')
                : $this->redirectToRoute('app_clothing_item_index');
        }

        // Progress bar for batch review
        [$totalUnreviewed, $currentPosition] = $from === 'batch_review'
            ? $this->batchReviewProgress->getProgress($owner, $item)
            : [0, 0];

        return $this->render('clothing_item/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'clothing_item.edit.title',
            'isEdit' => true,
            'list_path' => $from === 'batch_review' ? 'app_clothing_item_batch_review' : 'app_clothing_item_index',
            'from' => $from,
            'totalUnreviewed' => $totalUnreviewed,
            'currentPosition' => $currentPosition,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_clothing_item_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, ClothingItem $item): Response
    {
        $this->denyAccessUnlessGranted('delete', $item);

        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->getString('_token'))) {
            $this->deleter->delete($item);
            $this->addFlash('success', $this->translator->trans('flash.clothing_item.deleted'));
        }

        return $this->redirectToRoute('app_clothing_item_index');
    }

    #[Route('/batch-review', name: 'app_clothing_item_batch_review', methods: ['GET'])]
    public function batchReview(): Response
    {
        $owner = $this->getUser();

        if (!$owner instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $items = $this->repository->findUnreviewedByOwner($owner);

        if (empty($items)) {
            $this->addFlash('info', $this->translator->trans('batch_upload.review.empty'));
            return $this->redirectToRoute('app_clothing_item_index');
        }

        // Start redirect when at least one item is analyzed
        $analyzedItems = array_filter(
            $items,
            fn(ClothingItem $item) => $item->getStatus() === ClothingItemStatus::ANALYZED
        );

        if (empty($analyzedItems)) {
            // When no items are analyzed yet, show a waiting page
            return $this->render('clothing_item/batch_waiting.html.twig', [
                'pendingCount' => count($items),
            ]);
        }

        return $this->redirectToRoute('app_clothing_item_edit', [
            'id'   => array_values($analyzedItems)[0]->getId(),
            'from' => 'batch_review',
        ]);
    }

    #[Route('/{id}/analysis-photo', name: 'app_clothing_item_analysis_photo', methods: ['GET'])]
    public function analysisPhoto(
        ClothingItem $item,
        #[Autowire('%clothing_analysis_dir%')]
        string $analysisDir
    ): Response {
        $this->denyAccessUnlessGranted('view', $item);

        if ($item->getPhotoPath() === null) {
            throw $this->createNotFoundException('No analysis photo available');
        }

        $path = $analysisDir . '/' . $item->getPhotoPath();

        if (!file_exists($path)) {
            throw $this->createNotFoundException('Analysis photo not found');
        }

        return new BinaryFileResponse($path);
    }

//    #[Route('/upload', name: 'app_clothing_item_upload', methods: ['POST'])]
//    public function uploadClothing(
//        Request $request,
//        ColorExtractionApiService $apiService,
//        ColorMatchingService $colorMatcher
//    ): Response {
//        $files = $request->files->all('images');
//
//        if (empty($files)) {
//            $this->addFlash('warning', 'clothing_item.upload.no_files');
//            return $this->redirectToRoute('app_clothing_item_index');
//        }
//
//        foreach ($files as $file) {}
//    }
}
