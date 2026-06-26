<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Material;
use App\Entity\Pattern;
use App\Entity\Style;
use App\Entity\SubCategory;
use App\Form\PropertyColorType;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/property')]
#[IsGranted('ROLE_ADMIN')]
class PropertyController extends AbstractController
{
    private const array ENTITY_MAP = [
        'color' => Color::class,
        'material' => Material::class,
        'pattern' => Pattern::class,
        'style' => Style::class,
    ];

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/', name: 'app_property_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('property/index.html.twig');
    }

    #[Route('/{entityType}', name: 'app_property_entity_index', methods: ['GET'])]
    public function propertyIndex(string $entityType, EntityManagerInterface $em): Response
    {
        $class = $this->getEntityClass($entityType);

        $items = $em->getRepository($class)->findBy([], ['family' => 'ASC', 'g' => 'DESC']);

        return $this->render('property/entity_index.html.twig', [
            'items' => $items,
            'entityType' => $entityType,
            'title' => 'property.entity_index.title.plural.' . $entityType,
            'hexCode' => true,
            'sortOrder' => false,
            'isWarm' => false,
            'showRoute' => 'app_property_entity_show',
            'editRoute' => 'app_property_entity_edit',
            'newRoute' => 'app_property_entity_new',
        ]);
    }

    #[Route('/{entityType}/new', name: 'app_property_entity_new', methods: ['GET', 'POST'])]
    public function new(string $entityType, EntityManagerInterface $em, Request $request): Response
    {
        $class = $this->getEntityClass($entityType);

        /** @var Color|Material|Category|SubCategory $item */
        $item = new $class();
        $form = $this->createForm(PropertyColorType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();

            /** @var string $name */
            $name = $item->getName();

            $this->addFlash('success', $this->translator->trans(
                'property.entity_new.flash.success',
                [
                    '%type%' => ucfirst($this->translator
                        ->trans('property.entity_index.title.singular.' . $entityType)),
                    '%name%' => ucfirst($name)
                ],
            ));

            return $this->redirectToRoute('app_property_entity_index', [
                'entityType' => $entityType
            ]);
        }

        return $this->render('property/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'property.entity_new.title.' . $entityType,
            'entry' => $item,
            'indexRoute' => 'app_property_entity_index',
            'entityType' => $entityType,
            'isEdit' => false,
        ]);
    }

    #[Route('/{entityType}/{id}/edit', name: 'app_property_entity_edit', methods: ['GET', 'POST'])]
    public function edit(string $entityType, EntityManagerInterface $em, Request $request, int $id): Response
    {
        $class = $this->getEntityClass($entityType);

        /** @var Color|Material|Category|SubCategory $item */
        $item = $em->getRepository($class)->find($id);

        if ($item == null) {
            throw $this->createNotFoundException('Item not found');
        }

        $form = $this->createForm(PropertyColorType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            /** @var string $name */
            $name = $item->getName();

            $this->addFlash('success', $this->translator->trans(
                'property.entity_edit.flash.success',
                [
                    '%type%' => ucfirst($this->translator
                        ->trans('property.entity_index.title.singular.' . $entityType)),
                    '%name%' => ucfirst($name)
                ],
            ));

            return $this->redirectToRoute('app_property_entity_index', [
                'entityType' => $entityType
            ]);
        }

        return $this->render('property/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'property.entity_edit.title.' . $entityType,
            'entry' => $item,
            'indexRoute' => 'app_property_entity_index',
            'entityType' => $entityType,
            'isEdit' => true,
        ]);
    }

    #[Route('/{entityType}/{id}/show', name: 'app_property_entity_show', methods: ['GET'])]
    public function show(string $entityType, EntityManagerInterface $em, int $id): Response
    {
        $class = $this->getEntityClass($entityType);
        $item = $em->getRepository($class)->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Item not found');
        }

        return $this->render('property/show.html.twig', [
            'item' => $item,
            'entityType' => $entityType,
            'title' => 'property.entity_show.title.' . $entityType,
            'indexRoute' => 'app_property_entity_index',
            'editRoute' => 'app_property_entity_edit',
            'deleteRoute' => 'app_property_entity_delete',
        ]);
    }

    #[Route('/{entityType}/{id}/delete', name: 'app_property_entity_delete', methods: ['POST'])]
    public function delete(string $entityType, EntityManagerInterface $em, Request $request, int $id): Response
    {
        $class = $this->getEntityClass($entityType);
        /** @var Color|Material|Category|SubCategory $item */
        $item = $em->getRepository($class)->find($id);

        if ($item == null) {
            throw $this->createNotFoundException('Item not found');
        }

        /** @var int $id */
        $id = $item->getId();
        /** @var string $itemName */
        $itemName = $item->getName();

        if ($this->isCsrfTokenValid('delete' . $id, $request->getPayload()->getString('_token'))) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', $this->translator->trans(
                'property.entity_delete.flash.success',
                [
                    '%name%' => ucfirst($itemName),
                    '%type%' => ucfirst($this->translator->trans('property.entity_index.title.singular.' . $entityType))
                ],
            ));
        }

        return $this->redirectToRoute('app_property_entity_index', [
            'entityType' => $entityType
        ]);
    }

    /**
     * @param string $entityType
     * @return class-string<object>
     */
    private function getEntityClass(string $entityType): string
    {
        if (!isset(self::ENTITY_MAP[$entityType])) {
            throw new InvalidArgumentException('Invalid entity type');
        }

        return self::ENTITY_MAP[$entityType];
    }
}
