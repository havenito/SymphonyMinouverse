<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Entity\User;

class TwoFactorAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new RedirectResponse($this->router->generate('main'));
        }

        // Vérifier si l'utilisateur a la 2FA activée
        if ($user->isTwoFactorEnabled()) {
            $session = $request->getSession();
            $session->set('2fa_required', true);
            $session->set('2fa_user_id', $user->getId());
            
            // Déconnecter temporairement l'utilisateur
            $token->getUser()->eraseCredentials();
            
            return new RedirectResponse($this->router->generate('app_2fa_verify'));
        }

        // Sinon, connexion normale
        return new RedirectResponse($this->router->generate('main'));
    }
}
