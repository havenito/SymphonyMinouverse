<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginSuccessListener implements EventSubscriberInterface
{
    private RouterInterface $router;
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RouterInterface $router, 
        RequestStack $requestStack, 
        EntityManagerInterface $entityManager
    ) {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            // Vérifier si l'utilisateur est banni
            if ($user->getIsBanned()) {
                $request = $this->requestStack->getCurrentRequest();
                $session = $request->getSession();
                
                $banMessage = 'Votre compte a été banni.';
                if ($user->getBanReason()) {
                    $banMessage .= ' Raison : ' . $user->getBanReason();
                }
                
                // Utiliser la session pour stocker le message d'erreur
                $session->set('login_error', $banMessage);
                
                // Rediriger vers la page de connexion
                $response = new RedirectResponse($this->router->generate('app_login') . '?banned=1');
                $event->setResponse($response);
                return;
            }

            // Promouvoir automatiquement les VISITOR en USER lors de la connexion
            if (in_array('ROLE_VISITOR', $user->getRoles()) && !in_array('ROLE_USER', $user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
                $user->setIsAccepted(true);
                
                // Sauvegarder les changements
                $this->entityManager->flush();
            }
        }
    }
}