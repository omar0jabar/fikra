<?php

namespace App\Repository;

use App\Entity\ApprovedProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedProject[]    findAll()
 * @method ApprovedProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedProject::class);
    }

   public function getProjects($sectors, $verified, array $raised, $language, $search, $offset, $order)
   {

      $qb = $this->createQueryBuilder('ap');
      if (!empty($sectors)) {
         $qb->join('ap.sectors', 'aps')
             ->where('aps.id IN ('.$sectors.')');
      }
       if ($verified != null) {
           $qb->andWhere('ap.isVerified = :verified')
               ->setParameter('verified', $verified);
       }
       if (!empty($raised)) {
           if (!empty($raised['min'])) {
               $qb->andWhere('ap.amount BETWEEN :min AND :max')
                   ->setParameter('min', $raised['min'])
                    ->setParameter('max' , $raised['max']);
           } else {
               $qb->andWhere('ap.amount >= :min')
                   ->setParameter('min', $raised['max']);
           }
       }

       if ($language) {
           $qb->andWhere('ap.language = :language')
               ->setParameter('language', $language);
       }
       if ($search) {
           $qb->andWhere('ap.name LIKE :search')
               ->orWhere('ap.description LIKE :search')
               ->setParameter('search', '%'.$search.'%');
       }
       if ($order) {
           if ($order == 2) {
               $qb->orderBy('ap.createdAt', 'ASC');
           } else if ($order == 3) {
               $qb->orderBy('ap.amount', 'ASC');
           } else if ($order == 4) {
               $qb->orderBy('ap.amount', 'DESC');
           } else {
               $qb->orderBy('ap.createdAt', "DESC");
           }
       } else {
           $qb->orderBy('ap.orderBy', "DESC");
       }
      return $qb
          ->andWhere('ap.isDeleted = 0')
          ->andWhere('ap.isApproved = 1')
          ->setFirstResult($offset)
         ->setMaxResults(6)
         ->getQuery()
         ->getResult();
   }

    public function getProjectsForCount($sectors, $verified, $raised, $language, $search, $order = 1)
    {
        $qb = $this->createQueryBuilder('ap');
        if (!empty($sectors)) {
            $qb->join('ap.sectors', 'aps')
                ->where('aps.id IN ('.$sectors.')');
        }
        if ($verified != null) {
            $qb->andWhere('ap.isVerified = :verified')
                ->setParameter('verified', $verified);
        }
        if (!empty($raised)) {
            if (!empty($raised['min'])) {
                $qb->andWhere('ap.amount BETWEEN :min AND :max')
                    ->setParameter('min', $raised['min'])
                    ->setParameter('max' , $raised['max']);
            } else {
                $qb->andWhere('ap.amount >= :min')
                    ->setParameter('min', $raised['max']);
            }
        }
        if ($language) {
            $qb->andWhere('ap.language = :language')
                ->setParameter('language', $language);
        }
        if ($search) {
            $qb->andWhere('ap.name LIKE :search')
                ->orWhere('ap.description LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($order) {
            if ($order == 2) {
                $qb->orderBy('ap.createdAt', 'ASC');
            } else if ($order == 3) {
                $qb->orderBy('ap.amount', 'ASC');
            } else if ($order == 4) {
                $qb->orderBy('ap.amount', 'DESC');
            } else {
                $qb->orderBy('ap.createdAt', "DESC");
            }
        } else {
            $qb->orderBy('ap.orderBy', "DESC");
        }
        return $qb
            ->andWhere('ap.isDeleted = 0')
            ->andWhere('ap.isApproved = 1')
            ->getQuery()
            ->getResult();
    }

   public function getRecentProjects($limit = 6)
   {
      return $this->createQueryBuilder('p')
         ->andWhere('p.isDeleted = 0')
         ->andWhere('p.isApproved = 1')
         ->orderBy('p.orderBy', 'DESC')
         ->setMaxResults($limit)
         ->getQuery()
         ->getResult()
         ;
   }

    public function getSimilarProjects($sectors, $idProject, $limit = 3)
    {
        return $this->createQueryBuilder('p')
            ->join('p.sectors', 's')
            ->where('s.id IN ('.$sectors.')')
            ->andWhere('p.isDeleted = 0')
            ->andWhere('p.isApproved = 1')
            ->andWhere('p.id != :id')
            ->setParameters(['id' => $idProject])
            ->orderBy('p.orderBy', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

}
