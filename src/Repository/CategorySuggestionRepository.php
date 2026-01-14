<?php

namespace App\Repository;

use App\Entity\CategorySuggestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorySuggestion>
 */
class CategorySuggestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorySuggestion::class);
    }

    public function findPendingSuggestions()
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('cs.suggestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}