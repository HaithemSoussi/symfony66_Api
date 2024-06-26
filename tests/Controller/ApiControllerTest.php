<?php
// tests/Controller/ApiControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class ApiControllerTest extends WebTestCase
{
    private $entityManager;
    
    protected function setUp(): void
    {
        // Create a client to access the container
        $client = static::createClient();
        // Access the service container
        $container = $client->getContainer();
        // Get the entity manager
        $this->entityManager = $container->get('doctrine')->getManager();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        // Clear the database after each test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Subscription s')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Contact c')->execute();
        // Close the EntityManager and avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
    
    public function testCreateSubscription(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/subscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'beginDate' => '2024-07-01 00:00:00',
            'endDate' => '2024-07-26 00:00:00',
            'contact' => [
                'name' => 'Jane',
                'firstname' => 'Doe',
            ],
        ]));
        
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
    
    public function testUpdateSubscription(): void
    {
        $client = static::createClient();

        // Create a new subscription
        $client->request('POST', '/api/subscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'beginDate' => '2024-07-01 00:00:00',
            'endDate' => '2024-07-26 00:00:00',
            'contact' => [
                'name' => 'Jane',
                'firstname' => 'Doe',
            ],
        ]));
        
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $subscriptionData = json_decode($response->getContent(), true);
        $subscriptionId = $subscriptionData['id'];

        // Update the subscription
        $client->request('PUT', '/api/subscription/'.$subscriptionId, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'beginDate' => '2024-07-01 00:00:00',
                'endDate' => '2024-07-26 00:00:00',
                'contact' => [
                    'id' => $subscriptionData['contact']['id'],
                    'name' => 'John',
                    'firstname' => 'Doe',
                ],
            ]));
        
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
    
    public function testDeleteSubscription(): void
    {
        $client = static::createClient();

        // Create a new subscription
        $client->request('POST', '/api/subscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'beginDate' => '2024-07-01 00:00:00',
            'endDate' => '2024-07-26 00:00:00',
            'contact' => [
                'name' => 'Jane',
                'firstname' => 'Doe',
            ],
        ]));
        
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $subscriptionData = json_decode($response->getContent(), true);
        $subscriptionId = $subscriptionData['id'];

        // Delete the subscription
        $client->request('DELETE', '/api/subscription/'.$subscriptionId);
        
        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}
