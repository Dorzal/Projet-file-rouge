<?php


namespace App\DataFixtures;


use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StockFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i <= 60 ; $i++){
            $stock = new Stock();
            $stock->setArticleId($i);
            $stock->setQty(10);
            $manager->persist($stock);
        }
        $manager->flush();
    }
}