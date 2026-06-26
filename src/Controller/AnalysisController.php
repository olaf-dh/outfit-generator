<?php

declare(strict_types=1);

namespace App\Controller;

use App\Color\Service\ColorConverterService;
use App\Entity\ClothingItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/analysis')]
class AnalysisController extends AbstractController
{
    #[Route('/clothing/{id}', name: 'app_clothing_anlysis_show', methods: ['GET'])]
    public function show(ClothingItem $item, ColorConverterService $converter): Response
    {
        $colorAnalysis = $item->getColorAnalysis();
        $hexCodes = $colorAnalysis?->getExtractedColors();

        return $this->render('analysis/clothing_item.html.twig', [
            'item' => $item,
            'hexCodes' => $hexCodes,
        ]);
    }
}
