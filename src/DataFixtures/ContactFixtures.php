<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContactFixtures extends Fixture
{
    // load contact
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

// Generate 20 contacts
        for ($i = 0; $i < 20; $i++) {
            $contact = new Contact();
            $contact->setName($faker->name());
            $contact->setFirstname($faker->firstName());
            
            $manager->persist($contact);
            $this->addReference('contact_'.$i, $contact);
        }
        
        $manager->flush();
    }
}
