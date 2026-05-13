<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Domain\Outfit\Message\AnalyzeClothingItemMessage;
use App\Domain\Outfit\MessageHandler\AnalyzeClothingItemHandler;
use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\User;
use App\Form\ClothingItemType;
use App\Repository\ClothingItemRepository;
use App\Service\ClothingItemDeleter;
use App\Service\ClothingItemPhotoUploader;
use App\Service\ColorReductionService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                    $fileName = $this->photoUploader->upload($photoFile);
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
            'title' => 'clothing_item.add.title',
            'isEdit' => false,
            'list_path' => 'app_clothing_item_index'
        ]);
    }

    #[Route('/{id}', name: 'app_clothing_item_show', methods: ['GET'])]
    public function show(ClothingItem $item): Response
    {
        $this->denyAccessUnlessGranted('view', $item);

        return $this->render('clothing_item/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clothing_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClothingItem $item): Response
    {
        $this->denyAccessUnlessGranted('edit', $item);

        $form = $this->createForm(ClothingItemType::class, $item, ['isEdit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Color $color */
            $color = $form->get('itemColors')->getData();
            // Update primary color
            $selectedColor = $color->getId();
            if ($selectedColor !== null) {
                foreach ($item->getItemColors() as $itemColor) {
                    $itemColor->setIsPrimary(
                        $itemColor->getColor()->getId() === $selectedColor
                    );
                }
            }

            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photo')->getData();
            $newFile = false;

            /** @var int $id */
            $id = $item->getId();

            if ($photoFile !== null) {
                $newFile = true;
                if ($item->getPhotoPath() !== null) {
                    $this->photoUploader->delete($item->getPhotoPath());
                    $itemColors = $item->getItemColors();
                    foreach ($itemColors as $itemColor) {
                        $item->removeItemColor($itemColor);
                    }
                }

                try {
                    $fileName = $this->photoUploader->upload($photoFile);
                    $item->setPhotoPath($fileName);
                } catch (InvalidArgumentException $e) {
                    $this->addFlash('error', $this->translator->trans('clothing_item.form.error.invalid_image'));
                    return $this->redirectToRoute('app_clothing_item_edit', ['item' => $item, 'form' => $form]);
                }
                $this->handler->__invoke(new AnalyzeClothingItemMessage($id));
            } else {
                $item->setStatus(ClothingItemStatus::COMPLETE);
            }

            $this->colorReduction->normalize($item);

            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('flash.clothing_item.updated'));

            if ($newFile) {
                return $this->redirectToRoute('app_clothing_item_edit', [
                    'id' => $id,
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute('app_clothing_item_index');
        }

        return $this->render('clothing_item/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'clothing_item.edit.title',
            'isEdit' => true,
            'list_path' => 'app_clothing_item_index',
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
}
