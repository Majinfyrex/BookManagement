<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour gérer les auteurs, avec un préfixe '/author' pour toutes ses routes
#[Route('/author')]
final class AuthorController extends AbstractController
{
    // Route pour afficher tous les auteurs
    #[Route(name: 'app_author_index', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository): Response
    {
        // Vérifie si l'utilisateur connecté a le rôle 'ROLE_USER'
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Affiche la vue avec la liste des auteurs
        return $this->render('author/index.html.twig', [
            'authors' => $authorRepository->findAll(), // Récupère tous les auteurs depuis le repository
        ]);
    }

    // Route pour créer un nouvel auteur
    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $author = new Author(); // Instanciation d'un nouvel auteur
        $form = $this->createForm(AuthorType::class, $author); // Création du formulaire
        $form->handleRequest($request); // Récupération et traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist l'entité dans la base de données
            $entityManager->persist($author);
            $entityManager->flush();

            // Redirection vers la liste des auteurs après création
            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour créer un nouvel auteur
        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour afficher un auteur spécifique
    #[Route('/{id}', name: 'app_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); // Vérifie l'accès utilisateur

        // Affiche la vue avec les détails de l'auteur
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    // Route pour éditer un auteur
    #[Route('/{id}/edit', name: 'app_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $form = $this->createForm(AuthorType::class, $author); // Création du formulaire d'édition
        $form->handleRequest($request); // Traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Met à jour l'auteur dans la base de données

            // Redirection après la modification
            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour modifier un auteur
        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour supprimer un auteur
    #[Route('/{id}', name: 'app_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Vérifie si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete' . $author->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($author); // Supprime l'auteur
            $entityManager->flush(); // Enregistre les modifications
        }

        // Redirection après suppression
        return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
    }
}