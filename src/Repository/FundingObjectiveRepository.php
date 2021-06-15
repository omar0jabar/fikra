<?php

namespace App\Repository;

use App\Entity\FundingObjective;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FundingObjective|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundingObjective|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundingObjective[]    findAll()
 * @method FundingObjective[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundingObjectiveRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FundingObjective::class);
    }

    // /**
    //  * @return FundingObjective[] Returns an array of FundingObjective objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FundingObjective
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
