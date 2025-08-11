#!/bin/bash

set -e

echo "🚀 Déploiement de l'application Symfony Handmades"
echo "================================================"

# Variables
COMPOSE_FILE="docker-compose.prod.yml"
APP_NAME="symfony_handmades"

# Vérifier que Docker et Docker Compose sont installés
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Vérifier que le fichier .env.prod existe
if [ ! -f ".env.prod" ]; then
    echo "❌ Le fichier .env.prod n'existe pas. Veuillez le créer d'abord."
    exit 1
fi

echo "✅ Vérifications préliminaires terminées"

# Arrêter les conteneurs existants
echo "🛑 Arrêt des conteneurs existants..."
docker-compose -f $COMPOSE_FILE down --remove-orphans || true

# Nettoyer les images non utilisées
echo "🧹 Nettoyage des images Docker..."
docker system prune -f

# Construire les images
echo "🔨 Construction des images Docker..."
docker-compose -f $COMPOSE_FILE build --no-cache

# Démarrer les services
echo "🚀 Démarrage des services..."
docker-compose -f $COMPOSE_FILE up -d

# Attendre que les services soient prêts
echo "⏳ Attente du démarrage des services..."
sleep 30

# Vérifier que l'application répond
echo "🔍 Vérification de l'état de l'application..."
if curl -f http://localhost/health > /dev/null 2>&1; then
    echo "✅ Application déployée avec succès!"
    echo "🌐 L'application est accessible sur http://localhost"
else
    echo "❌ L'application ne répond pas. Vérifiez les logs:"
    echo "   docker-compose -f $COMPOSE_FILE logs"
    exit 1
fi

# Afficher les informations de déploiement
echo ""
echo "📊 Informations de déploiement:"
echo "================================"
echo "🐳 Conteneurs actifs:"
docker-compose -f $COMPOSE_FILE ps

echo ""
echo "📝 Commandes utiles:"
echo "  - Voir les logs: docker-compose -f $COMPOSE_FILE logs -f"
echo "  - Arrêter: docker-compose -f $COMPOSE_FILE down"
echo "  - Redémarrer: docker-compose -f $COMPOSE_FILE restart"
echo "  - Accéder au conteneur: docker-compose -f $COMPOSE_FILE exec app sh"

echo ""
echo "🎉 Déploiement terminé avec succès!"

