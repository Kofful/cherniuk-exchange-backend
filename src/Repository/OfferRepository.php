<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public const OFFER_COUNT_PER_PAGE = 10;
    public const OFFER_CLASS = "App\\Entity\\Offer";

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function getUserHistory(int $page, int $userId): array
    {
        $statusId = Offer::STATUS_CLOSED_ID;
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("off")
            ->from(self::OFFER_CLASS, "off")
            ->where("off.creator_id = $userId")
            ->orWhere("off.target_id = $userId")
            ->andWhere("off.status_id = $statusId")
            ->orderBy("off.id", "ASC")
            ->setFirstResult(($page - 1) * self::OFFER_COUNT_PER_PAGE)
            ->setMaxResults(self::OFFER_COUNT_PER_PAGE)
            ->getQuery();
        return $query->getResult();
    }

    public function getUserHistoryCount(int $userId): int
    {
        $statusId = Offer::STATUS_CLOSED_ID;
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("count(off)")
            ->from(self::OFFER_CLASS, "off")
            ->where("off.creator_id = $userId")
            ->orWhere("off.target_id = $userId")
            ->andWhere("off.status_id = $statusId")
            ->orderBy("off.id", "ASC")
            ->getQuery();
        return $query->getSingleScalarResult();
    }

    private function getCountQuery(string $stickerName, int $isAccept): string
    {
        return "(select count(s$isAccept) from " .
            OfferItemRepository::OFFER_ITEM_CLASS . " i$isAccept join " .
            StickerRepository::STICKER_CLASS . " s$isAccept " .
            "where i$isAccept.offer_id = off.id AND i$isAccept.is_accept = $isAccept " .
            "AND s$isAccept.name like '%$stickerName%' " .
            "AND s$isAccept.id = i$isAccept.sticker_id) > 0";
    }

    public function getOpenOffers(
        int $page,
        int $minTargetPayment,
        int $maxTargetPayment,
        string $targetQuery,
        int $minCreatorPayment,
        int $maxCreatorPayment,
        string $creatorQuery
    ): array {
        $statusId = Offer::STATUS_OPEN_ID;

        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("off")
            ->from(self::OFFER_CLASS, "off")
            ->join(OfferItemRepository::OFFER_ITEM_CLASS, "it")
            ->join(StickerRepository::STICKER_CLASS, "st")
            ->where($this->getCountQuery($targetQuery, 1))
            ->andWhere($this->getCountQuery($creatorQuery, 0))
            ->andWhere("off.id = it.offer_id")
            ->andWhere("st.id = it.sticker_id")
            ->andWhere("off.target_payment BETWEEN $minTargetPayment AND $maxTargetPayment")
            ->andWhere("off.creator_payment BETWEEN $minCreatorPayment AND $maxCreatorPayment")
            ->andWhere("off.target_id is null")
            ->andWhere("off.status_id = $statusId")
            ->orderBy("off.id", "ASC")
            ->groupBy("off.id")
            ->setFirstResult(($page - 1) * self::OFFER_COUNT_PER_PAGE)
            ->setMaxResults(self::OFFER_COUNT_PER_PAGE)
            ->getQuery();
        return $query->getResult();
    }

    public function getOpenOffersCount(
        int $minTargetPayment,
        int $maxTargetPayment,
        string $targetQuery,
        int $minCreatorPayment,
        int $maxCreatorPayment,
        string $creatorQuery
    ): int {
        $statusId = Offer::STATUS_OPEN_ID;

        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("off")
            ->from(self::OFFER_CLASS, "off")
            ->join(OfferItemRepository::OFFER_ITEM_CLASS, "it")
            ->join(StickerRepository::STICKER_CLASS, "st")
            ->where($this->getCountQuery($targetQuery, 1))
            ->andWhere($this->getCountQuery($creatorQuery, 0))
            ->andWhere("off.id = it.offer_id")
            ->andWhere("st.id = it.sticker_id")
            ->andWhere("off.target_payment BETWEEN $minTargetPayment AND $maxTargetPayment")
            ->andWhere("off.creator_payment BETWEEN $minCreatorPayment AND $maxCreatorPayment")
            ->andWhere("off.target_id is null")
            ->andWhere("off.status_id = $statusId")
            ->groupBy("off.id")
            ->getQuery();
        return count($query->getResult());
    }
}
