<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Définition du contrôleur BookController avec le préfixe '/book' pour les routes
#[Route('/book')]
final class BookController extends AbstractController
{
    // Route pour afficher tous les livres
    #[Route(name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle 'ROLE_USER'
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Rend la vue avec la liste des livres
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(), // Récupère tous les livres depuis le repository
        ]);
    }

    // Route pour ajouter un nouveau livre
    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $book = new Book(); // Création d'un nouvel objet Book
        $form = $this->createForm(BookType::class, $book); // Création du formulaire associé à Book
        $form->handleRequest($request); // Traitement de la requête

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde de l'entité dans la base de données
            $entityManager->persist($book);
            $entityManager->flush();

            // Redirection vers la liste des livres après l'ajout
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire pour créer un nouveau livre
        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    // Route pour afficher les détails d'un livre spécifique
    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); // Vérifie l'accès utilisateur

        // Rend la vue avec les détails du livre
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    // Route pour éditer un livre existant
    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $form = $this->createForm(BookType::class, $book); // Création du formulaire d'édition
        $form->handleRequest($request); // Traitement de la requête

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Met à jour l'entité dans la base de données

            // Redirection après l'édition
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire d'édition
        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    // Route pour supprimer un livre
    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Vérifie la validité du token CSRF
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book); // Supprime le livre
            $entityManager->flush(); // Enregistre les modifications
        }

        // Redirection après la suppression
        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }

    // Route pour retourner un livre emprunté
    #[Route('/book/{id}/return', name: 'app_book_return', methods: ['GET', 'POST'])]
    public function returnBook(Book $book, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser(); // Récupère l'utilisateur courant

        // Trouve l'emprunt correspondant à l'utilisateur et au livre
        $borrow = $book->getBorrowed()->filter(function ($b) use ($user) {
            return $b->getUser()->getId() === $user->getId();
        })->first();

        if ($borrow) {
            // Supprime l'emprunt ou le marque comme retourné
            $book->removeBorrowed($borrow);
            $entityManager->remove($borrow);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Le livre a été retourné avec succès.');
        } else {
            // Message d'erreur si l'action n'est pas autorisée
            $this->addFlash('error', 'Action non autorisée.');
        }

        // Redirection après le retour
        return $this->redirectToRoute('app_book_index');
    }
}