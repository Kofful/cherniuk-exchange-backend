<?php

namespace App\Service\Sticker;


use App\Entity\Sticker;
use App\Repository\StickerRepository;
use App\Service\Normalizer\StickerNormalizer;
use App\Service\Image\ImageService;
use App\Service\Validator\RegistrationValidator;
use App\Service\Validator\StickerValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StickerService
{
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;
    private ImageService $imageService;

    public function __construct(StickerRepository $stickerRepository, EntityManagerInterface $entityManager, ImageService $imageService)
    {
        $this->stickerRepository = $stickerRepository;
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
    }

    public function getAll(bool $withCoefficients, int $page, int $limit): array
    {
        $result = [];

        $normalizer = new StickerNormalizer();

        $stickers = $this->stickerRepository->findPage($page, $limit);

        $hiddenColumns = $withCoefficients ? ["updated_at", "created_at"] : ["updated_at", "created_at", "chance", "coefficient"];
        foreach ($stickers as $sticker) {
            array_push($result, $normalizer->normalize($sticker, $hiddenColumns));
        }

        return $result;
    }

    public function getCount(): int
    {
        return $this->stickerRepository->count([]);
    }

    public function prepareSticker(array $query, array $whitelist): Sticker
    {
        $query = array_intersect_key($query, array_flip($whitelist));
        return (new Serializer([new ObjectNormalizer()]))
            ->denormalize($query, "App\Entity\Sticker");
    }

    public function add(Sticker $sticker): array
    {
        $response = [];

        $sticker->setChance(Sticker::MAX_CHANCE / $sticker->getCoefficient());

        $this->entityManager->persist($sticker);
        $this->entityManager->flush();

        return $response;
    }

    public function update(Sticker $sticker): array
    {
        if (!$sticker->getId()) {
            return ["Sticker id not given."];
        }

        $stickerEntity = $this->stickerRepository->find($sticker->getId());

        if (!isset($stickerEntity)) {
            return ["Sticker with this id doesn't exist"];
        }

        if ($sticker->getCoefficient() !== null) {
            if ($sticker->getCoefficient() == 0) {
                $stickerEntity->setChance(0);
            } else {
                $stickerEntity->setChance(Sticker::MAX_CHANCE / $sticker->getCoefficient());
            }
            $stickerEntity->setCoefficient($sticker->getCoefficient());
        }

        if ($sticker->getPath()) {
            $this->imageService->removeImage($stickerEntity->getPath());
            $stickerEntity->setPath($sticker->getPath());
        }

        if ($sticker->getName()) {
            $stickerEntity->setName($sticker->getName());
        }

        $this->entityManager->flush();

        return [];
    }
}
