<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    // load Subscription
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $subscription = new Subscription();
            $subscription->setBeginDate($faker->dateTime());
            $subscription->setEndDate($faker->dateTimeBetween('+3 week', '+1 month'));
            
            $contactReference = $this->getReference('contact_'.($i));
            $subscription->setContact($contactReference);
            $manager->persist($subscription);
            
            $this->addReference('subscription_'.$i, $subscription);
        }
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            ContactFixtures::class,
        ];
    }
}