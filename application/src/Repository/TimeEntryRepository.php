<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
class TimeEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    public function findOpenTimeEntriesByProject(int $projectId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.project = :projectId')
            ->andWhere('t.invoiced = false')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }
}
