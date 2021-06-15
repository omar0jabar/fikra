<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @param User $author
     * @return mixed
     */
   public function findAllByStartupNotDeleted(User $author)
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.isDeleted = 0')
          ->andWhere('p.author = :author')
          ->setParameter('author', $author)
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneNotDeletedByStartuper($id, $author): ?Project
   {
      return $this->createQueryBuilder('p')
         ->andWhere('p.isDeleted = 0')
         ->andWhere('p.id = :id')
         ->setParameter('id', $id)
         ->andWhere('p.author = :author')
         ->setParameter('author', $author)
         ->getQuery()
         ->getOneOrNullResult()
         ;
   }

   // /**
   //  * @return Project[] Returns an array of Project objects
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
    public function findOneBySomeField($value): ?Project
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
