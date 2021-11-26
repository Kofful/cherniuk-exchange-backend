<?php

namespace App\Service\Sticker;


use App\Entity\Sticker;
use App\Entity\User;
use App\Repository\StickerRepository;
use App\Service\Image\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StickerService
{
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;
    private ImageService $imageService;

    public const STICKER_COOLDOWN = 10;

    public function __construct(StickerRepository $stickerRepository, EntityManagerInterface $entityManager, ImageService $imageService)
    {
        $this->stickerRepository = $stickerRepository;
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
    }

    public function addPath(Sticker $sticker): void
    {
        $oldPath = $sticker->getPath();
        $sticker->setPathSmall($_ENV["STICKER_PATH"] . explode(".", $oldPath)[0] . "_100.png");
        $sticker->setPath( $_ENV["STICKER_PATH"] . $oldPath);
    }

    public function getAll(int $page, int $limit): array
    {
        $stickers = $this->stickerRepository->findPage($page, $limit);

        foreach ($stickers as $sticker) {
            $this->addPath($sticker);
        }

        return $stickers;
    }

    public function getCount(): int
    {
        return $this->stickerRepository->count([]);
    }

    public function prepareSticker(array $query, array $whitelist): Sticker
    {
        $query = array_intersect_key($query, array_flip($whitelist));
        $serializer = new Serializer([new ObjectNormalizer()]);

        return $serializer->denormalize($query, "App\Entity\Sticker");
    }

    public function add(Sticker $sticker): array
    {
        $result = [];

        $sticker->setChance(Sticker::MAX_CHANCE / $sticker->getCoefficient());

        $this->entityManager->persist($sticker);
        $this->entityManager->flush();

        return $result;
    }

    public function update(Sticker $sticker): array
    {
        if (!$sticker->getId()) {
            return ["sticker.id.not.given"];
        }

        $stickerEntity = $this->stickerRepository->find($sticker->getId());

        if (!isset($stickerEntity)) {
            return ["sticker.not.exist"];
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

    public function giveStickerToUser(User $user): ?Sticker
    {
        $result = null;
        $rewardedAt = $user->getRewardedAt();
        $difference = self::STICKER_COOLDOWN;

        if (isset($rewardedAt)) {
            $now = time();
            $rewardedAt = $rewardedAt->getTimestamp();
            //getting difference in minutes
            $difference = floor(($now - $rewardedAt) / 60);
        }

        if ($difference >= self::STICKER_COOLDOWN) {
            $stickers = $this->stickerRepository->findDroppable();
            $max = $this->stickerRepository->getMaxDropValue();
            $randValue = rand(1, $max);

            foreach ($stickers as $sticker) {
                $randValue -= $sticker->getChance();
                if($randValue <= 0) {
                    $result = $sticker;
                    break;
                }
            }
        }

        return $result;
    }
}
