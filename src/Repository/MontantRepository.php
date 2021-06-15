<?php

namespace App\Repository;

use App\Entity\Montant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Montant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Montant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Montant[]    findAll()
 * @method Montant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MontantRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Montant::class);
    }

    // /**
    //  * @return Montant[] Returns an array of Montant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Montant
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
