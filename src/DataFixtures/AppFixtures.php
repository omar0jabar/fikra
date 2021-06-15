<?php

namespace App\DataFixtures;

use App\Entity\Avantage;
use App\Entity\BusinessModel;
use App\Entity\City;
use App\Entity\ContactInfo;
use App\Entity\DocumentType;
use App\Entity\Earned;
use App\Entity\Partner;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\SalesChannels;
use App\Entity\Sector;
use App\Entity\Step;
use App\Entity\TeamMember;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
   private $encoder;

   public function __construct(UserPasswordEncoderInterface $encoder)
   {
      $this->encoder = $encoder;
   }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
   public function load(ObjectManager $manager)
   {
      $faker = Factory::create('en_US');

       $labels = ["Rabat", "Salé", "Casablanca", "Tanger", "Oujda", "Agadir", "Marrakech","Dakhla"];
       $cities = [];
       foreach ($labels as $label) {
           $city = new City();
           $city->setName($label);
           $cities[] = $city;
           $manager->persist($city);
       }

      $labels = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_STARTUPER'];
      $roles = [];
      foreach ($labels as $label) {
         $role = new Role();
         $role->setLabel($label)
            ->setCreatedAt($faker->dateTimeBetween('-6 month'));
         $roles[] = $role;
         $manager->persist($role);
      }

      $createdAt = $faker->dateTimeBetween('-6 month');
      // Set default admin account
      // Login : admin@admin.com
      // Password : admin
      $user = new User();
      $user->setFirstName($faker->firstName)
         ->setLastName($faker->lastName)
         ->setProfile('admin')
         ->setPhone($faker->phoneNumber)
         ->setEmail('admin@admin.com')
         ->setPassword($this->encoder->encodePassword($user, 'admin'))
         ->setAddress($faker->address)
         ->setAvatar('avatar2.jpg')
         ->setBirthday($faker->dateTimeBetween('-'.mt_rand(20, 40).' year'))
         ->setIsActive(1)
         ->setIsBanned(0)
         ->setRole($roles[0])
         ->setCreatedAt($createdAt)
         ->setUpdatedAt($createdAt);
      $manager->persist($user);

      $startupers = [];

      for ($i = 1; $i <= 20; $i++) {
         $user = new User();
         $user->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setProfile('startuper')
            ->setPhone($faker->phoneNumber);
         if ($i === 1) {
            $user->setEmail("startuper@startuper.com");
         } else {
            $user->setEmail("startuper" . $i . "@startuper.com");
         }
         $user->setPassword($this->encoder->encodePassword($user, 'startuper'))
            ->setAddress($faker->address)
            ->setAvatar('avatar'.mt_rand(1, 3).'.jpg')
            ->setBirthday($faker->dateTimeBetween('-'.mt_rand(20, 40).' year'))
            ->setIsActive(1)
            ->setIsBanned(0)
            ->setRole($roles[2]);

         $user->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt);
         $manager->persist($user);
         $startupers[] = $user;
      }

      $labelSteps = ["Idéation", "Création", "Amorçage", "Scaling", "Expansion"];
      $steps = [];
      foreach ($labelSteps as $label) {
         $step = new Step();
         $step->setLabelFr($label);
         $step->setLabelEn($label);
         $step->setHelpFr($label);
         $step->setHelpEn($label);
         $steps[] = $step;
         $manager->persist($step);
      }

      $LabelsEarneds = ["Le produit/service n'est pas encore sur le marché",
         "0-50000 MAD",
         "50000-500000 MAD",
         "500000-2000000 MAD",
         "+ de 2000000 MAD"];
      $earneds = [];
      foreach ($LabelsEarneds as $label) {
         $earned = new Earned();
         $earned->setLabelFr($label);
         $earned->setLabelEn($label);
         $earneds[] = $earned;
         $manager->persist($earned);
      }

      $labelSales = ["Vente directe (B2B, B2C..)",
         "E-commerce",
         "Réseaux sociaux",
         "Vente/livraison à domicile",
         "Catalogue/télé-achat"];
      $sales = [];
      foreach ($labelSales as $label) {
         $sale = new SalesChannels();
         $sale->setLabelFr($label);
         $sale->setLabelEn($label);
         $sales[] = $sale;
         $manager->persist($sale);
      }

      $labelSectors = ["Art", "Logistique", "Agriculture", "Technologie", "Restauration", "Science", "Internet", "Infrastructure"];
      $sectors = [];
      foreach ($labelSectors as $label) {
         $sector = new Sector();
         $sector->setLabelFr($label);
         $sector->setLabelEn($label);
         $sectors[] = $sector;
         $manager->persist($sector);
      }

      $labelBusiness = ["Vente directe", "Abonnement", "Saas", "Freemium", "Commision"];
      $businessModels = [];
      foreach ($labelBusiness as $label) {
         $business = new BusinessModel();
         $business->setLabelFr($label);
         $business->setLabelEn($label);
         $businessModels[] = $business;
         $manager->persist($business);
      }

      $labelDocumentTypes = ["Executive summary",
         "Certificat négatif",
         "Attestation de reference",
         "Projections financières"];
      $documentTypes = [];
      foreach ($labelDocumentTypes as $label) {
         $type = new DocumentType();
         $type->setLabelFr($label);
         $type->setLabelEn($label);
         $documentTypes[] = $type;
         $manager->persist($type);
      }

      $bools = [true, false];
      $languages = ['fr', 'en'];

      for ($k = 1; $k <= 50; $k++) {
         $project = new Project();
         $project->setName($faker->sentence(mt_rand(2, 3)))
             ->setLanguage($languages[mt_rand(0, 1)])
            ->setDescription($faker->paragraph)
            ->setStep($steps[mt_rand(0, count($steps) -1)])
            ->setEarned($earneds[mt_rand(0, count($earneds) - 1)])
            //->setMainProducts($faker->word.', '.$faker->word.', '.$faker->word)
            ->setStartup(true)
            ->setDenomination($faker->sentence)
            ->setCreatingDate($faker->dateTimeBetween('-10 year'))
            ->setRc(substr($faker->creditCardNumber, 0, 10))
            ->setIsVerified(mt_rand(0, 1));

         for ($j = 1; $j < mt_rand(2, 5); $j++) {
            $project->addSalesChannel($sales[mt_rand(0, count($sales) - 1)]);
         }
         for ($j = 1; $j < mt_rand(2, 5); $j++) {
            $project->addSector($sectors[mt_rand(0, count($sectors) - 1)]);
         }
         for ($j = 1; $j < mt_rand(2, 5); $j++) {
            $project->addBusinessModel($businessModels[mt_rand(0, count($businessModels) - 1)]);
         }

         for ($j = 1; $j < mt_rand(2, 5); $j++) {
            $advantage = new Avantage();
             $advantage->setName($faker->sentence(3))
               ->setProject($project);
            $manager->persist($advantage);
         }

         $project->setMorocco(true)
            ->setOtherCountry($bools[mt_rand(0, 1)])
            ->setMarketResearch($bools[mt_rand(0, 1)]);

         $budget = mt_rand(50000, 2000000);
         $raised = mt_rand(10000, $budget);
         $amount = $budget - $raised;

         $project->setBudget($budget)
            ->setRaised($raised)
            ->setAmount($amount)
            ->setSummary($faker->paragraph)
            ->setExpress($faker->paragraph);

         for ($m = 1; $m < mt_rand(2, 5); $m++) {
            $member = new TeamMember();
            $member->setFirstName($faker->firstName)
               ->setLastName($faker->lastName)
               ->setPosition($faker->jobTitle)
               ->setProject($project);
            if ($m === 1) {
               $member->setPorteur(true);
            }
            $manager->persist($member);
         }

         if ($k > 47) {
            $project->setAuthor($startupers[0]);
         } else {
            $project->setAuthor($startupers[mt_rand(1, count($startupers) -1)]);
         }
         $project->setStepCreating(7)
            ->setIsDraft(false);
         $manager->persist($project);

      }

      $infos = [
          'email' => 'contact@pfestartup.com',
          'phone' => '+212635102030',
          'address' => 'LUS Kenitra Maroc',
          'facebook' => 'https://www.facebook.com/',
          'linkedin' => 'https://www.linkedin.com/',
          'youtube' => 'https://www.youtube.com/',
          'twitter' => 'https://twitter.com/',
      ];
      foreach ($infos as $key => $value) {
          $info = new ContactInfo();
          $info->setTitle($key);
          $info->setInfo($value);
          $manager->persist($info);
      }

   $manager->flush();
  } 
}
