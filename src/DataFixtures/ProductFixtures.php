<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    // load Product
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setLabel($faker->word);
            for ($j = 0; $j < 20; $j++) {
                $subscriptionReference = $this->getReference('subscription_'.$j);
                $product->setSubscription($subscriptionReference);
            }
            $manager->persist($product);
        }
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            SubscriptionFixtures::class,
        ];
    }
}
