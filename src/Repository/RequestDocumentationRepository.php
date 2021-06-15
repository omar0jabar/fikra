<?php

namespace App\Repository;

use App\Entity\RequestDocumentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RequestDocumentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestDocumentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestDocumentation[]    findAll()
 * @method RequestDocumentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestDocumentationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RequestDocumentation::class);
    }

    // /**
    //  * @return RequestDocumentation[] Returns an array of RequestDocumentation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RequestDocumentation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
