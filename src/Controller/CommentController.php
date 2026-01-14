<?php

namespace App\Controller;

use App\Entity\User; 
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\CommentReport;
use App\Form\CommentType;
use App\Form\CommentReportType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\CommentReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommentController extends AbstractController
{
    private $entityManager;
    private $commentRepository;
    private $postRepository;
    private $commentReportRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        PostRepository $postRepository,
        CommentReportRepository $commentReportRepository
    ) {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->commentReportRepository = $commentReportRepository;
    }

    // Afficher les commentaires d'un article
    #[Route('/post/{postId}/comments', name: 'post_comments')]
    public function showComments(int $postId): Response
    {
        // Récupérer l'article
        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $comments = $this->commentRepository->findBy(['post' => $postId]);
        } else {
            $comments = $this->commentRepository->findBy(['post' => $postId, 'status' => 'validé']);
        }

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'post' => $post,
        ]);
    }

    // Ajouter un commentaire
    #[Route('/post/{postId}/comment', name: 'post_add_comment', methods: ['POST'])]
    public function addComment(int $postId, Request $request): Response
    {
        $user = $this->getUser();
    
        if (!$user || in_array('ROLE_VISITOR', $user->getRoles())) {
            $this->addFlash('error', 'Votre compte doit être validé pour commenter.');
            return $this->redirectToRoute('post_show', ['id' => $postId]);
        }

        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setUser($user);
        $comment->setCreatedAt(new \DateTime());
        $comment->setStatus('en attente'); 

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Votre commentaire est en attente de validation.');
            return $this->redirectToRoute('post_comments', ['postId' => $postId]);
        }
    
        return $this->render('comment/add.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    // Supprimer un commentaire
    #[Route('/post/comment/{commentId}/delete', name: 'post_delete_comment', methods: ['POST'])]
    public function deleteComment(int $commentId): Response
    {
        $comment = $this->commentRepository->find($commentId);
        
        if (!$comment) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');

        return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
    }

    // Valider un commentaire (administrateur)
    #[Route('/post/comment/{commentId}/validate', name: 'post_validate_comment', methods: ['POST'])]
    public function validateComment(int $commentId): Response
    {
        $comment = $this->commentRepository->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment->setStatus('validé');
        $this->entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été validé avec succès.');

        return $this->redirectToRoute('post_comments', ['postId' => $comment->getPost()->getId()]);
    }

    // Signaler un commentaire
    #[Route('/comment/{id}/report', name: 'comment_report')]
    public function reportComment(Comment $comment, Request $request): Response
    {
        // Vérifier que l'utilisateur est connecté et approuvé
        $user = $this->getUser();
        if (!$user instanceof User || !$user->getIsAccepted()) {
            $this->addFlash('error', 'Vous devez être connecté et approuvé pour signaler un commentaire.');
            return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
        }

        // Vérifier si l'utilisateur a déjà signalé ce commentaire
        $existingReport = $this->commentReportRepository->findOneBy([
            'comment' => $comment,
            'reportedBy' => $user
        ]);

        if ($existingReport) {
            $this->addFlash('warning', 'Vous avez déjà signalé ce commentaire.');
            return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
        }

        $report = new CommentReport();
        $report->setComment($comment);
        $report->setReportedBy($user);

        $form = $this->createForm(CommentReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($report);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre signalement a été envoyé. Il sera examiné par les administrateurs.');
            return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
        }

        return $this->render('comment/report.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }
    
}