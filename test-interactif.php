<?php
/**
 * Script de test interactif
 * Permet de tester manuellement les entités User et Post
 * 
 * Usage: php test-interactif.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\User;
use App\Entity\Post;

// Couleurs pour le terminal
class Color {
    const RESET = "\033[0m";
    const GREEN = "\033[32m";
    const BLUE = "\033[34m";
    const CYAN = "\033[36m";
    const YELLOW = "\033[33m";
    const RED = "\033[31m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
}

function afficherHeader() {
    echo "\n";
    echo Color::CYAN . "╔════════════════════════════════════════════════════════════════╗" . Color::RESET . "\n";
    echo Color::CYAN . "║           TEST UNITAIRES - BLOG MINOUVERSE                     ║" . Color::RESET . "\n";
    echo Color::CYAN . "╚════════════════════════════════════════════════════════════════╝" . Color::RESET . "\n";
    echo "\n";
}

function lireEntree($question) {
    echo Color::YELLOW . $question . Color::RESET . " ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return trim($line);
}

function afficherSucces($message) {
    echo Color::GREEN . "  ✅ " . $message . Color::RESET . "\n";
}

function afficherEchec($message) {
    echo Color::RED . "  ❌ " . $message . Color::RESET . "\n";
}

function afficherInfo($message) {
    echo Color::CYAN . "  ℹ️  " . $message . Color::RESET . "\n";
}

function testerUser() {
    echo "\n";
    echo Color::BLUE . Color::BOLD . "═══════════════ TEST DE L'ENTITÉ USER ═══════════════" . Color::RESET . "\n\n";
    
    try {
        $user = new User();
        afficherSucces("Création de l'utilisateur réussie");
        
        // Test Email
        echo "\n" . Color::CYAN . "Test de l'email" . Color::RESET . "\n";
        $email = lireEntree("Entrez un email :");
        
        if (empty($email)) {
            afficherEchec("L'email ne peut pas être vide");
        } else {
            $user->setEmail($email);
            if ($user->getEmail() === $email) {
                afficherSucces("L'email '$email' a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement de l'email");
            }
        }
        
        // Test Prénom
        echo "\n" . Color::CYAN . "Test du prénom" . Color::RESET . "\n";
        $firstName = lireEntree("Entrez un prénom :");
        
        if (empty($firstName)) {
            afficherEchec("Le prénom ne peut pas être vide");
        } else {
            $user->setFirstName($firstName);
            if ($user->getFirstName() === $firstName) {
                afficherSucces("Le prénom '$firstName' a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du prénom");
            }
        }
        
        // Test Nom
        echo "\n" . Color::CYAN . "Test du nom" . Color::RESET . "\n";
        $lastName = lireEntree("Entrez un nom :");
        
        if (empty($lastName)) {
            afficherEchec("Le nom ne peut pas être vide");
        } else {
            $user->setLastName($lastName);
            if ($user->getLastName() === $lastName) {
                afficherSucces("Le nom '$lastName' a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du nom");
            }
        }
        
        // Test Mot de passe
        echo "\n" . Color::CYAN . "Test du mot de passe" . Color::RESET . "\n";
        $password = lireEntree("Entrez un mot de passe :");
        
        if (empty($password)) {
            afficherEchec("Le mot de passe ne peut pas être vide");
        } else {
            $user->setPassword($password);
            if ($user->getPassword() === $password) {
                afficherSucces("Le mot de passe a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du mot de passe");
            }
        }
        
        // Test Rôles
        echo "\n" . Color::CYAN . "Test des rôles" . Color::RESET . "\n";
        echo Color::WHITE . "Rôles disponibles : ROLE_USER, ROLE_ADMIN, ROLE_MODERATOR" . Color::RESET . "\n";
        $roles = lireEntree("Entrez les rôles séparés par des virgules :");
        
        if (!empty($roles)) {
            $rolesArray = array_map('trim', explode(',', $roles));
            $rolesValides = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR'];
            $rolesInvalides = [];
            
            // Vérifier que tous les rôles sont valides
            foreach ($rolesArray as $role) {
                if (!in_array($role, $rolesValides)) {
                    $rolesInvalides[] = $role;
                }
            }
            
            if (!empty($rolesInvalides)) {
                afficherEchec("Rôle(s) invalide(s) : " . implode(', ', $rolesInvalides));
                afficherInfo("Veuillez utiliser uniquement : ROLE_USER, ROLE_ADMIN, ROLE_MODERATOR");
            } else {
                $user->setRoles($rolesArray);
                
                $userRoles = $user->getRoles();
                $tousPresents = true;
                foreach ($rolesArray as $role) {
                    if (!in_array($role, $userRoles)) {
                        $tousPresents = false;
                        break;
                    }
                }
                
                if ($tousPresents) {
                    afficherSucces("Les rôles ont été enregistrés correctement : " . implode(', ', $userRoles));
                } else {
                    afficherEchec("Erreur lors de l'enregistrement des rôles");
                }
            }
        }
        
        // Test Photo de profil
        echo "\n" . Color::CYAN . "Test de la photo de profil" . Color::RESET . "\n";
        $photo = lireEntree("Entrez le nom d'une photo (ex: photo.jpg) ou laissez vide :");
        
        if (!empty($photo)) {
            $user->setProfilePicture($photo);
            if ($user->getProfilePicture() === $photo) {
                afficherSucces("La photo '$photo' a été enregistrée correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement de la photo");
            }
        } else {
            afficherInfo("Aucune photo de profil définie");
        }
        
        // Test Statut actif
        echo "\n" . Color::CYAN . "Test du statut actif" . Color::RESET . "\n";
        $actif = lireEntree("L'utilisateur est-il actif ? (o/n) :");
        
        if ($actif === 'o' || $actif === 'O') {
            $user->setIsActive(true);
            if ($user->getIsActive() === true) {
                afficherSucces("L'utilisateur est maintenant actif");
            } else {
                afficherEchec("Erreur lors de l'activation");
            }
        } elseif ($actif === 'n' || $actif === 'N') {
            $user->setIsActive(false);
            if ($user->getIsActive() === false) {
                afficherSucces("L'utilisateur est maintenant inactif");
            } else {
                afficherEchec("Erreur lors de la désactivation");
            }
        }
        
        // Récapitulatif
        echo "\n";
        echo Color::CYAN . "═══════════════════════════════════════════════════════════════" . Color::RESET . "\n";
        echo Color::BOLD . "📋 RÉCAPITULATIF DE L'UTILISATEUR :" . Color::RESET . "\n";
        echo Color::WHITE . "  • Email : " . ($user->getEmail() ?? 'Non défini') . Color::RESET . "\n";
        echo Color::WHITE . "  • Prénom : " . ($user->getFirstName() ?? 'Non défini') . Color::RESET . "\n";
        echo Color::WHITE . "  • Nom : " . ($user->getLastName() ?? 'Non défini') . Color::RESET . "\n";
        echo Color::WHITE . "  • Rôles : " . implode(', ', $user->getRoles()) . Color::RESET . "\n";
        echo Color::WHITE . "  • Photo : " . ($user->getProfilePicture() ?? 'Aucune') . Color::RESET . "\n";
        echo Color::WHITE . "  • Actif : " . ($user->getIsActive() ? 'Oui' : 'Non') . Color::RESET . "\n";
        echo Color::WHITE . "  • Date de création : " . $user->getCreatedAt()->format('d/m/Y H:i:s') . Color::RESET . "\n";
        echo Color::CYAN . "═══════════════════════════════════════════════════════════════" . Color::RESET . "\n";
        
        echo "\n" . Color::GREEN . "🎉 Tous les tests de l'utilisateur ont été exécutés !" . Color::RESET . "\n";
        
    } catch (\Exception $e) {
        echo "\n";
        afficherEchec("ERREUR CRITIQUE : " . $e->getMessage());
        echo Color::RED . "Trace : " . $e->getTraceAsString() . Color::RESET . "\n";
    }
}

function testerPost() {
    echo "\n";
    echo Color::BLUE . Color::BOLD . "═══════════════ TEST DE L'ENTITÉ POST ═══════════════" . Color::RESET . "\n\n";
    
    try {
        $post = new Post();
        afficherSucces("Création du post réussie");
        
        // Test Titre
        echo "\n" . Color::CYAN . "📝 Test du titre" . Color::RESET . "\n";
        $title = lireEntree("Entrez un titre pour le post :");
        
        if (empty($title)) {
            afficherEchec("Le titre ne peut pas être vide");
        } else {
            $post->setTitle($title);
            if ($post->getTitle() === $title) {
                afficherSucces("Le titre '$title' a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du titre");
            }
        }
        
        // Test Contenu
        echo "\n" . Color::CYAN . "📄 Test du contenu" . Color::RESET . "\n";
        $content = lireEntree("Entrez le contenu du post :");
        
        if (empty($content)) {
            afficherEchec("Le contenu ne peut pas être vide");
        } else {
            $post->setContent($content);
            if ($post->getContent() === $content) {
                afficherSucces("Le contenu a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du contenu");
            }
        }
        
        // Test Titre anglais
        echo "\n" . Color::CYAN . "🇬🇧 Test du titre en anglais" . Color::RESET . "\n";
        $titleEn = lireEntree("Entrez un titre en anglais (optionnel) :");
        
        if (!empty($titleEn)) {
            $post->setTitleEn($titleEn);
            if ($post->getTitleEn() === $titleEn) {
                afficherSucces("Le titre anglais '$titleEn' a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du titre anglais");
            }
        } else {
            afficherInfo("Pas de titre anglais défini");
        }
        
        // Test Contenu anglais
        echo "\n" . Color::CYAN . "🇬🇧 Test du contenu en anglais" . Color::RESET . "\n";
        $contentEn = lireEntree("Entrez le contenu en anglais (optionnel) :");
        
        if (!empty($contentEn)) {
            $post->setContentEn($contentEn);
            if ($post->getContentEn() === $contentEn) {
                afficherSucces("Le contenu anglais a été enregistré correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement du contenu anglais");
            }
        } else {
            afficherInfo("Pas de contenu anglais défini");
        }
        
        // Test Image
        echo "\n" . Color::CYAN . "🖼️  Test de l'image" . Color::RESET . "\n";
        $picture = lireEntree("Entrez le nom d'une image (ex: article.jpg) ou laissez vide :");
        
        if (!empty($picture)) {
            $post->setPicture($picture);
            if ($post->getPicture() === $picture) {
                afficherSucces("L'image '$picture' a été enregistrée correctement");
            } else {
                afficherEchec("Erreur lors de l'enregistrement de l'image");
            }
        } else {
            afficherInfo("Aucune image définie");
        }
        
        // Test Date de publication
        echo "\n" . Color::CYAN . "📅 Test de la date de publication" . Color::RESET . "\n";
        $publier = lireEntree("Publier maintenant ? (o/n) :");
        
        if ($publier === 'o' || $publier === 'O') {
            $post->setPublishedAt(new \DateTime());
            if ($post->getPublishedAt() !== null) {
                afficherSucces("Date de publication définie : " . $post->getPublishedAt()->format('d/m/Y H:i:s'));
            } else {
                afficherEchec("Erreur lors de la définition de la date");
            }
        } else {
            afficherInfo("Le post n'est pas publié (brouillon)");
        }
        
        // Récapitulatif
        echo "\n";
        echo Color::CYAN . "═══════════════════════════════════════════════════════════════" . Color::RESET . "\n";
        echo Color::BOLD . "📋 RÉCAPITULATIF DU POST :" . Color::RESET . "\n";
        echo Color::WHITE . "  • Titre (FR) : " . ($post->getTitle() ?? 'Non défini') . Color::RESET . "\n";
        echo Color::WHITE . "  • Contenu (FR) : " . substr($post->getContent() ?? 'Non défini', 0, 50) . "..." . Color::RESET . "\n";
        echo Color::WHITE . "  • Titre (EN) : " . ($post->getTitleEn() ?? 'Non défini') . Color::RESET . "\n";
        echo Color::WHITE . "  • Contenu (EN) : " . (($post->getContentEn() ? substr($post->getContentEn(), 0, 50) . "..." : 'Non défini')) . Color::RESET . "\n";
        echo Color::WHITE . "  • Image : " . ($post->getPicture() ?? 'Aucune') . Color::RESET . "\n";
        echo Color::WHITE . "  • Publié : " . ($post->getPublishedAt() ? $post->getPublishedAt()->format('d/m/Y H:i:s') : 'Brouillon') . Color::RESET . "\n";
        echo Color::CYAN . "═══════════════════════════════════════════════════════════════" . Color::RESET . "\n";
        
        echo "\n" . Color::GREEN . "Tous les tests du post ont été exécutés !" . Color::RESET . "\n";
        
    } catch (\Exception $e) {
        echo "\n";
        afficherEchec("ERREUR CRITIQUE : " . $e->getMessage());
        echo Color::RED . "Trace : " . $e->getTraceAsString() . Color::RESET . "\n";
    }
}

// Programme principal
afficherHeader();

echo Color::WHITE . "Choisissez l'entité à tester :" . Color::RESET . "\n";
echo Color::CYAN . "  1. User (Utilisateur)" . Color::RESET . "\n";
echo Color::CYAN . "  2. Post (Article)" . Color::RESET . "\n";
echo Color::CYAN . "  3. Les deux" . Color::RESET . "\n";

$choix = lireEntree("\nVotre choix (1, 2 ou 3) :");

switch ($choix) {
    case '1':
        testerUser();
        break;
    case '2':
        testerPost();
        break;
    case '3':
        testerUser();
        echo "\n\n";
        testerPost();
        break;
    default:
        echo Color::RED . "Choix invalide. Veuillez relancer le script." . Color::RESET . "\n";
        exit(1);
}

echo "\n";
echo Color::CYAN . "╔════════════════════════════════════════════════════════════════╗" . Color::RESET . "\n";
echo Color::CYAN . "║                    Tests terminés !                            ║" . Color::RESET . "\n";
echo Color::CYAN . "╚════════════════════════════════════════════════════════════════╝" . Color::RESET . "\n";
echo "\n";
