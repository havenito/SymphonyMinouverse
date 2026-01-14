<?php

namespace App\Controller;

use App\Service\TwoFactorAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/2fa')]
class TwoFactorController extends AbstractController
{
    #[Route('/setup', name: 'app_2fa_setup')]
    public function setup(TwoFactorAuthService $twoFactorService, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Générer un nouveau secret si pas déjà fait
        if (!$user->getTwoFactorSecret()) {
            $secret = $twoFactorService->generateSecret();
            $user->setTwoFactorSecret($secret);
            $entityManager->flush();
        }

        $qrCodeUrl = $twoFactorService->getQRCodeUrl(
            $user->getTwoFactorSecret(),
            $user->getEmail(),
            'BlogMinouverse'
        );

        // Générer les codes de récupération
        $backupCodes = $twoFactorService->generateBackupCodes(10);

        return $this->render('security/2fa_setup.html.twig', [
            'secret' => $user->getTwoFactorSecret(),
            'qrCodeUrl' => $qrCodeUrl,
            'backupCodes' => $backupCodes,
        ]);
    }

    #[Route('/enable', name: 'app_2fa_enable', methods: ['POST'])]
    public function enable(
        Request $request,
        TwoFactorAuthService $twoFactorService,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $code = $request->request->get('code');

        // Vérifier le secret existe
        if (!$user->getTwoFactorSecret()) {
            $this->addFlash('error', 'Aucun secret 2FA configuré. Veuillez réessayer la configuration.');
            return $this->redirectToRoute('app_2fa_setup');
        }

        if (!$twoFactorService->verifyCode($user->getTwoFactorSecret(), $code)) {
            $this->addFlash('error', 'Code invalide. Veuillez réessayer.');
            return $this->redirectToRoute('app_2fa_setup');
        }

        // Activer la 2FA
        $user->setTwoFactorEnabled(true);

        // Sauvegarder les codes de récupération hashés
        $backupCodes = json_decode($request->request->get('backup_codes'), true);
        $hashedCodes = array_map(
            fn($code) => $twoFactorService->hashBackupCode($code),
            $backupCodes
        );
        $user->setBackupCodes($hashedCodes);

        $entityManager->flush();

        $this->addFlash('success', 'La double authentification a été activée avec succès !');
        return $this->redirectToRoute('app_2fa_status');
    }

    #[Route('/disable', name: 'app_2fa_disable', methods: ['POST'])]
    public function disable(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Désactiver la 2FA
        $user->setTwoFactorEnabled(false);
        $user->setTwoFactorSecret(null);
        $user->setBackupCodes(null);

        $entityManager->flush();

        $this->addFlash('success', 'La double authentification a été désactivée.');
        return $this->redirectToRoute('app_2fa_status');
    }

    #[Route('/status', name: 'app_2fa_status')]
    public function status(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('security/2fa_status.html.twig', [
            'is2faEnabled' => $user->isTwoFactorEnabled(),
            'backupCodesCount' => $user->getBackupCodes() ? count($user->getBackupCodes()) : 0,
        ]);
    }

    #[Route('/verify', name: 'app_2fa_verify')]
    public function verify(SessionInterface $session): Response
    {
        // Page de vérification du code 2FA après connexion
        if (!$session->get('2fa_required')) {
            return $this->redirectToRoute('main');
        }

        return $this->render('security/2fa_verify.html.twig');
    }

    #[Route('/check', name: 'app_2fa_check', methods: ['POST'])]
    public function check(
        Request $request,
        TwoFactorAuthService $twoFactorService,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$session->get('2fa_required')) {
            return $this->redirectToRoute('main');
        }

        $userId = $session->get('2fa_user_id');
        $user = $entityManager->getRepository(\App\Entity\User::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $code = $request->request->get('code');
        $useBackupCode = $request->request->get('use_backup_code', false);

        $isValid = false;

        if ($useBackupCode) {
            // Vérifier un code de récupération
            if ($user->getBackupCodes()) {
                foreach ($user->getBackupCodes() as $hashedCode) {
                    if ($twoFactorService->verifyBackupCode($code, $hashedCode)) {
                        $isValid = true;
                        // Supprimer le code utilisé
                        $user->removeBackupCode($code);
                        $entityManager->flush();
                        break;
                    }
                }
            }
        } else {
            // Vérifier le code TOTP
            $isValid = $twoFactorService->verifyCode($user->getTwoFactorSecret(), $code);
        }

        if (!$isValid) {
            $this->addFlash('error', 'Code invalide. Veuillez réessayer.');
            return $this->redirectToRoute('app_2fa_verify');
        }

        // Authentification réussie
        $session->remove('2fa_required');
        $session->remove('2fa_user_id');
        $session->set('2fa_complete', true);

        $this->addFlash('success', 'Authentification réussie !');
        return $this->redirectToRoute('main');
    }
}
