<?php

namespace App\Repository;

use App\Entity\BackgroundSlider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BackgroundSlider|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackgroundSlider|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackgroundSlider[]    findAll()
 * @method BackgroundSlider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackgroundSliderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BackgroundSlider::class);
    }

    // /**
    //  * @return BackgroundSlider[] Returns an array of BackgroundSlider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BackgroundSlider
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
