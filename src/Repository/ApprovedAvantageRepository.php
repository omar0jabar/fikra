<?php

namespace App\Repository;

use App\Entity\ApprovedAvantage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedAvantage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedAvantage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedAvantage[]    findAll()
 * @method ApprovedAvantage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedAvantageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedAvantage::class);
    }

    // /**
    //  * @return ApprovedAvantage[] Returns an array of ApprovedAvantage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApprovedAvantage
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
