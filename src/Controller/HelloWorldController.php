<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class HelloWorldController extends AbstractController
{
    #[Route('/hello/world', name: 'app_hello_world')]
    public function index(): Response
    {
    $nom = "Arisrisher";  
    $strName = "John";
    $strFirstName = "joe";
    $objUser = new Utilisateur();
    $objUser->setNom($nom);
    //$myName = $objUser->getNom();

        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'HelloWorldController',
            'name'            =>  $strName,
            'firstName'       =>  $strFirstName,
            'Utilisateur'             =>  $objUser,
        ]);
    }
   
}
