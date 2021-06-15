<?php

namespace App\Repository;

use App\Entity\ApprovedGalleryPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedGalleryPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedGalleryPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedGalleryPhoto[]    findAll()
 * @method ApprovedGalleryPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedGalleryPhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedGalleryPhoto::class);
    }

    // /**
    //  * @return ApprovedGalleryPhoto[] Returns an array of ApprovedGalleryPhoto objects
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
    public function findOneBySomeField($value): ?ApprovedGalleryPhoto
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
