<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire basique pour l'entité User
 * 
 * Ce test vérifie les fonctions de base de l'entité User :
 * - Création d'un utilisateur
 * - Getters et Setters (email, nom, prénom, mot de passe)
 * - Gestion des rôles
 * - Date de création automatique
 */
class UserTest extends TestCase
{
    /**
     * Test 1 : Création d'un utilisateur
     * Vérifie qu'on peut créer un nouvel utilisateur
     */
    public function testCanCreateUser(): void
    {
        $user = new User();
        
        // Un nouvel utilisateur existe et n'a pas encore d'ID
        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->getId());
    }

    /**
     * Test 2 : Setter et Getter pour l'email
     * Vérifie qu'on peut définir et récupérer un email
     */
    public function testEmailGetterAndSetter(): void
    {
        $user = new User();
        $email = 'test@example.com';
        
        // Définir l'email avec setEmail()
        $user->setEmail($email);
        
        // Récupérer l'email avec getEmail()
        $this->assertEquals($email, $user->getEmail());
    }

    /**
     * Test 3 : Setter et Getter pour le prénom et nom
     * Vérifie qu'on peut définir et récupérer le prénom et le nom
     */
    public function testNameGettersAndSetters(): void
    {
        $user = new User();
        
        // Définir le prénom
        $user->setFirstName('Jean');
        $this->assertEquals('Jean', $user->getFirstName());
        
        // Définir le nom
        $user->setLastName('Dupont');
        $this->assertEquals('Dupont', $user->getLastName());
    }

    /**
     * Test 4 : Setter et Getter pour le mot de passe
     * Vérifie qu'on peut définir et récupérer un mot de passe
     */
    public function testPasswordGetterAndSetter(): void
    {
        $user = new User();
        $password = 'motdepasse123';
        
        $user->setPassword($password);
        
        $this->assertEquals($password, $user->getPassword());
    }

    /**
     * Test 5 : Gestion des rôles
     * Vérifie qu'on peut ajouter et récupérer des rôles
     */
    public function testRolesManagement(): void
    {
        $user = new User();
        
        // Par défaut, le tableau de rôles est vide
        $this->assertIsArray($user->getRoles());
        
        // Ajouter des rôles
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user->setRoles($roles);
        
        // Vérifier que les rôles sont bien enregistrés
        $userRoles = $user->getRoles();
        $this->assertContains('ROLE_USER', $userRoles);
        $this->assertContains('ROLE_ADMIN', $userRoles);
    }

    /**
     * Test 6 : Date de création automatique
     * Vérifie que la date de création est définie automatiquement
     */
    public function testCreatedAtIsSetAutomatically(): void
    {
        $user = new User();
        
        // La date de création doit être définie automatiquement
        $this->assertNotNull($user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getCreatedAt());
        
        // Vérifier que la date est très récente (moins de 2 secondes)
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $user->getCreatedAt()->getTimestamp();
        $this->assertLessThan(2, $diff);
    }

    /**
     * Test 7 : Statut actif par défaut
     * Vérifie qu'un utilisateur est actif par défaut
     */
    public function testUserIsActiveByDefault(): void
    {
        $user = new User();
        
        // Par défaut, un utilisateur doit être actif
        $this->assertTrue($user->getIsActive());
    }

    /**
     * Test 8 : Modification du statut actif
     * Vérifie qu'on peut activer/désactiver un utilisateur
     */
    public function testCanChangeActiveStatus(): void
    {
        $user = new User();
        
        // Désactiver l'utilisateur
        $user->setIsActive(false);
        $this->assertFalse($user->getIsActive());
        
        // Réactiver l'utilisateur
        $user->setIsActive(true);
        $this->assertTrue($user->getIsActive());
    }

    /**
     * Test 9 : Photo de profil
     * Vérifie qu'on peut définir une photo de profil
     */
    public function testProfilePictureGetterAndSetter(): void
    {
        $user = new User();
        
        // Par défaut, pas de photo
        $this->assertNull($user->getProfilePicture());
        
        // Ajouter une photo
        $user->setProfilePicture('photo.jpg');
        $this->assertEquals('photo.jpg', $user->getProfilePicture());
    }

    /**
     * Test 10 : Chaînage des méthodes (Fluent Interface)
     * Vérifie qu'on peut chaîner les setters
     */
    public function testFluentInterface(): void
    {
        $user = new User();
        
        // Chaîner plusieurs setters
        $result = $user
            ->setEmail('john@example.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPassword('secret123');
        
        // Le résultat doit être la même instance
        $this->assertSame($user, $result);
        
        // Vérifier que toutes les valeurs sont définies
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('secret123', $user->getPassword());
    }

    /**
     * Test 11 : Compteur d'avertissements par défaut
     * Vérifie que le compteur d'avertissements commence à 0
     */
    public function testWarningCountDefaultValue(): void
    {
        $user = new User();
        
        // Par défaut, aucun avertissement
        $this->assertEquals(0, $user->getWarningCount());
    }

    /**
     * Test 12 : UserIdentifier (pour Symfony Security)
     * Vérifie que getUserIdentifier retourne l'email
     */
    public function testUserIdentifier(): void
    {
        $user = new User();
        $email = 'user@test.com';
        
        $user->setEmail($email);
        
        // getUserIdentifier doit retourner l'email
        $this->assertEquals($email, $user->getUserIdentifier());
    }
}
