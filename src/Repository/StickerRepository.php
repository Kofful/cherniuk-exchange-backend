<?php

namespace App\Repository;

use App\Entity\Sticker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sticker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sticker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sticker[]    findAll()
 * @method Sticker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StickerRepository extends ServiceEntityRepository
{
    public const STICKER_CLASS = "App\\Entity\\Sticker";

    private QueryBuilder $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sticker::class);
        $this->queryBuilder = new QueryBuilder($this->getEntityManager());
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function findPage(int $page, int $limit): array
    {
        $query = $this->queryBuilder
            ->select("st")
            ->from(self::STICKER_CLASS, "st")
            ->orderBy("st.id", "ASC")
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();
        return $query->getArrayResult();
    }


    /**
     * get all stickers where chance is not zero
     * @return Sticker[]
     */
    public function findDroppable(): array
    {
        $query = $this->queryBuilder
            ->select("st")
            ->from(self::STICKER_CLASS, "st")
            ->where("st.chance > 0")
            ->getQuery();
        return $query->getResult();
    }

    public function getMaxDropValue(): int
    {
        $query = $this->queryBuilder
            ->select("SUM(st.chance)")
            ->from(self::STICKER_CLASS, "st")
            ->getQuery();
        return $query->getSingleScalarResult();
    }
}
