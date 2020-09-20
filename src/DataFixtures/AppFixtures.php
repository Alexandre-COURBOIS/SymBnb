<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encod)
    {

        $this->encoder = $encod;

    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $users = [];
        $gender = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++) {

            $user = new User();

            $genre = $faker->randomElement($gender);

            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = rand(0, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $pwd1 = "abcdefghijklmnopqrstuvwxyz";
            $longueur = strlen($pwd1);
            $hash = $pwd1[rand(0, $longueur - 1)];

            $hash = $this->encoder->encodePassword($user, $hash);


            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPicture($picture)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setPassword($hash);

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 1; $i <= 30; $i++) {

            $ad = new Annonce();
            $title = $faker->sentence();
            $image = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';


            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($image)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(25, 150))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

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
