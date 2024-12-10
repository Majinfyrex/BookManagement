<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour gérer l'inscription des utilisateurs
class RegistrationController extends AbstractController
{
    // Route pour afficher et traiter le formulaire d'inscription
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // Instanciation d'un nouvel utilisateur
        $form = $this->createForm(RegistrationFormType::class, $user); // Création du formulaire d'inscription
        $form->handleRequest($request); // Traitement des données soumises

        // Assigne le rôle "ROLE_USER" par défaut à l'utilisateur
        $user->setRoles(['ROLE_USER']);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData(); // Récupère le mot de passe brut du formulaire

            // Hachage du mot de passe avant de le stocker dans la base de données
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user); // Prépare l'utilisateur pour la persistance
            $entityManager->flush(); // Enregistre l'utilisateur dans la base de données

            // TODO : Ajouter ici d'autres actions, comme l'envoi d'un e-mail de confirmation

            // Redirection vers la page de connexion après une inscription réussie
            return $this->redirectToRoute('app_login');
        }

        // Rend la vue du formulaire d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form, // Passe le formulaire à la vue
        ]);
    }
}