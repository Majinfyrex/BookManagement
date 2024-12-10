<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Définition du contrôleur HomeController
class HomeController extends AbstractController
{
    // Route pour la page d'accueil ('/')
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Rend la vue pour la page d'accueil
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', // Variable passée à la vue
        ]);
    }

    // Route pour le tableau de bord ('/dashboard')
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        // Rend la vue pour la page du tableau de bord
        return $this->render('home/dashboard.html.twig', [
            'controller_name' => 'HomeController', // Variable passée à la vue
        ]);
    }
}