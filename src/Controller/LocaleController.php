<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/locale/{_locale}', name: 'change_locale', requirements: ['_locale' => 'fr|en'])]
    public function changeLocale(string $_locale, Request $request): Response
    {
        // Sauvegarder la locale dans la session
        $request->getSession()->set('_locale', $_locale);
        
        // Rediriger vers la page précédente ou l'accueil
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }
        
        return $this->redirectToRoute('main');
    }
}
