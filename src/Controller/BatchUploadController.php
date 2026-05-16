<?php

namespace App\Controller;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\Service\ClothingItemPhotoUploader;
use App\Entity\ClothingItem;
use App\Entity\User;
use App\Form\BatchUploadType;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/clothing-item/batch-upload')]
#[IsGranted('ROLE_USER')]
class BatchUploadController extends AbstractController
{
    public function __construct(
        private readonly ClothingItemPhotoUploader $photoUploader,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/', name: 'app_clothing_item_batch_upload', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(BatchUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $photos */
            $photos = $form->get('photos')->getData();
            $owner  = $this->getUser();

            if (!$owner instanceof User) {
                return $this->redirectToRoute('app_register');
            }

            $successCount = 0;
            $errorCount   = 0;

            foreach ($photos as $photo) {
                try {
                    // File name without extension as temporary name
                    $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $cleanName    = $this->cleanFilename($originalName);

                    // Save photo
                    $filename = $this->photoUploader->uploadAnalysis($photo);

                    // ClothingItem create
                    $item = new ClothingItem();
                    $item->setName($cleanName);
                    $item->setPhotoPath($filename);
                    $item->setOwner($owner);
                    $item->setStatus(ClothingItemStatus::PENDING);
                    $item->setMinLayerDepth(1);
                    $item->setMaxLayerDepth(3);

                    $this->entityManager->persist($item);
                    $this->entityManager->flush();

                    /** @var int $id */
                    $id = $item->getId();

                    // Lineup analysis in Queue
                    $this->messageBus->dispatch(new AnalyzeClothingItemMessage($id));

                    $successCount++;
                } catch (InvalidArgumentException $e) {
                    $errorCount++;
                } catch (ExceptionInterface $e) {
                }
            }

            // Flash-Messages
            if ($successCount > 0) {
                $this->addFlash('success', $this->translator->trans(
                    'batch_upload.flash.success',
                    ['%count%' => $successCount]
                ));
            }

            if ($errorCount > 0) {
                $this->addFlash('warning', $this->translator->trans(
                    'batch_upload.flash.errors',
                    ['%count%' => $errorCount]
                ));
            }

            return $this->redirectToRoute('app_clothing_item_batch_review');
        }

        return $this->render('clothing_item/batch_upload.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Cleaned up file name for usage as ClothingItem-Name.
     * Replace underscore and hyphen with spaces
     */
    private function cleanFilename(string $filename): string
    {
        $clean = str_replace(['_', '-'], ' ', $filename);
        $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;
        return trim(ucwords(strtolower($clean)));
    }
}
