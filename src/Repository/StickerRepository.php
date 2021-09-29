<?php

namespace App\Repository;

use App\Entity\Sticker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sticker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sticker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sticker[]    findAll()
 * @method Sticker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StickerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sticker::class);
    }
}
