# 🎮 Guide du Test Interactif

## 📋 Description

Le script `test-interactif.php` vous permet de **tester manuellement** les entités de votre projet en entrant vous-même les valeurs. Le script vous dit immédiatement si chaque test fonctionne ou non.

## 🚀 Lancement

```bash
php test-interactif.php
```

## 🎯 Comment ça marche ?

### 1. Choisir l'entité

Au démarrage, vous choisissez ce que vous voulez tester :
- **1** : User (Utilisateur)
- **2** : Post (Article)
- **3** : Les deux

### 2. Entrer vos valeurs

Le script vous demande d'entrer des valeurs une par une. Pour chaque valeur :
- ✅ = Le test a **réussi** (la valeur a été correctement enregistrée)
- ❌ = Le test a **échoué** (il y a un problème)
- ℹ️ = Information (pas critique)

### 3. Récapitulatif

À la fin, vous obtenez un récapitulatif complet de toutes les valeurs enregistrées.

## 📝 Exemples de tests

### Test User (Utilisateur)

```
═══════════════ TEST DE L'ENTITÉ USER ═══════════════

  ✅ Création de l'utilisateur réussie

📧 Test de l'email
Entrez un email : jean.dupont@example.com
  ✅ L'email 'jean.dupont@example.com' a été enregistré correctement

👤 Test du prénom
Entrez un prénom : Jean
  ✅ Le prénom 'Jean' a été enregistré correctement

👤 Test du nom
Entrez un nom : Dupont
  ✅ Le nom 'Dupont' a été enregistré correctement

🔒 Test du mot de passe
Entrez un mot de passe : MonMotDePasse123
  ✅ Le mot de passe a été enregistré correctement

🎭 Test des rôles
Rôles disponibles : ROLE_USER, ROLE_ADMIN, ROLE_MODERATOR
Entrez les rôles séparés par des virgules : ROLE_USER, ROLE_ADMIN
  ✅ Les rôles ont été enregistrés correctement : ROLE_USER, ROLE_ADMIN

📷 Test de la photo de profil
Entrez le nom d'une photo (ex: photo.jpg) ou laissez vide : profil.jpg
  ✅ La photo 'profil.jpg' a été enregistrée correctement

⚡ Test du statut actif
L'utilisateur est-il actif ? (o/n) : o
  ✅ L'utilisateur est maintenant actif

═══════════════════════════════════════════════════════════════
📋 RÉCAPITULATIF DE L'UTILISATEUR :
  • Email : jean.dupont@example.com
  • Prénom : Jean
  • Nom : Dupont
  • Rôles : ROLE_USER, ROLE_ADMIN
  • Photo : profil.jpg
  • Actif : Oui
  • Date de création : 13/01/2026 08:39:05
═══════════════════════════════════════════════════════════════
```

### Test Post (Article)

```
═══════════════ TEST DE L'ENTITÉ POST ═══════════════

  ✅ Création du post réussie

📝 Test du titre
Entrez un titre pour le post : Mon premier article
  ✅ Le titre 'Mon premier article' a été enregistré correctement

📄 Test du contenu
Entrez le contenu du post : Ceci est le contenu de mon article.
  ✅ Le contenu a été enregistré correctement

🇬🇧 Test du titre en anglais
Entrez un titre en anglais (optionnel) : My first article
  ✅ Le titre anglais 'My first article' a été enregistré correctement

🇬🇧 Test du contenu en anglais
Entrez le contenu en anglais (optionnel) : This is my article content.
  ✅ Le contenu anglais a été enregistré correctement

🖼️  Test de l'image
Entrez le nom d'une image (ex: article.jpg) ou laissez vide : article-cover.jpg
  ✅ L'image 'article-cover.jpg' a été enregistrée correctement

📅 Test de la date de publication
Publier maintenant ? (o/n) : o
  ✅ Date de publication définie : 13/01/2026 08:45:30

═══════════════════════════════════════════════════════════════
📋 RÉCAPITULATIF DU POST :
  • Titre (FR) : Mon premier article
  • Contenu (FR) : Ceci est le contenu de mon article....
  • Titre (EN) : My first article
  • Contenu (EN) : This is my article content....
  • Image : article-cover.jpg
  • Publié : 13/01/2026 08:45:30
═══════════════════════════════════════════════════════════════
```

## 💡 Cas d'utilisation

### ✅ Quand utiliser ce script ?

- **Tests manuels** : Vérifier rapidement que vos entités fonctionnent
- **Démonstration** : Montrer comment les entités fonctionnent
- **Débogage** : Trouver des problèmes avec des valeurs spécifiques
- **Apprentissage** : Comprendre comment les getters/setters fonctionnent

### ❌ Quand NE PAS utiliser ce script ?

- **Tests automatisés** : Utilisez plutôt `php run-tests.php` (tests PHPUnit)
- **Tests de régression** : Utilisez les tests unitaires automatiques
- **Intégration continue** : Les tests PHPUnit sont plus adaptés

## 🎨 Personnalisation

Vous pouvez modifier le script pour ajouter d'autres tests :

1. Ouvrir [test-interactif.php](../test-interactif.php)
2. Ajouter vos propres questions dans les fonctions `testerUser()` ou `testerPost()`
3. Relancer le script

## 🔄 Différence avec les tests automatisés

| Aspect | Test Interactif | Tests Automatisés (PHPUnit) |
|--------|----------------|----------------------------|
| **Exécution** | Manuelle | Automatique |
| **Valeurs** | Vous les entrez | Prédéfinies dans le code |
| **Usage** | Test ponctuel | Tests répétés |
| **Vitesse** | Lent | Très rapide |
| **Idéal pour** | Démonstration, débogage | CI/CD, développement |

## 📊 Commandes

| Commande | Description |
|----------|-------------|
| `php test-interactif.php` | Lancer le test interactif |
| `php run-tests.php` | Lancer les tests automatisés |
| `php vendor/bin/phpunit tests/Unit` | Lancer PHPUnit directement |

---

**🎮 Amusez-vous à tester vos entités !**
