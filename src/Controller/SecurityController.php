<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// Contrôleur pour gérer la connexion et la déconnexion des utilisateurs
class SecurityController extends AbstractController
{
    // Route pour la page de connexion
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifie si un utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard'); // Redirection vers le tableau de bord
        }

        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rend la vue du formulaire de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Passe le dernier nom d'utilisateur à la vue
            'error' => $error, // Passe l'erreur à la vue, s'il y en a une
        ]);
    }

    // Route pour la déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode est intentionnellement vide
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}