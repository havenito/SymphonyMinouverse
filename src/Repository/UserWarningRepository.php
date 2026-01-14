<?php

namespace App\Repository;

use App\Entity\UserWarning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWarning>
 */
class UserWarningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWarning::class);
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('uw')
            ->andWhere('uw.user = :user')
            ->setParameter('user', $user)
            ->orderBy('uw.issuedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}