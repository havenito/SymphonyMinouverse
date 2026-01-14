<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CommentReport;
use App\Entity\UserWarning;
use App\Repository\UserRepository;
use App\Repository\CommentReportRepository;
use App\Repository\UserWarningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $commentReportRepository;
    private $userWarningRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        CommentReportRepository $commentReportRepository,
        UserWarningRepository $userWarningRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->commentReportRepository = $commentReportRepository;
        $this->userWarningRepository = $userWarningRepository;
    }

    #[Route('/users', name: 'admin_users')]
    public function listUsers(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;
        
        $totalUsers = $this->userRepository->count([]);
        $users = $this->userRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        $pendingReports = $this->commentReportRepository->findPendingReports();
        
        $totalPages = ceil($totalUsers / $limit);
        
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'pendingReports' => $pendingReports,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/reports', name: 'admin_reports')]
    public function listReports(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $pendingReports = $this->commentReportRepository->findPendingReports();
        $resolvedReports = $this->commentReportRepository->findReportsByStatus('resolved');
        
        return $this->render('admin/reports.html.twig', [
            'pendingReports' => $pendingReports,
            'resolvedReports' => $resolvedReports,
        ]);
    }

    #[Route('/user/{id}/approve', name: 'admin_user_approve')]
    public function approveUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Approuver l'utilisateur
        $user->setIsAccepted(true);
        
        // Si c'est un VISITOR, le promouvoir en USER
        if (in_array('ROLE_VISITOR', $user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
        }
        
        $this->entityManager->flush();

        $this->addFlash('success', "L'utilisateur {$user->getEmail()} a été approuvé et promu au rang d'utilisateur.");
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/ban', name: 'admin_user_ban')]
    public function banUser(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $reason = $request->request->get('reason', 'Comportement inapproprié');
        
        $user->setIsBanned(true);
        $user->setBanReason($reason);
        $user->setBannedAt(new \DateTime());
        
        $this->entityManager->flush();

        $this->addFlash('success', "L'utilisateur {$user->getEmail()} a été banni.");
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/unban', name: 'admin_user_unban')]
    public function unbanUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $user->setIsBanned(false);
        $user->setBanReason(null);
        $user->setBannedAt(null);
        
        $this->entityManager->flush();

        $this->addFlash('success', "L'utilisateur {$user->getEmail()} a été débanni.");
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/report/{id}/warn', name: 'admin_report_warn')]
    public function warnUser(CommentReport $report, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Vérification CSRF
        if (!$this->isCsrfTokenValid('warn_report_' . $report->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('admin_reports');
        }
        
        try {
            $reason = $request->request->get('reason', 'Commentaire inapproprié');
            
            // Récupérer l'utilisateur du commentaire avant toute modification
            $user = $report->getComment()->getUser();
            
            // Créer un avertissement
            $warning = new UserWarning();
            $warning->setUser($user);
            $warning->setIssuedBy($this->getUser());
            $warning->setReason($reason);
            $warning->setRelatedReport($report);
            
            $this->entityManager->persist($warning);
            
            // Incrémenter le compteur de warnings de l'utilisateur
            $user->addWarning();
            
            // Marquer le signalement comme résolu
            $report->setStatus('resolved');
            $report->setReviewedBy($this->getUser());
            $report->setReviewedAt(new \DateTime());
            
            // Sauvegarder les changements AVANT de supprimer le commentaire
            $this->entityManager->flush();
            
            // Maintenant supprimer le commentaire
            $this->entityManager->remove($report->getComment());
            $this->entityManager->flush();

            if ($user->getIsBanned()) {
                $this->addFlash('warning', "L'utilisateur {$user->getEmail()} a reçu un avertissement et a été automatiquement banni (3 avertissements).");
            } else {
                $this->addFlash('success', "L'utilisateur {$user->getEmail()} a reçu un avertissement ({$user->getWarningCount()}/3).");
            }

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'avertissement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_reports');
    }

    #[Route('/report/{id}/ban', name: 'admin_report_ban')]
    public function banUserFromReport(CommentReport $report, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Vérification CSRF
        if (!$this->isCsrfTokenValid('ban_report_' . $report->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('admin_reports');
        }
        
        try {
            $reason = $request->request->get('reason', 'Commentaire gravement inapproprié');
            
            // Récupérer l'utilisateur du commentaire avant toute modification
            $user = $report->getComment()->getUser();
            $user->setIsBanned(true);
            $user->setBanReason($reason);
            $user->setBannedAt(new \DateTime());
            
            // Marquer le signalement comme résolu
            $report->setStatus('resolved');
            $report->setReviewedBy($this->getUser());
            $report->setReviewedAt(new \DateTime());
            
            // Sauvegarder les changements AVANT de supprimer le commentaire
            $this->entityManager->flush();
            
            // Maintenant supprimer le commentaire
            $this->entityManager->remove($report->getComment());
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur {$user->getEmail()} a été banni définitivement.");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du bannissement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_reports');
    }

    #[Route('/report/{id}/dismiss', name: 'admin_report_dismiss')]
    public function dismissReport(CommentReport $report): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        try {
            $report->setStatus('dismissed');
            $report->setReviewedBy($this->getUser());
            $report->setReviewedAt(new \DateTime());
            
            $this->entityManager->flush();

            $this->addFlash('info', 'Le signalement a été rejeté.');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_reports');
    }
}