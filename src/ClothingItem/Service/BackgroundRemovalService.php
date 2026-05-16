<?php

declare(strict_types=1);

namespace App\ClothingItem\Service;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;

readonly class BackgroundRemovalService
{
    public function __construct(
        #[Autowire('%rembg_path%')]
        private string $rembgPath,
        #[Autowire('%clothing_upload_dir%')]
        private string $uploadDir,
    ) {
    }

    /**
     * Remove the background of an image using rembg.
     * Returns the filename of the free-styled image.
     *
     * @throws ProcessFailedException when rembg failes
     * @throws InvalidArgumentException when the input file does not exist
     */
    public function removeBackground(string $inputPath): string
    {
        if (!file_exists($inputPath)) {
            throw new InvalidArgumentException(
                sprintf('Input file not found: %s', $inputPath)
            );
        }

        $outputFilename = Uuid::v4()->toRfc4122() . '.png';
        $outputPath     = $this->uploadDir . '/' . $outputFilename;

        $process = new Process([
            $this->rembgPath,
            'i',
            $inputPath,
            $outputPath,
        ]);

        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!file_exists($outputPath)) {
            throw new RuntimeException(
                sprintf('rembg produced no output file at: %s', $outputPath)
            );
        }

        return $outputFilename;
    }
}
