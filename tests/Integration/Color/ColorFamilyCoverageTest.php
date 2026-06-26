<?php

declare(strict_types=1);

namespace App\Tests\Integration\Color;

use App\Color\Resolver\ColorFamilyResolver;
use App\DTO\Color\ExtractedColor;
use App\Entity\Color;
use App\Repository\ColorRepository;
use App\Tests\Support\IntegrationTestCase;

class ColorFamilyCoverageTest extends IntegrationTestCase
{
    private ColorRepository $colorRepository;
    private ColorFamilyResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();
        $this->colorRepository = self::getContainer()->get(ColorRepository::class);
        $this->resolver = new ColorFamilyResolver();
    }

    public function testFixtureColorsCanResolveTheirOwnFamily(): void
    {
        /** @var list<Color> $colors */
        $colors = $this->colorRepository->findAll();

        foreach ($colors as $color) {

            $resolvedFamilies = $this->resolver->resolve(
                new ExtractedColor(
                    hex: $color->getHexCode(),
                    r: $color->getR(),
                    g: $color->getG(),
                    b: $color->getB(),
                    h: $color->getH(),
                    s: $color->getS(),
                    v: $color->getV(),
                    weight: 1.0,
                )
            );

            self::assertContains(
                $color->getFamily()?->value,
                $resolvedFamilies,
                sprintf(
                    'Color "%s" (%s) expected family %s, resolver returned [%s]',
                    $color->getName(),
                    $color->getHexCode(),
                    $color->getFamily()?->value,
                    implode(', ', $resolvedFamilies)
                )
            );
        }
    }
}
