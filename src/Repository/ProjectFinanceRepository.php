<?php

namespace App\Repository;

use App\Entity\ProjectFinance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectFinance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFinance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFinance[]    findAll()
 * @method ProjectFinance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFinanceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectFinance::class);
    }

    // /**
    //  * @return ProjectFinance[] Returns an array of ProjectFinance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectFinance
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
