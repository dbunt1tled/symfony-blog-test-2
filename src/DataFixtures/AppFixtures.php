<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class AppFixtures extends Fixture
{
    private $faker;
    private $passwordEncoder;

    private $userReferences = [];
    private $postReferences = [];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
        $this->faker = Factory::create('en_US');
    }

    public function load(ObjectManager $manager): void
    {
        $countUser = random_int(5, 10);
        $countPosts = random_int($countUser, $countUser * 3);
        $countComments = random_int($countPosts, $countPosts * 3);

        $this->loadUser($manager, $countPosts);
        $manager->flush();
        $this->loadPost($manager, $countPosts);
        $manager->flush();
        $this->loadComment($manager, $countComments);
        $manager->flush();
    }
    public function loadUser(ObjectManager $manager, int $count = 5): void
    {
        if ($count < 1) {
            return;
        }
        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $user->setName($this->faker->unique()->firstName . ' ' .$this->faker->unique()->lastName)
                ->setEmail($this->faker->unique()->email)
                ->setPlainPassword('12345678')
                ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()))
                ->setRoles([User::ROLE_USER]);
            $manager->persist($user);
            $this->addReference($user->getUsername(),$user);
            $this->userReferences[] = $user->getUsername();

        }
    }
    public function loadPost(ObjectManager $manager, int $count = 5): void
    {
        if ($count < 1) {
           return;
        }
        for ($i = 0; $i < $count; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->unique()->sentence(3))
                ->setSlug($this->faker->unique()->slug)
                ->setContent($this->faker->unique()->text(600))
                ->setPublished(\DateTimeImmutable::createFromMutable($this->faker->unique()->dateTimeThisYear))
                ->setAuthor($this->getReference($this->userReferences[array_rand($this->userReferences)]));
            $manager->persist($blogPost);
            $this->addReference($blogPost->getSlug(),$blogPost);
            $this->postReferences[] = $blogPost->getSlug();

        }
    }

    public function loadComment(ObjectManager $manager, int $count = 5): void
    {
        if ($count < 1) {
            return;
        }
        for ($i = 0; $i < $count; $i++) {
            $comment = new Comment();
            $comment->setAuthor($this->getReference($this->userReferences[array_rand($this->userReferences)]))
                ->setContent($this->faker->unique()->text(600))
                ->setPublished(\DateTimeImmutable::createFromMutable($this->faker->unique()->dateTimeThisYear))
                ->setBlogPost($this->getReference($this->postReferences[array_rand($this->postReferences)]));
            $manager->persist($comment);
        }
    }
}
