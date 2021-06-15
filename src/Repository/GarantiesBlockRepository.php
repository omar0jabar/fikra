<?php

namespace App\Repository;

use App\Entity\GarantiesBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GarantiesBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method GarantiesBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method GarantiesBlock[]    findAll()
 * @method GarantiesBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarantiesBlockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GarantiesBlock::class);
    }

    // /**
    //  * @return GarantiesBlock[] Returns an array of GarantiesBlock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GarantiesBlock
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
