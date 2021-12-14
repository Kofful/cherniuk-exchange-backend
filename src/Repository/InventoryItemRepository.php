<?php

namespace App\Repository;

use App\Entity\InventoryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;

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
     * @param int $userId
     * @return InventoryItem[]
     */
    public function getItemsByUserId(int $userId, int $page): array
    {
        $limit = 25;
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("it")
            ->from(self::INVENTORY_ITEM_CLASS, "it")
            ->where("it.owner_id = $userId")
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();
        return $query->getResult();
    }

    public function getItemsCountByUserId(int $userId): int
    {
        $queryBuilder = new QueryBuilder($this->getEntityManager());
        $query = $queryBuilder
            ->select("count(it.id)")
            ->from(self::INVENTORY_ITEM_CLASS, "it")
            ->where("it.owner_id = $userId")
            ->getQuery();
        $result = $query->getSingleScalarResult();
        return $result;
    }

    public function sellItem(int $itemId, int $price): bool
    {
        $isSold = true;
        try {
            $item = $this->find($itemId);
            $user = $item->getOwner();
            $newWallet = $user->getWallet() + $price;
            $user->setWallet($newWallet);

            $this->getEntityManager()->remove($item);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $isSold = false;
        }
        return $isSold;
    }
}
