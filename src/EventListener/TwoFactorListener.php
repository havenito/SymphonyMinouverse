<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TwoFactorListener implements EventSubscriberInterface
{
    private RouterInterface $router;
    private TokenStorageInterface $tokenStorage;

    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        // Routes qui ne nécessitent pas de vérification 2FA
        $allowedRoutes = [
            'app_2fa_verify',
            'app_2fa_check',
            'app_login',
            'app_logout',
            '_wdt',
            '_profiler',
        ];

        $currentRoute = $request->attributes->get('_route');

        if (in_array($currentRoute, $allowedRoutes)) {
            return;
        }

        // Si l'utilisateur a besoin de la 2FA mais n'est pas sur la page de vérification
        if ($session->get('2fa_required') && $currentRoute !== 'app_2fa_verify') {
            $response = new RedirectResponse($this->router->generate('app_2fa_verify'));
            $event->setResponse($response);
        }
    }
}
