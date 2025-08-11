# Guide de Déploiement Docker - Application Symfony Handmades

## 🎯 Résumé

J'ai créé une configuration Docker complète pour votre application Symfony 5.4. Vous avez maintenant deux options de déploiement :

1. **Configuration Production** (recommandée) : Multi-conteneurs avec Nginx, PHP-FPM, PostgreSQL et Redis
2. **Configuration Simple** : Pour tests rapides avec Apache et PostgreSQL

## 📁 Fichiers Créés

### Configuration Docker
- `Dockerfile.new` - Dockerfile optimisé pour la production
- `Dockerfile.simple` - Dockerfile simplifié pour les tests
- `docker-compose.prod.yml` - Configuration production complète
- `docker-compose.simple.yml` - Configuration simplifiée

### Configuration des Services
- `docker/nginx/` - Configuration Nginx avec SSL/TLS
- `docker/apache/` - Configuration Apache simplifiée
- `docker/php/` - Configuration PHP optimisée
- `docker/supervisor/` - Gestion des processus
- `docker/scripts/` - Scripts de démarrage

### Environnement et Sécurité
- `.env.prod` - Variables d'environnement de production
- `config/jwt/` - Clés JWT générées
- `docker/ssl/` - Certificats SSL auto-signés
- `public/health.php` - Endpoint de santé

### Scripts et Documentation
- `deploy.sh` - Script de déploiement automatisé
- `README-DOCKER.md` - Documentation complète
- `GUIDE-DEPLOIEMENT.md` - Ce guide

## 🚀 Déploiement Rapide

### Option 1 : Configuration Simple (Recommandée pour débuter)

```bash
# 1. Démarrer avec la configuration simple
sudo docker-compose -f docker-compose.simple.yml up -d

# 2. Vérifier que l'application fonctionne
curl http://localhost:8080/health

# 3. Accéder à l'application
# http://localhost:8080
```

### Option 2 : Configuration Production

```bash
# 1. Utiliser le script de déploiement automatisé
chmod +x deploy.sh
./deploy.sh

# 2. Ou manuellement
sudo docker-compose -f docker-compose.prod.yml up -d
```

## 🔧 Configuration Requise

### Avant le Déploiement

1. **Variables d'environnement** - Modifiez `.env` :
   ```bash
   # Générez une clé secrète unique
   APP_SECRET=votre_cle_secrete_unique_ici
   
   # Configurez la base de données si nécessaire
   DATABASE_URL=postgresql://symfony:symfony@db:5432/handmades_db
   ```

2. **Clés JWT** (déjà générées) :
   - `config/jwt/private.pem`
   - `config/jwt/public.pem`

3. **Certificats SSL** (déjà générés pour les tests) :
   - `docker/ssl/cert.pem`
   - `docker/ssl/key.pem`

## 📊 Architecture

### Configuration Production
```
Internet → Nginx (SSL/TLS) → Application Symfony → PostgreSQL
                          ↘ Redis (Cache/Sessions)
```

### Configuration Simple
```
Internet → Apache → Application Symfony → PostgreSQL
```

## 🔍 Vérification du Déploiement

### Tests de Santé
```bash
# Endpoint de santé
curl http://localhost:8080/health

# Ou pour la production avec SSL
curl https://localhost/health
```

### Logs
```bash
# Configuration simple
sudo docker-compose -f docker-compose.simple.yml logs -f

# Configuration production
sudo docker-compose -f docker-compose.prod.yml logs -f
```

### Accès aux Conteneurs
```bash
# Application
sudo docker-compose -f docker-compose.simple.yml exec app bash

# Base de données
sudo docker-compose -f docker-compose.simple.yml exec db psql -U symfony handmades_db
```

## 🛠️ Commandes Utiles

### Gestion des Services
```bash
# Démarrer
sudo docker-compose -f docker-compose.simple.yml up -d

# Arrêter
sudo docker-compose -f docker-compose.simple.yml down

# Redémarrer
sudo docker-compose -f docker-compose.simple.yml restart

# Voir l'état
sudo docker-compose -f docker-compose.simple.yml ps
```

### Commandes Symfony
```bash
# Cache
sudo docker-compose -f docker-compose.simple.yml exec app php bin/console cache:clear

# Migrations
sudo docker-compose -f docker-compose.simple.yml exec app php bin/console doctrine:migrations:migrate

# Créer un utilisateur admin (si applicable)
sudo docker-compose -f docker-compose.simple.yml exec app php bin/console app:create-admin
```

## 🔒 Sécurité

### Mesures Incluses
- ✅ Headers de sécurité HTTP
- ✅ Rate limiting (configuration production)
- ✅ Chiffrement SSL/TLS
- ✅ Isolation des conteneurs
- ✅ Utilisateurs non-root
- ✅ Clés JWT sécurisées

### À Faire en Production
1. Remplacez les certificats SSL auto-signés par des vrais certificats
2. Changez tous les mots de passe par défaut
3. Configurez un firewall
4. Activez les logs d'audit
5. Mettez à jour régulièrement

## 🚨 Dépannage

### Problèmes Courants

1. **Port déjà utilisé**
   ```bash
   # Changer le port dans docker-compose
   ports:
     - "8081:80"  # Au lieu de 8080:80
   ```

2. **Erreurs de permissions**
   ```bash
   sudo docker-compose exec app chown -R www-data:www-data /var/www/html/var
   ```

3. **Base de données non accessible**
   ```bash
   # Vérifier que le conteneur DB est démarré
   sudo docker-compose ps db
   ```

### Logs Détaillés
```bash
# Application
sudo docker-compose exec app tail -f /var/log/apache2/error.log

# Base de données
sudo docker-compose logs db

# Tous les services
sudo docker-compose logs -f
```

## 📈 Optimisations Incluses

### Performance
- **OPcache** : Cache des opcodes PHP activé
- **Compression** : Gzip activé sur Nginx
- **Cache statique** : Headers de cache pour les assets
- **Multi-stage build** : Images Docker optimisées

### Monitoring
- **Health checks** : Vérification automatique de l'état
- **Logs structurés** : Logs centralisés
- **Métriques** : Prêt pour Prometheus/Grafana

## 🔄 Mise à Jour

Pour mettre à jour l'application :

```bash
# 1. Récupérer les modifications
git pull origin main

# 2. Reconstruire et redéployer
sudo docker-compose -f docker-compose.simple.yml down
sudo docker-compose -f docker-compose.simple.yml up -d --build
```

## 📞 Support

### Commandes de Diagnostic
```bash
# État des conteneurs
sudo docker ps

# Utilisation des ressources
sudo docker stats

# Espace disque
sudo docker system df

# Nettoyer les ressources inutilisées
sudo docker system prune -f
```

### Fichiers de Configuration Importants
- `docker-compose.simple.yml` - Configuration principale
- `.env` - Variables d'environnement
- `docker/apache/vhost.conf` - Configuration Apache
- `docker/php/php.ini` - Configuration PHP

## ✅ Checklist de Déploiement

- [ ] Docker et Docker Compose installés
- [ ] Variables d'environnement configurées dans `.env`
- [ ] Clés JWT générées
- [ ] Ports disponibles (8080 pour simple, 80/443 pour production)
- [ ] Application démarrée avec `docker-compose up -d`
- [ ] Health check réussi : `curl http://localhost:8080/health`
- [ ] Interface accessible : `http://localhost:8080`

## 🎉 Félicitations !

Votre application Symfony est maintenant dockerisée et prête pour le déploiement. La configuration est optimisée pour la production avec toutes les bonnes pratiques de sécurité et de performance.

Pour toute question ou problème, consultez les logs avec les commandes fournies ci-dessus.

