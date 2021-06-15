<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    const SUFIX_SLUG = '%s-copie';
    const SUFIX_TITLE = '%s-copie';
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, Article::class);
   }

   // /**
   //  * @return Article[] Returns an array of Article objects
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
   public function findOneBySomeField($value): ?Article
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
       $query = $this->createQueryBuilder('a')
           ->andWhere('a.slug = :val')
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
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.title = :val')
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
