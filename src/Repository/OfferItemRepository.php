<?php

namespace App\Repository;

use App\Entity\OfferItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfferItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferItem[]    findAll()
 * @method OfferItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferItem::class);
    }
}
