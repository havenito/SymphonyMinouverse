<?php

namespace App\Repository;

use App\Entity\CommentReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentReport>
 */
class CommentReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentReport::class);
    }

    public function findPendingReports()
    {
        return $this->createQueryBuilder('cr')
            ->andWhere('cr.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('cr.reportedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findReportsByStatus(string $status)
    {
        return $this->createQueryBuilder('cr')
            ->andWhere('cr.status = :status')
            ->setParameter('status', $status)
            ->orderBy('cr.reportedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}