<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Entity\Article;
use EgioDigital\CMSBundle\Entity\Block;
use EgioDigital\CMSBundle\Entity\CategoryArticle;
use EgioDigital\CMSBundle\Entity\CategoryPage;
use EgioDigital\CMSBundle\Entity\Page;
use EgioDigital\CMSBundle\Entity\Row;
use Faker\Factory;

class CMSFixtures extends Fixture
{
   public function load(ObjectManager $manager)
   {
      $faker = Factory::create('en_US');

      $langues = ['en', 'fr'];
      $blocks = ['text', 'image', 'video'];
      $cols = [3, 4, 6, 8, 9, 12];

      for ($i=1; $i < 10; $i++) {
         $categoryPage = new CategoryPage();
         $categoryPage->setTitle($faker->domainWord)
            ->setLang($langues[mt_rand(0,1)]);
         $manager->persist($categoryPage);
         for ($j=1; $j < mt_rand(3,5) ; $j++) {
            $page = new Page();
            $page->setTitle($faker->sentence())
               ->setMetaTitle($faker->sentence())
               ->setMetaTags('tag1,tag2,tag3,tag4')
               ->setMetaDescription($faker->paragraph(2))
               ->setHtmlIdAttr($faker->word)
               ->setHtmlClassAttr($faker->word)
               ->setIsActive(true)
               ->setCategory($categoryPage)
               ->setLang($categoryPage->getLang())
            ;
            $manager->persist($page);
            $manager->flush();

            for ($l=1; $l < mt_rand(2, 4); $l++) {
               $row = new Row();
               $row->setClass('class'.$l)
                  ->setIdHtml('id'.$l)
                  ->setType('page')
                  ->setIdCms($page->getId())
                  ->setIndexRow($l);
               $manager->persist($row);

               for ($k=1; $k < mt_rand(1, 3); $k++) {
                  $block = new Block();
                  $block->setTitle($faker->sentence())
                     ->setPosition($k)
                     ->setColLarge($cols[mt_rand(0, 5)])
                     ->setClearfix(true)
                     //->setIsActive(true)
                     ->setWidth(420)
                     ->setHeight(315)
                     ->setRow($l)
                     ->setPage($page)
                  ;
                  $blockName = $blocks[mt_rand(0,2)];
                  if ($blockName == 'image') {
                     $block->setImageName('simple.jpg');
                  } elseif ($blockName == 'text') {
                     $block->setContent('<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>');
                  } elseif ($blockName == 'video') {
                     $block->setLinkVideo('https://www.youtube.com/watch?v=rmCA3qQkqso');
                  }
                  $block->setType($blockName);
                  $manager->persist($block);

               }
            }
         }
      }

      for ($i=1; $i < 10; $i++) {
         $categoryArticle = new CategoryArticle();
         $categoryArticle->setTitle($faker->domainWord);
         $categoryArticle->setLang($langues[mt_rand(0,1)]);
         $manager->persist($categoryArticle);
         for ($j=1; $j < mt_rand(3,5) ; $j++) {
            $article = new Article();
            $article->setTitle($faker->sentence())
               ->setMetaTitle($faker->sentence())
               ->setMetaTags('tag1,tag2,tag3,tag4')
               ->setMetaDescription($faker->paragraph(2))
               ->setHtmlIdAttr($faker->word)
               ->setHtmlClassAttr($faker->word)
               ->setContent($faker->paragraph(2))
               ->setIsActive(true)
               ->setCategory($categoryArticle)
               ->setLang($categoryArticle->getLang())
               ->setDateTri(new \DateTime())
            ;
            $manager->persist($article);
            $manager->flush();

            for ($l=1; $l < mt_rand(2, 4); $l++) {
               $row = new Row();
               $row->setClass('class'.$l)
                  ->setIdHtml('id'.$l)
                  ->setType('article')
                  ->setIdCms($article->getId())
                  ->setIndexRow($l);
               $manager->persist($row);

               for ($k=1; $k < mt_rand(1, 4); $k++) {
                  $block = new Block();
                  $block->setTitle($faker->sentence())
                     ->setPosition($k)
                     ->setColLarge($cols[mt_rand(0, 5)])
                     ->setClearfix(true)
                     //->setIsActive(true)
                     ->setWidth(420)
                     ->setHeight(315)
                     ->setRow($l)
                     ->setArticle($article)
                  ;
                  $blockName = $blocks[mt_rand(0,2)];
                  if ($blockName == 'image') {
                     $block->setImageName('simple.jpg');
                  } elseif ($blockName == 'text') {
                     $block->setContent('<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>');
                  } elseif ($blockName == 'video') {
                     $block->setLinkVideo('https://www.youtube.com/watch?v=rmCA3qQkqso');
                  }
                  $block->setType($blockName);
                  $manager->persist($block);
               }
            }

         }
      }

      $manager->flush();
   }
}
