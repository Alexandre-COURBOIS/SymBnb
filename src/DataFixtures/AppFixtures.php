<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {


        $faker = Factory::create('FR-fr');

        for ($i = 1; $i <= 30; $i++) {

            $ad = new Annonce();
            $title = $faker->sentence();
            $image = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';


            $ad->setTitle($title)
                ->setCoverImage($image)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(25, 150))
                ->setRooms(mt_rand(1, 5));

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence)
                      ->setAd($ad);

                $manager->persist($image);

            }
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
