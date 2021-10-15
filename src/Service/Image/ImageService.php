<?php

namespace App\Service\Image;

use App\Service\Validator\StickerValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageService
{
    public const PNG_EXTENSION = "png";

    private string $targetDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->targetDir = $kernel->getProjectDir() . $_ENV["STICKER_PATH"];
    }

    public function saveImageToDirectory($file): ?string
    {
        if (!isset($file) || $file->guessExtension() != self::PNG_EXTENSION) {
            return null;
        }
        $storedFile = new StoredFile();
        $storedFile->setName(time());
        $storedFile->setExtension($file->guessExtension());
        $file->move($this->targetDir, $storedFile->getName() . "." . $storedFile->getExtension());
        $this->cropFile($storedFile, 250);
        $this->cropFile($storedFile, 100, "_100");
        return $storedFile->getName() . "." . $storedFile->getExtension();
    }

    public function removeImage(string $fileName): void
    {
        unlink($this->targetDir . $fileName);
        $dotPosition = strpos($fileName, ".");
        $extension = substr($fileName, $dotPosition);
        $name = substr($fileName, 0, $dotPosition);
        unlink($this->targetDir . $name . "_100" . $extension);
    }

    public function cropFile(StoredFile $file, int $size, string $suffix = ""): void
    {
        $original = imagecreatefrompng($this->targetDir . $file->getName() . "." . $file->getExtension());
        $originalSize = getimagesize($this->targetDir . $file->getName() . "." . $file->getExtension());
        $resizedStandard = imagecreatetruecolor($size, $size);
        imagealphablending($resizedStandard, false);
        imagesavealpha($resizedStandard, true);
        $transparent = imagecolorallocatealpha($resizedStandard, 255, 255, 255, 127);
        imagefilledrectangle($resizedStandard, 0, 0, $size, $size, $transparent);
        imagecopyresampled($resizedStandard, $original,
            0, 0, 0, 0,
            $size, $size, $originalSize[0], $originalSize[1]);
        imagepng($resizedStandard, $this->targetDir . $file->getName() . "$suffix." . $file->getExtension());
    }
}
