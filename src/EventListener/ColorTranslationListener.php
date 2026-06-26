<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Color\Service\ColorTranslationService;
use App\Entity\Color;
use Doctrine\ORM\Event\PostPersistEventArgs;

readonly class ColorTranslationListener
{
//    public function __construct(private ColorTranslationService $translationService)
//    {
//    }

//    public function postPersist(Color $color, PostPersistEventArgs $eventArgs): void
//    {
//        $this->translationService->addTranslation(
//            $color->getName(),
//            $color->getHexCode()
//        );
//    }
}
