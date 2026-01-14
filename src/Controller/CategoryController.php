<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategorySuggestion;
use App\Entity\User;
use App\Form\CategoryFormType;
use App\Form\CategorySuggestionType;
use App\Repository\CategoryRepository;
use App\Repository\CategorySuggestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class CategoryController extends AbstractController
{
    // Liste des catégories
    #[Route('/categories', name: 'category_list')]
    public function list(Request $request, CategoryRepository $categoryRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 3; // 3 catégories par page
        $offset = ($page - 1) * $limit;
        
        $totalCategories = $categoryRepository->count([]);
        $categories = $categoryRepository->findBy([], ['name' => 'ASC'], $limit, $offset);
        $totalPages = ceil($totalCategories / $limit);

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    // Affichage d'une catégorie et ses articles
    #[Route('/category/{id}', name: 'category_show', requirements: ['id' => '\d+'])]
    public function show(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas.');
        }

        $posts = $category->getPosts();

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    // Création d'une nouvelle catégorie
    #[Route('/category/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Restriction d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès.');

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'une catégorie
    #[Route('/category/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(
        Category $category, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        // Restriction d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Vérifier le token CSRF
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $category->getId(), $token))) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // Vérifier si la catégorie a des articles associés
        if (!$category->getPosts()->isEmpty()) {
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer une catégorie contenant des articles.');
            return $this->redirectToRoute('category_list');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');

        return $this->redirectToRoute('category_list');
    }

    // Suggestion de nouvelle catégorie par les utilisateurs
    #[Route('/category/suggest', name: 'category_suggest')]
    public function suggest(Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est connecté et approuvé
        $user = $this->getUser();
        if (!$user instanceof User || !$user->getIsAccepted()) {
            $this->addFlash('error', 'Vous devez être connecté et approuvé pour suggérer une catégorie.');
            return $this->redirectToRoute('category_list');
        }

        $suggestion = new CategorySuggestion();
        $suggestion->setSuggestedBy($user);

        $form = $this->createForm(CategorySuggestionType::class, $suggestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($suggestion);
            $em->flush();

            $this->addFlash('success', 'Votre suggestion de catégorie a été envoyée. Elle sera examinée par les administrateurs.');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/suggest.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Liste des suggestions pour les admins
    #[Route('/admin/category/suggestions', name: 'admin_category_suggestions')]
    public function adminSuggestions(CategorySuggestionRepository $suggestionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pendingSuggestions = $suggestionRepository->findPendingSuggestions();

        return $this->render('admin/category_suggestions.html.twig', [
            'suggestions' => $pendingSuggestions
        ]);
    }

    // Approuver une suggestion de catégorie
    #[Route('/admin/category/suggestion/{id}/approve', name: 'admin_category_suggestion_approve')]
    public function approveSuggestion(CategorySuggestion $suggestion, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Créer la nouvelle catégorie
        $category = new Category();
        $category->setName($suggestion->getName());
        $category->setDescription($suggestion->getDescription());

        $em->persist($category);

        // Marquer la suggestion comme approuvée
        $suggestion->setStatus('approved');
        $suggestion->setReviewedBy($this->getUser());
        $suggestion->setReviewedAt(new \DateTime());

        $em->flush();

        $this->addFlash('success', 'La suggestion de catégorie a été approuvée et la catégorie créée.');
        return $this->redirectToRoute('admin_category_suggestions');
    }

    // Rejeter une suggestion de catégorie
    #[Route('/admin/category/suggestion/{id}/reject', name: 'admin_category_suggestion_reject')]
    public function rejectSuggestion(CategorySuggestion $suggestion, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $suggestion->setStatus('rejected');
        $suggestion->setReviewedBy($this->getUser());
        $suggestion->setReviewedAt(new \DateTime());

        $em->flush();

        $this->addFlash('info', 'La suggestion de catégorie a été rejetée.');
        return $this->redirectToRoute('admin_category_suggestions');
    }
}