<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'app_api')]
class ApiController extends AbstractController
{
    // GET /subscription/{contact_id}
    #[Route('/subscription/{contact_id}', name: 'get_subscription' , methods:['get'])]
    public function getSubscription(ManagerRegistry $doctrine,SerializerInterface $serializer, int $contact_id): JsonResponse
    {
        $contact = $doctrine->getRepository(Contact::class)->find($contact_id);
        if (!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $subscription = $doctrine->getRepository(Subscription::class)
                                 ->findOneBy(['contact' => $contact ]);
        
        if (!$subscription) {
            return $this->json('No subscription found for id ', 404);
        }

        $data = $serializer->serialize($subscription, 'json');
        return new JsonResponse($data, 200, [], true);
    }
    
    // POST /subscription
    #[Route('/subscription', name: 'subscription' , methods:['post'])]
    public function create(EntityManagerInterface $em, serializerInterface $serializer, Request $request): Response
    {
        /*
        {
        "beginDate": "2023-06-01 00:00:00",
        "endDate": "2023-06-05 00:00:00",
            "contact": {
            "name": "John",
            "firstname": "Doe"
            }
        }
         */
        $data = $request->getContent();
        try {
            // Deserialize the JSON data into a Subscription object
            $subscription = $serializer->deserialize($data, Subscription::class, 'json', [
                'datetime_format' => 'Y-m-d H:i:s' // Adjust format based on your date string
            ]);
            
            // Extract contact data from the subscription
            $requestData = json_decode($data, true);
            if (!isset($requestData['contact'])) {
                return new JsonResponse(['error' => 'Contact data is missing'], 400);
            }
            
            // Deserialize the contact data
            $contactData = $requestData['contact'];
            $contact = $serializer->deserialize(json_encode($contactData), Contact::class, 'json');
            
            // Check if the contact already exists (optional, based on your requirements)
            if (isset($contactData['id'])) {
                $existingContact = $em->getRepository(Contact::class)->find($contactData['id']);
                if ($existingContact) {
                    $contact = $existingContact;
                }
            }
            
            // Set the contact for the subscription
            $subscription->setContact($contact);
            
            // Persist and flush the entities
            $em->persist($subscription);
            $em->flush();
            
            return $this->json('Subscription created', 201);
        } catch (NotNormalizableValueException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    // PUT /subscription/{id}
    #[Route('/subscription/{id}', name: 'subscription' , methods:['put'])]
    public function updateSubscription(int $id, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $data = $request->getContent();
        try {
            // Find the existing Subscription entity
            $subscription = $em->getRepository(Subscription::class)->find($id);
            if (!$subscription) {
                return new JsonResponse(['error' => 'Subscription not found'], 404);
            }
            
            // Deserialize the JSON data into the existing Subscription object
            $serializer->deserialize($data, Subscription::class, 'json', [
                'datetime_format' => 'Y-m-d H:i:s',
                'object_to_populate' => $subscription // Populate the existing entity
            ]);
            
            // Extract contact data from the subscription
            $requestData = json_decode($data, true);
            if (isset($requestData['contact'])) {
                // Deserialize the contact data
                $contactData = $requestData['contact'];
                $contact = $serializer->deserialize(json_encode($contactData), Contact::class, 'json');
                
                // Check if the contact already exists (optional, based on your requirements)
                if (isset($contactData['id'])) {
                    $existingContact = $em->getRepository(Contact::class)->find($contactData['id']);
                    if ($existingContact) {
                        $contact = $existingContact;
                    }
                }
                
                // Set the contact for the subscription
                $subscription->setContact($contact);
            }
            
            // Persist and flush the entities
            $em->persist($subscription);
            $em->flush();
            
            return $this->json('Subscription updated', 201);
        } catch (NotNormalizableValueException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    // DELETE /subscription/{id}
    #[Route('/subscription/{id}', name: 'subscription' , methods:['delete'])]
    public function deleteSubscription(int $id, EntityManagerInterface $em): Response
    {
        // Find the existing Subscription entity
        $subscription = $em->getRepository(Subscription::class)->find($id);
        if (!$subscription) {
            return new JsonResponse(['error' => 'Subscription not found'], 404);
        }
        
        // Remove the Subscription entity
        $em->remove($subscription);
        $em->flush();
        
        return new JsonResponse(['success'=>'Subscription deleted'], 204);
    }
    
    
    
    
    
    
    
    
    
}
