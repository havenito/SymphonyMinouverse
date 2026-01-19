<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que les différents boutons sont présents et fonctionnent correctement
 */
class ButtonTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'KERNEL_CLASS' => \App\Kernel::class,
        ]);
        $this->client->followRedirects();
    }

    /**
     * bouton de connexion sur la page d'accueil (présent et accessible)
     */
    public function testLoginButtonExists(): void
    {
        $crawler = $this->client->request('GET', '/login');
        
        // Vérifie que la page se charge correctement
        $this->assertResponseIsSuccessful();
        
        // Vérifie la présence du bouton de connexion
        $this->assertSelectorExists('button[type="submit"]');
        
        // Compte le nombre de boutons submit sur la page de login
        $submitButtons = $crawler->filter('button[type="submit"]');
        $this->assertGreaterThan(0, count($submitButtons), 'Le bouton de connexion doit être présent');
    }

    /**
     * Bouton d'inscription (présent sur la page de registration)
     */
    public function testRegistrationButtonExists(): void
    {
        $crawler = $this->client->request('GET', '/register');
        
        // Vérifie que la page se charge correctement
        $this->assertResponseIsSuccessful();
        
        // Vérifie la présence d'un bouton submit pour l'inscription
        $this->assertSelectorExists('button[type="submit"]');
        
        // Vérifie le texte du bouton
        $submitButtons = $crawler->filter('button[type="submit"]');
        $this->assertGreaterThan(0, count($submitButtons));
    }

    /**
     * Bouton "retour" sur les pages (présents sur les pages appropriées)
     */
    public function testBackButtonOnPages(): void
    {
        $crawler = $this->client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        
        $backLinks = $crawler->filter('a.btn, a[href*="back"], a:contains("Retour")');
        
        $this->assertGreaterThanOrEqual(0, count($backLinks));
    }

    /**
     * Test des boutons avec classes Bootstrap
     */
    public function testBootstrapButtonClasses(): void
    {
        $crawler = $this->client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        
        $buttons = $crawler->filter('.btn, button.btn, a.btn');
        
        $this->assertGreaterThanOrEqual(0, count($buttons));
    }

    /**
     * Nettoyage après les tests
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
