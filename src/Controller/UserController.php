<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour gérer les utilisateurs, avec un préfixe '/user' pour toutes ses routes
#[Route('/user')]
final class UserController extends AbstractController
{
    // Route pour afficher tous les utilisateurs
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Rend la vue avec la liste des utilisateurs
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(), // Récupère tous les utilisateurs depuis le repository
        ]);
    }

    // Route pour créer un nouvel utilisateur
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $user = new User(); // Instanciation d'un nouvel utilisateur
        $form = $this->createForm(UserType::class, $user); // Création du formulaire
        $form->handleRequest($request); // Traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user); // Prépare l'utilisateur pour la persistance
            $entityManager->flush(); // Enregistre l'utilisateur dans la base de données

            // Redirection vers la liste des utilisateurs après création
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour créer un nouvel utilisateur
        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour afficher les détails d'un utilisateur spécifique
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Affiche la vue avec les détails de l'utilisateur
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    // Route pour modifier un utilisateur
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $form = $this->createForm(UserType::class, $user); // Création du formulaire d'édition
        $form->handleRequest($request); // Traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Met à jour l'utilisateur dans la base de données

            // Redirection après la modification
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour modifier un utilisateur
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour supprimer un utilisateur
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Vérifie si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user); // Supprime l'utilisateur
            $entityManager->flush(); // Enregistre les modifications
        }

        // Redirection après suppression
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}