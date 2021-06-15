<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\ArticlePublished;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticlePublished|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticlePublished|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticlePublished[]    findAll()
 * @method ArticlePublished[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlePublishedRepository extends ServiceEntityRepository
{
    const SUFIX_SLUG = '%s-copie';
    const SUFIX_TITLE = '%s-copie';
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArticlePublished::class);
    }


    public function getAllQuery($locale = 'fr', $categories = null)
    {
        $query =  $this->createQueryBuilder('a');
        if (!empty($categories)) {
            $query->join('a.category', 'category')
                ->where('category.id IN ('.$categories.')');
        }
        $query
            ->andWhere('a.isActive = 1')
            ->andWhere('a.lang = :langue')
            ->setParameter('langue', $locale)
            ->orderBy('a.dateTri', 'DESC');
        return $query->getQuery();
    }

    public function getArticlesSuggestion($category, $idArticle, $locale = 'fr', $limit = 3)
    {
        $column = array_rand(['a.id'=>'a.id', 'a.createdAt'=>'a.createdAt']);
        $order = array_rand(['ASC'=>'ASC', 'DESC'=>'DESC']);
        return $this->createQueryBuilder('a')
            ->andWhere('a.category = :category')
            ->andWhere('a.lang = :langue')
            ->andWhere('a.id != :id')
            ->andWhere('a.isActive = 1')
            ->setParameters(['category' => $category, 'id' => $idArticle, 'langue' => $locale])
            ->orderBy($column, $order)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getThreeArticlesSuggestion($locale = 'fr', $limit = 3)
    {
        $column = array_rand(['a.id'=>'a.id', 'a.createdAt'=>'a.createdAt']);
        $order = array_rand(['ASC'=>'ASC', 'DESC'=>'DESC']);
        return $this->createQueryBuilder('a')
            ->andWhere('a.lang = :lang')
            ->andWhere('a.isActive = 1')
            ->setParameters(['lang' => $locale])
            ->orderBy($column, $order)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getArticlesForHome($locale = 'fr', $limit = 3)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.lang = :langue')
            ->setParameter('langue', $locale)
            ->orderBy('a.dateTri', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
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
