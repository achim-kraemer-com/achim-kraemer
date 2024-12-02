<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmailSignature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailSignature>
 *
 * @method EmailSignature|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailSignature|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailSignature[]    findAll()
 * @method EmailSignature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailSignature::class);
    }

    public function add(EmailSignature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailSignature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
