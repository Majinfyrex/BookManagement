<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Form\BorrowType;
use App\Repository\BorrowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour gérer les emprunts, avec un préfixe '/borrow' pour toutes ses routes
#[Route('/borrow')]
final class BorrowController extends AbstractController
{
    // Route pour afficher tous les emprunts
    #[Route(name: 'app_borrow_index', methods: ['GET'])]
    public function index(BorrowRepository $borrowRepository): Response
    {
        // Affiche la vue avec la liste des emprunts
        return $this->render('borrow/index.html.twig', [
            'borrows' => $borrowRepository->findAll(), // Récupère tous les emprunts
        ]);
    }

    // Route pour créer un nouvel emprunt
    #[Route('/new', name: 'app_borrow_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $borrow = new Borrow(); // Instanciation d'un nouvel emprunt
        $form = $this->createForm(BorrowType::class, $borrow); // Création du formulaire d'emprunt
        $form->handleRequest($request); // Traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist l'emprunt dans la base de données
            $entityManager->persist($borrow);
            $entityManager->flush();

            // Redirection après la création de l'emprunt
            return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour créer un nouvel emprunt
        return $this->render('borrow/new.html.twig', [
            'borrow' => $borrow,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour afficher les détails d'un emprunt spécifique
    #[Route('/{id}', name: 'app_borrow_show', methods: ['GET'])]
    public function show(Borrow $borrow): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Affiche la vue avec les détails de l'emprunt
        return $this->render('borrow/show.html.twig', [
            'borrow' => $borrow,
        ]);
    }

    // Route pour éditer un emprunt
    #[Route('/{id}/edit', name: 'app_borrow_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Borrow $borrow, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        $form = $this->createForm(BorrowType::class, $borrow); // Création du formulaire d'édition
        $form->handleRequest($request); // Traitement des données soumises

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Met à jour l'emprunt dans la base de données

            // Redirection après la modification
            return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche la vue pour modifier un emprunt
        return $this->render('borrow/edit.html.twig', [
            'borrow' => $borrow,
            'form' => $form, // Passe le formulaire à la vue
        ]);
    }

    // Route pour supprimer un emprunt
    #[Route('/{id}', name: 'app_borrow_delete', methods: ['POST'])]
    public function delete(Request $request, Borrow $borrow, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès réservé aux administrateurs

        // Vérifie si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete' . $borrow->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($borrow); // Supprime l'emprunt
            $entityManager->flush(); // Enregistre les modifications
        }

        // Redirection après suppression
        return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
    }

    // Méthode pour emprunter un livre
    public function borrow(Book $book, BorrowRepository $borrowRepository, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifie si le livre est déjà emprunté
        $activeBorrow = $borrowRepository->findActiveBorrowForBook($book);

        if ($activeBorrow) {
            $this->addFlash('error', 'Ce livre est déjà emprunté.');
            return $this->redirectToRoute('app_borrow_index');
        }

        $borrow = new Borrow(); // Création d'un nouvel emprunt
        $borrow->setBook($book); // Associe le livre à l'emprunt
        $borrow->setUser($security->getUser()); // Associe l'utilisateur à l'emprunt
        $borrow->setBorrowDate(new \DateTime()); // Définit la date d'emprunt
        $borrow->setStatus('borrowed'); // Définit le statut de l'emprunt

        $book->setAvailable(false); // Marque le livre comme non disponible

        $entityManager->persist($borrow);
        $entityManager->persist($book);
        $entityManager->flush();

        $this->addFlash('success', 'Livre emprunté avec succès.');
        return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
    }

    // Méthode pour retourner un livre emprunté
    public function returnBook(Borrow $borrow, EntityManagerInterface $entityManager): Response
    {
        $borrow->setReturnedAt(new \DateTime()); // Définit la date de retour

        $entityManager->flush(); // Sauvegarde les modifications

        $this->addFlash('success', 'Le livre a été retourné.');

        return $this->redirectToRoute('app_book_index');
    }
}
