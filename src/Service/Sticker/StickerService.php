<?php

namespace App\Service\Sticker;


use App\Repository\StickerRepository;
use App\Service\Normalizer\StickerNormalizer;

class StickerService
{
    private $stickerRepository;

    public function __construct(StickerRepository $stickerRepository) {
        $this->stickerRepository = $stickerRepository;
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
}
