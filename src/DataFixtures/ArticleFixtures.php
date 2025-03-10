<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Author;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $author1 = new Author();
        $author1->setName('John');
        $author1->setLastName('Doe');
        $manager->persist($author1);

        $article1 = new Article();
        $article1->setTitle('Article 1');
        $article1->setParagprah('lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum');
        $article1->setImagePath('https://i.ibb.co/HfV82WYy/pic01.jpg');
        $article1->setCoverPath('https://i.ibb.co/HfV82WYy/pic01.jpg');
        $article1->setAuthor($author1);

        $manager->persist($article1);

        $author2 = new Author();
        $author2->setName('John');
        $author2->setLastName('Pork');
        $manager->persist($author2);

        $article2 = new Article();
        $article2->setTitle('Article 2');
        $article2->setParagprah('lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum');
        $article2->setImagePath('https://i.ibb.co/TZWsWGc/pic02.jpg');
        $article2->setCoverPath('https://i.ibb.co/TZWsWGc/pic02.jpg');
        $article2->setAuthor($author2);

        $manager->persist($article2);

        $author3 = new Author();
        $author3->setName('Arthur');
        $author3->setLastName('Doel');
        $manager->persist($author3);

        $article3 = new Article();
        $article3->setTitle('Article 3');
        $article3->setParagprah('lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum');
        $article3->setImagePath('https://i.ibb.co/k6khCZZ7/pic03.jpg');
        $article3->setCoverPath('https://i.ibb.co/k6khCZZ7/pic03.jpg');
        $article3->setAuthor($author3);

        $manager->persist($article3);


        $manager->flush();
    }
}
