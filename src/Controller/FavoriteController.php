<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Post;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    #[Route('/state/{id}', name: 'favorite_state', methods: ['GET'])]
    public function state(
        Post $post,
        FavoriteRepository $favoriteRepository
    ): JsonResponse {
        $isFavorite = false;
        $user = $this->getUser();
        
        if ($user) {
            /** @var \App\Entity\User $user */
            $isFavorite = $favoriteRepository->isFavorite($user, $post);
        }

        return new JsonResponse([
            'isFavorite' => $isFavorite,
            'count' => $favoriteRepository->countFavorites($post),
        ]);
    }

    #[Route('/toggle/{id}', name: 'favorite_toggle', methods: ['POST'])]
    public function toggle(
        Post $post,
        FavoriteRepository $favoriteRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Vérifie que l'utilisateur est connecté (ROLE_USER ou ROLE_ADMIN)
        if (!$this->getUser()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Vous devez être connecté'
            ], 401);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifier si le favori existe déjà
        $favorite = $favoriteRepository->findOneBy([
            'user' => $user,
            'post' => $post,
        ]);

        if ($favorite) {
            // Supprimer le favori
            $entityManager->remove($favorite);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'removed',
                'message' => 'Article retiré des favoris',
                'count' => $favoriteRepository->countFavorites($post),
            ]);
        } else {
            // Ajouter aux favoris
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setPost($post);

            $entityManager->persist($favorite);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'added',
                'message' => 'Article ajouté aux favoris',
                'count' => $favoriteRepository->countFavorites($post),
            ]);
        }
    }

    #[Route('/my-favorites', name: 'favorite_list')]
    public function list(FavoriteRepository $favoriteRepository): Response
    {
        // Vérifie que l'utilisateur est connecté (ROLE_USER ou ROLE_ADMIN)
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à vos favoris.');
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $favorites = $favoriteRepository->findUserFavoritePosts($user);
        
        // Extraire les posts des objets Favorite
        $favoritePosts = array_map(function($favorite) {
            return $favorite->getPost();
        }, $favorites);

        return $this->render('favorite/list.html.twig', [
            'favoritePosts' => $favoritePosts,
        ]);
    }
}
