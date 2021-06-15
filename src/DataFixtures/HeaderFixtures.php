<?php

namespace App\DataFixtures;

use App\Entity\Header;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class HeaderFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');
        $pages = ["projects","documents"];
        $langs = ['fr','en'];
        foreach ($pages as $page) {
            $title = $faker->sentence();
            $description = $faker->paragraph();

            if ($page == "projects") {
                $title = "Trouver les meilleures<br>Opportunités D'investissement";
                $description = "Rejoignez notre réseau de startups et investisseurs.";
            } else if ($page == "documents") {
                $title = "Votre meilleur<br>source d'informations<br>à haute valeur ajoutée";
            }
            foreach ($langs as $lang) {
                $header = new Header();
                $header->setPage($page)
                    ->setLang($lang)
                    ->setDescription("<h1>" . $title . "</h1><br><p>" . $description. "</p>");
                $manager->persist($header);
            }
        }

        $manager->flush();
    }
}
