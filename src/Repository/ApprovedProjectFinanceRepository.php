<?php

namespace App\Repository;

use App\Entity\ApprovedProjectFinance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedProjectFinance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedProjectFinance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedProjectFinance[]    findAll()
 * @method ApprovedProjectFinance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedProjectFinanceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedProjectFinance::class);
    }

    // /**
    //  * @return ApprovedProjectFinance[] Returns an array of ApprovedProjectFinance objects
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
    public function findOneBySomeField($value): ?ApprovedProjectFinance
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
