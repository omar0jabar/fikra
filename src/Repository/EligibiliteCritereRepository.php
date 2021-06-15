<?php

namespace App\Repository;

use App\Entity\EligibiliteCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EligibiliteCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method EligibiliteCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method EligibiliteCritere[]    findAll()
 * @method EligibiliteCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EligibiliteCritereRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EligibiliteCritere::class);
    }

    // /**
    //  * @return EligibiliteCritere[] Returns an array of EligibiliteCritere objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EligibiliteCritere
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
