<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Entity\Reservation;
use App\Entity\Role;
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

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Alexandre')
            ->setLastName('Courbois')
            ->setEmail('courbois.alexandre76440@gmail.com')
            ->setPassword($this->encoder->encodePassword($adminUser, 'michel'))
            ->setPicture('https://media-exp1.licdn.com/dms/image/C5603AQF06PGh-OOpLA/profile-displayphoto-shrink_200_200/0?e=1604534400&v=beta&t=BMNnX29sfkNr4LneP3Vb0bK3Y55JkokdZTGZpAwiHhc')
            ->setIntroduction($faker->sentence)
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);


        $users = [];
        $gender = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++) {

            $user = new User();

            $genre = $faker->randomElement($gender);

            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = rand(0, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

//            $pwd1 = "abcdefghijklmnopqrstuvwxyz";
//            $longueur = strlen($pwd1);
//            $hash = $pwd1[rand(0, $longueur - 1)];

            $hash = "password";
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

            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $reservation = new Reservation();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = mt_rand(3, 10);
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];
                $comment = $faker->paragraph();
                $val = rand(0, 3);

                $reservation->setBooker($booker)
                    ->setAnnonce($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount);
                    if ($val == 2) {
                        $reservation->setCommentaire($comment);
                    }


                $manager->persist($reservation);

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
