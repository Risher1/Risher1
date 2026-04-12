<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SendEmailMessage;



final class HelloWorldController extends AbstractController
{
    #[Route('/hello/world', name: 'app_hello_world')]
    public function index(): Response
    {
    $nom = "Arisrisher";  
    $strName = "John";
    $strFirstName = "joe";
    $objUser = new User();
    $objUser->setUsername($nom);
    //$myName = $objUser->getName();

        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'HelloWorldController',
            'name'            =>  $strName,
            'firstName'       =>  $strFirstName,
            'User'             =>  $objUser,
        ]);
    }
     #[Route('/send-email', name: 'app_send_email')]
    public function sendEmail(MessageBusInterface $bus): Response
    {

        $emailMessage = new SendEmailMessage(
            to: 'hello@example.com',
            subject: 'Test Email',
            content: 'Bonjour, ceci est un email de test.',
        );
        $bus->dispatch($emailMessage);

        $this->addFlash('success', 'Email envoyé avec succès !');

        return $this->redirectToRoute('app_task');
    }
}


