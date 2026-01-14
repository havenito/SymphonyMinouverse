<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\FavoriteRepository;


class PostController extends AbstractController
{
    #[Route('/post/create', name: 'post_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à créer des articles.');
        }
    
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('picture')->getData();
            if ($uploadedFile) {
                $uploadDir = $this->getParameter('uploads_directory');
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
    
                try {
                    $uploadedFile->move($uploadDir, $newFilename);
                    $post->setPicture($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('post_create');
                }
            }

            $post->setUser($this->getUser());

            if (!$post->getPublishedAt()) {
                $post->setPublishedAt(new \DateTime()); 
            }

            $entityManager->persist($post);
            $entityManager->flush();
    
            $this->addFlash('success', 'L\'article a été créé avec succès.');

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }
    
        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/posts', name: 'post_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $limit = 3;

        $page = max(1, $request->query->getInt('page', 1));

        $offset = ($page - 1) * $limit;

        $posts = $entityManager->getRepository(Post::class)
            ->findBy([], ['publishedAt' => 'DESC'], $limit, $offset);

        $totalPosts = $entityManager->getRepository(Post::class)->count([]);

        $totalPages = ceil($totalPosts / $limit);

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/post/{id}', name: 'post_show',)]
    public function show(Request $request, EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository, ?Post $post): Response
    {
        if (!$post) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        // Get favorite data
        $isFavorite = false;
        $user = $this->getUser();
        if ($user) {
            $isFavorite = $favoriteRepository->isFavorite($user, $post);
        }
        $favoriteCount = $favoriteRepository->countFavorites($post);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
    
        $replyForms = [];

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTime());
    
            $entityManager->persist($comment);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        if ($request->isMethod('POST') && isset($request->request->get('reply')['content'])) {
            $replyContent = $request->request->get('reply')['content'];
            $commentId = $request->request->get('reply')['commentId'];
        
            $comment = $entityManager->getRepository(Comment::class)->find($commentId);
        
            if ($comment && $replyContent) {
                $reply = new Comment();
                $reply->setContent($replyContent);
                $reply->setUser($this->getUser());
                $reply->setCreatedAt(new \DateTime());
                $reply->setPost($post);
                $reply->setParentComment($comment);  
        
                $entityManager->persist($reply);
                $entityManager->flush();

                $this->addFlash('success', 'Votre réponse a été ajoutée.');
            }

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        // Filtrer les commentaires selon le rôle de l'utilisateur
        $comments = [];
        if ($this->isGranted('ROLE_ADMIN')) {
            // Les admins voient tous les commentaires
            $comments = $post->getComments()->toArray();
        } else {
            // Les utilisateurs normaux ne voient que les commentaires validés
            foreach ($post->getComments() as $comment) {
                if ($comment->getStatus() === 'validé' || $comment->getStatus() === null) {
                    $comments[] = $comment;
                }
            }
        }

        foreach ($comments as $comment) {
            $replyForm = $this->createForm(CommentType::class, new Comment());
            $replyForms[$comment->getId()] = $replyForm->createView(); 
        }
    
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'replyForms' => $replyForms,
            'isFavorite' => $isFavorite,
            'favoriteCount' => $favoriteCount,
        ]);
    }

    // Route pour modifier un article (admin uniquement)
#[Route('/post/edit/{id}', name: 'post_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, Post $post): Response
{

    if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $post->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet article.');
    }

    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $uploadedFile = $form->get('picture')->getData();

        if ($uploadedFile instanceof UploadedFile) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = $uploadedFile->guessExtension();
            if (!in_array($extension, $allowedExtensions)) {
                $this->addFlash('error', 'Le type de fichier n\'est pas autorisé.');
                return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
            }

            $uploadDir = $this->getParameter('uploads_directory');
            $oldFilename = $post->getPicture();
            if ($oldFilename && file_exists($uploadDir . '/' . $oldFilename)) {
                unlink($uploadDir . '/' . $oldFilename);
            }

            $newFilename = uniqid() . '.' . $extension;
            try {
                $uploadedFile->move($uploadDir, $newFilename);
                $post->setPicture($newFilename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'L\'article a été mis à jour avec succès.');
        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    return $this->render('post/edit.html.twig', [
        'form' => $form->createView(),
        'post' => $post,
    ]);
}

    #[Route('/admin/post/{id}/delete', name: 'post_delete')]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Article supprimé avec succès.');
        return $this->redirectToRoute('post_list');
    }
}