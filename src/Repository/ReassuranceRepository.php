<?php

namespace App\Repository;

use App\Entity\Reassurance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Reassurance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reassurance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reassurance[]    findAll()
 * @method Reassurance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReassuranceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reassurance::class);
    }

    // /**
    //  * @return Reassurance[] Returns an array of Reassurance objects
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
    public function findOneBySomeField($value): ?Reassurance
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
