<?php

namespace App\DataFixtures;

use App\Entity\CommentCaMarcheBlock;
use App\Entity\GarantiesBlock;
use App\Entity\Ip;
use App\Entity\Maintenance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ConfigurationFixtures extends Fixture
{
   /**
    * @param ObjectManager $manager
    * @throws \Exception
    */
    public function load(ObjectManager $manager)
    {
       $maintenance = new Maintenance();
       $maintenance->setTitle("Website mode maintenance")
          ->setMessage("Website mode maintenance")
          ->setParagraph("sorry about this, please try again !")
          ->setTtl(new \DateTime('-2 month'))
          ->setIsLocked(false)
       ;
       for ($i = 1; $i <= 3; $i++) {
          $ip = new Ip();
          $ip->setIp('127.0.0.'.$i)
             ->setMaintenance($maintenance);
          $manager->persist($ip);
       }
       $manager->persist($maintenance);

       $faker = Factory::create('en_US');

       $garantieBlock = new GarantiesBlock();
       $garantieBlock->setTitle("Our garanties")
         ->setIntroduction($faker->paragraph(3))
         ->setContent($faker->paragraph(5))
         ->setLang('en')
       ;
       $manager->persist($garantieBlock);

       $garantieBlock = new GarantiesBlock();
       $garantieBlock->setTitle("Nos garanties")
          ->setIntroduction($faker->paragraph(3))
          ->setContent($faker->paragraph(5))
         ->setLang('fr');
       $manager->persist($garantieBlock);

       $manager->flush();
    }
}
