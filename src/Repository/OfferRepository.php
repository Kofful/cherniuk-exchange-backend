<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
