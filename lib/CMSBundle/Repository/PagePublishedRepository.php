<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\PagePublished;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PagePublished|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagePublished|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagePublished[]    findAll()
 * @method PagePublished[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagePublishedRepository extends ServiceEntityRepository
{
    const SUFIX_SLUG = '%s-copie';
    const SUFIX_TITLE = '%s-copie';
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, PagePublished::class);
   }

   public function getAllQuery($categories = null)
    {
        $query =  $this->createQueryBuilder('p');
        if (!is_null($categories)) {
            $query->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $categories);
        }
        $query->andWhere('p.isActive = 1')
            ->orderBy('p.id', 'DESC');
       return $query->getQuery();
    }

   // /**
   //  * @return Page[] Returns an array of Page objects
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
   public function findOneBySomeField($value): ?Page
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
   */

    public function getSlug($slug): ?String
    {
        $slug = sprintf(self::SUFIX_SLUG, $slug);
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (is_null($query)) {
            return $slug;
        } else {
            return $this->getSlug($slug);
        }
    }

    public function getTitle($title): ?String
    {
        $title = sprintf(self::SUFIX_TITLE, $title);
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.title = :val')
            ->setParameter('val', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (is_null($query)) {
            return $title;
        } else {
            return $this->getTitle($title);
        }
    }
}
