<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\EventPublished;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventPublished|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventPublished|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventPublished[]    findAll()
 * @method EventPublished[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPublishedRepository extends ServiceEntityRepository
{
    const SUFIX_SLUG = '%s-copie';
    const SUFIX_TITLE = '%s-copie';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventPublished::class);
    }


    public function getAllQuery($locale = 'fr', $categories = null)
    {
        $query =  $this->createQueryBuilder('e')
            ->join('e.category', "category")
            ->join('e.event', "evt")
        ;
        if (!empty($categories)) {
            $query->where('category.id IN ('.$categories.')');
        }
        $query
            ->andWhere('e.isActive = 1')
            ->andWhere('e.lang = :lang')
            ->setParameter('lang', $locale)
            ->addOrderBy('evt.isExpired', 'ASC')
            ->addOrderBy('e.dateFin', 'ASC');
        return $query->getQuery();
    }

    public function getEventSuggestion($category, $idEvent, $locale = 'fr', $limit = 3)
    {
        $column = array_rand(['a.id'=>'a.id', 'a.createdAt'=>'a.createdAt']);
        $order = array_rand(['ASC'=>'ASC', 'DESC'=>'DESC']);
        return $this->createQueryBuilder('a')
            ->join('a.event', "evt")
            ->andWhere('a.category = :category')
            ->andWhere('a.lang = :lang')
            ->andWhere('a.id != :id')
            ->andWhere('a.isActive = 1')
            ->andWhere('evt.isExpired != 1')
            ->setParameters(['category' => $category, 'id' => $idEvent, 'lang' => $locale])
            ->orderBy($column, $order)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getRecentEvents($locale = 'fr', $total = 3)
    {
        $query =  $this->createQueryBuilder('e');
        $query
            ->join('e.event', "evt")
            ->andWhere('e.lang = :lang')
            ->setParameter('lang', $locale)
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->setMaxResults($total)
            ->addOrderBy('evt.isExpired', 'ASC')
            ->addOrderBy('e.dateFin', 'ASC');
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
