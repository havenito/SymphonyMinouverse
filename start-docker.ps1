# Script de démarrage Docker pour Blog Minouverse
Write-Host "🐳 Blog Minouverse - Démarrage Docker" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Vérifier si Docker est installé
Write-Host "🔍 Vérification de Docker..." -ForegroundColor Yellow
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Docker n'est pas installé !" -ForegroundColor Red
    Write-Host "📥 Téléchargez Docker Desktop : https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

# Vérifier si Docker est en cours d'exécution
try {
    docker info | Out-Null
    Write-Host " Docker est démarré" -ForegroundColor Green
} catch {
    Write-Host " Docker n'est pas démarré !" -ForegroundColor Red
    Write-Host " Veuillez lancer Docker Desktop" -ForegroundColor Yellow
    exit 1
}

# Copier le fichier .env si nécessaire
if (-not (Test-Path ".env")) {
    Write-Host " Création du fichier .env..." -ForegroundColor Yellow
    Copy-Item ".env.docker" ".env"
    Write-Host " Fichier .env créé" -ForegroundColor Green
} else {
    Write-Host "ℹ  Fichier .env existant conservé" -ForegroundColor Blue
}

# Arrêter les services existants
Write-Host "`n Arrêt des services existants..." -ForegroundColor Yellow
docker-compose down 2>$null

# Construire et démarrer les services
Write-Host " Construction et démarrage des services..." -ForegroundColor Yellow
Write-Host " Cela peut prendre quelques minutes la première fois...`n" -ForegroundColor Gray

docker-compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Host "`n Erreur lors du démarrage !" -ForegroundColor Red
    Write-Host " Vérifiez les logs avec : docker-compose logs" -ForegroundColor Yellow
    exit 1
}

# Attendre que les services soient prêts
Write-Host "`n Attente du démarrage des services..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

# Afficher l'état des services
Write-Host "`n État des services :" -ForegroundColor Cyan
docker-compose ps

# Créer les dossiers nécessaires et définir les permissions
Write-Host "`n Configuration des dossiers..." -ForegroundColor Yellow
docker-compose exec -T web mkdir -p public/uploads/posts public/uploads/profiles 2>$null
docker-compose exec -T web chmod -R 777 public/uploads var 2>$null

# Vider le cache Symfony
Write-Host " Nettoyage du cache..." -ForegroundColor Yellow
docker-compose exec -T web php bin/console cache:clear --no-interaction 2>$null

# Afficher les informations de connexion
Write-Host "`n Tous les services sont démarrés !`n" -ForegroundColor Green

Write-Host "ACCÈS AUX SERVICES :" -ForegroundColor Cyan
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "  📱 Application web" -ForegroundColor White
Write-Host "     → http://localhost:8080`n" -ForegroundColor Blue

Write-Host "  phpMyAdmin" -ForegroundColor White
Write-Host "     → http://localhost:8081" -ForegroundColor Blue
Write-Host "     Serveur      : database" -ForegroundColor Gray
Write-Host "     Utilisateur  : blog_user" -ForegroundColor Gray
Write-Host "     Mot de passe : blog_password`n" -ForegroundColor Gray

Write-Host "COMMANDES UTILES :" -ForegroundColor Cyan
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "  docker-compose logs -f          # Voir les logs" -ForegroundColor White
Write-Host "  docker-compose down             # Arrêter les services" -ForegroundColor White
Write-Host "  docker-compose restart web      # Redémarrer l'app" -ForegroundColor White
Write-Host "  docker-compose exec web bash    # Accéder au conteneur`n" -ForegroundColor White

Write-Host "Bon développement !" -ForegroundColor Magenta
