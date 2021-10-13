<?php

namespace App\Service\Sticker;


use App\Entity\Sticker;
use App\Repository\StickerRepository;
use App\Service\Normalizer\StickerNormalizer;
use App\Service\Validator\RegistrationValidator;
use App\Service\Validator\StickerValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StickerService
{
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(StickerRepository $stickerRepository, EntityManagerInterface $entityManager) {
        $this->stickerRepository = $stickerRepository;
        $this->entityManager = $entityManager;
    }

    public function getAll(bool $withCoefficients, int $page, int $limit): array
    {
        $result = [];

        $normalizer = new StickerNormalizer();

        $stickers = $this->stickerRepository->findPage($page, $limit);

        $hiddenColumns = $withCoefficients ? ["updated_at", "created_at"] : ["updated_at", "created_at", "chance", "coefficient"];
        foreach($stickers as $sticker) {
            array_push($result, $normalizer->normalize($sticker, $hiddenColumns));
        }

        return $result;
    }

    public function getCount(): int
    {
        return $this->stickerRepository->count([]);
    }

    public function prepareSticker(array $query): Sticker
    {
        return (new Serializer([new ObjectNormalizer()]))
            ->denormalize($query,"App\Entity\Sticker");
    }

    public function add(Sticker $sticker): array
    {
        $response = [];

        $sticker->setChance(Sticker::MAX_CHANCE / $sticker->getCoefficient());

        $this->entityManager->persist($sticker);
        $this->entityManager->flush();

        return $response;
    }
}
