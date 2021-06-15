<?php

namespace App\Repository;

use App\Entity\DepensesType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DepensesType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepensesType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepensesType[]    findAll()
 * @method DepensesType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepensesTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DepensesType::class);
    }

    // /**
    //  * @return DepensesType[] Returns an array of DepensesType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DepensesType
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
