<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        // Ne rien faire pour les requêtes de sous-requêtes
        if (!$event->isMainRequest()) {
            return;
        }

        // 1. Vérifier si la locale est dans la session
        $session = $request->getSession();
        if ($session && $locale = $session->get('_locale')) {
            $request->setLocale($locale);
            return;
        }

        // 2. Sinon, détecter la locale depuis le navigateur
        $preferredLanguage = $request->getPreferredLanguage(['fr', 'en']);
        
        // Si une langue préférée est détectée, l'utiliser
        if ($preferredLanguage) {
            $request->setLocale($preferredLanguage);
            if ($session) {
                $session->set('_locale', $preferredLanguage);
            }
        } else {
            // Sinon, utiliser la locale par défaut
            $request->setLocale($this->defaultLocale);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
