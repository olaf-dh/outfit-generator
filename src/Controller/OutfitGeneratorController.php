<?php

declare(strict_types=1);

namespace App\Controller;

use App\ClothingItem\Enum\SeasonType;
use App\ClothingItem\Enum\StyleType;
use App\ClothingItem\Enum\WeatherConditionType;
use App\DTO\Outfit\OutfitSuggestion;
use App\Entity\ClothingItem;
use App\Entity\User;
use App\Form\OutfitGeneratorType;
use App\Outfit\Service\OutfitSuggestionService;
use App\Repository\ClothingItemRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/outfit-generator')]
#[IsGranted('ROLE_USER')]
class OutfitGeneratorController extends AbstractController
{
    public function __construct(
        private readonly OutfitSuggestionService $suggestionService,
        private readonly ClothingItemRepository $repository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/', name: 'app_outfit_generator', methods: ['GET', 'POST'])]
    public function generateOutfit(Request $request): Response
    {
        /** @var User $owner */
        $owner = $this->getUser();

        $form = $this->createForm(OutfitGeneratorType::class, null, [
            'owner' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        $suggestions = null;
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Collection<int, ClothingItem> $seedItemsData */
            $seedItemsData = $form->get('seedItems')->getData();

            /** @var array<int, ClothingItem> $seedItems */
            $seedItems = $seedItemsData->toArray();

            /** @var StyleType $style */
            $style     = $form->get('style')->getData();

            /** @var SeasonType $season */
            $season    = $form->get('season')->getData();

            /** @var WeatherConditionType|null $weather */
            $weather   = $form->get('weather')->getData();

            /**
             * @var array<int, ClothingItem> $suggestions
             * @return OutfitSuggestion[]
             */
            $suggestions = $this->suggestionService->suggest(
                seedItems: $seedItems,
                style: $style,
                season: $season,
                weather: $weather
            );

            if (empty($suggestions)) {
                $this->addFlash('warning', $this->translator->trans('outfit.generator.no_suggestions'));
            }
        }

        $allItems = $this->repository->findByOwner($owner);
        $itemsById = [];
        $grouped = [];

        foreach ($allItems as $item) {
            /** @var int $id */
            $id = $item->getId();
            $itemsById[$id] = $item;

            /** @var string $categoryName */
            $categoryName = $item->getSubCategory()?->getCategory()?->getName();
            $grouped[$categoryName][] = $item;
        }

        return $this->render('outfit/generate.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'suggestions' => $suggestions,
            'itemsById'   => $itemsById,
            'grouped'     => $grouped,
        ]);
    }
}
