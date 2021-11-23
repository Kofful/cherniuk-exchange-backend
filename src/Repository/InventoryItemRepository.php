<?php

namespace App\Repository;

use App\Entity\InventoryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InventoryItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryItem[]    findAll()
 * @method InventoryItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventoryItemRepository extends ServiceEntityRepository
{
    public const INVENTORY_ITEM_CLASS = "App\\Entity\\InventoryItem";

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryItem::class);
    }

    public function addItem(InventoryItem $item): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($item);
        $entityManager->flush();
    }

    /**
     * @param $userId
     * @return InventoryItem[]
     */
    public function getItemsByUserId($userId): array
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("it")
            ->from(self::INVENTORY_ITEM_CLASS, "it")
            ->where("it.owner_id = $userId")
            ->getQuery();
        return $query->getResult();
    }
}
