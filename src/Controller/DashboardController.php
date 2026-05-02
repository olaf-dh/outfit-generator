<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClothingItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(ClothingItemRepository $clothingItemRepository): Response
    {
        $owner = $this->getUser();

        if (!$owner instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $items = $clothingItemRepository->findBy(['owner' => $owner]);

        /** @var string $email */
        $email = $owner->getUserIdentifier();

        $recentItems = array_slice($items, 0, 5);

        return $this->render('dashboard/index.html.twig', [
            'email' => $email,
            'recentItems' => $recentItems,
            'clothingItems' => $items,
        ]);
    }
}
