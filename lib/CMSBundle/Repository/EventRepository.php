<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    const SUFIX_SLUG = '%s-copie';
    const SUFIX_TITLE = '%s-copie';
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, Event::class);
   }

    public function getAllQuery($locale = 'fr')
    {
        $query =  $this->createQueryBuilder('e');
        $query
            ->andWhere('e.lang = :lang')
            ->andWhere('e.isActive = 1')
            ->setParameter('lang', $locale)
            //->addOrderBy('e.isActive', 'DESC')
            ->addOrderBy('e.isExpired', 'DESC');
        return $query->getQuery();
    }

    public function getRecentEvents($locale = 'fr', $total=4)
    {
        $query =  $this->createQueryBuilder('e');
        $query
            ->andWhere('e.lang = :lang')
            ->setParameter('lang', $locale)
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->setMaxResults($total)
            ->orderBy('e.createdAt', 'ASC');
        return $query->getQuery()->getResult();
    }

    public function getEventChangeStatus($date)
    {
        $query =  $this->createQueryBuilder('e');
        $query
            ->andWhere('e.dateFin < :date')
            ->setParameter('date', $date)
            ->andWhere('e.isActive = :etat')
            ->setParameter('etat', true)
            ->orderBy('e.id', 'DESC');
        return $query->getQuery()->getResult();
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
