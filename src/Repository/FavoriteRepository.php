<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    /**
     * Vérifie si un article est en favori pour un utilisateur
     */
    public function isFavorite(User $user, Post $post): bool
    {
        $favorite = $this->findOneBy([
            'user' => $user,
            'post' => $post,
        ]);

        return $favorite !== null;
    }

    /**
     * Compte le nombre de favoris pour un article
     */
    public function countFavorites(Post $post): int
    {
        return $this->count(['post' => $post]);
    }

    /**
     * Récupère tous les favoris d'un utilisateur
     */
    public function findUserFavorites(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les articles favoris d'un utilisateur avec leurs détails
     */
    public function findUserFavoritePosts(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->select('f, p')
            ->join('f.post', 'p')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
