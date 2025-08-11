# Déploiement Docker - Application Symfony Handmades

Ce guide vous explique comment déployer votre application Symfony 5.4 avec Docker.

## 📋 Prérequis

- Docker (version 20.10+)
- Docker Compose (version 1.29+)
- Git

## 🚀 Déploiement rapide

### 1. Configuration de l'environnement

Copiez le fichier d'environnement de production :
```bash
cp .env.prod .env
```

Modifiez les variables suivantes dans le fichier `.env` :
- `APP_SECRET` : Générez une clé secrète unique
- `JWT_PASSPHRASE` : Mot de passe pour les clés JWT
- `DATABASE_URL` : URL de la base de données (déjà configurée pour Docker)

### 2. Génération des clés JWT

Créez les clés JWT pour l'authentification :
```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

### 3. Déploiement automatique

Lancez le script de déploiement :
```bash
./deploy.sh
```

## 🔧 Déploiement manuel

### 1. Construction des images

```bash
docker-compose -f docker-compose.prod.yml build
```

### 2. Démarrage des services

```bash
docker-compose -f docker-compose.prod.yml up -d
```

### 3. Vérification

```bash
curl http://localhost/health
```

## 📊 Architecture

L'application utilise une architecture multi-conteneurs :

- **app** : Application Symfony avec PHP-FPM et Nginx
- **db** : Base de données PostgreSQL
- **nginx** : Proxy inverse avec SSL/TLS
- **redis** : Cache et sessions

## 🛠️ Commandes utiles

### Logs
```bash
# Tous les services
docker-compose -f docker-compose.prod.yml logs -f

# Service spécifique
docker-compose -f docker-compose.prod.yml logs -f app
```

### Accès aux conteneurs
```bash
# Application Symfony
docker-compose -f docker-compose.prod.yml exec app sh

# Base de données
docker-compose -f docker-compose.prod.yml exec db psql -U symfony handmades_db
```

### Commandes Symfony
```bash
# Cache
docker-compose -f docker-compose.prod.yml exec app php bin/console cache:clear

# Migrations
docker-compose -f docker-compose.prod.yml exec app php bin/console doctrine:migrations:migrate

# Fixtures (développement uniquement)
docker-compose -f docker-compose.prod.yml exec app php bin/console doctrine:fixtures:load
```

### Gestion des services
```bash
# Redémarrer
docker-compose -f docker-compose.prod.yml restart

# Arrêter
docker-compose -f docker-compose.prod.yml down

# Arrêter et supprimer les volumes
docker-compose -f docker-compose.prod.yml down -v
```

## 🔒 Configuration SSL/TLS

Pour activer HTTPS en production :

1. Placez vos certificats SSL dans `docker/ssl/` :
   - `cert.pem` : Certificat SSL
   - `key.pem` : Clé privée

2. Ou générez des certificats auto-signés pour les tests :
```bash
mkdir -p docker/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout docker/ssl/key.pem \
    -out docker/ssl/cert.pem \
    -subj "/C=FR/ST=France/L=Paris/O=Handmades/CN=localhost"
```

## 🔍 Monitoring et santé

### Health Check
L'application expose un endpoint de santé :
```bash
curl http://localhost/health
```

### Métriques
```bash
# Utilisation des ressources
docker stats

# Espace disque des volumes
docker system df
```

## 🐛 Dépannage

### Problèmes courants

1. **Port 80 déjà utilisé**
   ```bash
   # Changer le port dans docker-compose.prod.yml
   ports:
     - "8080:80"
   ```

2. **Erreurs de permissions**
   ```bash
   docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/var
   ```

3. **Base de données non accessible**
   ```bash
   # Vérifier que le conteneur DB est démarré
   docker-compose -f docker-compose.prod.yml ps db
   
   # Vérifier les logs
   docker-compose -f docker-compose.prod.yml logs db
   ```

### Logs détaillés
```bash
# Application
docker-compose -f docker-compose.prod.yml exec app tail -f /var/www/html/var/log/prod.log

# Nginx
docker-compose -f docker-compose.prod.yml exec nginx tail -f /var/log/nginx/error.log

# PHP-FPM
docker-compose -f docker-compose.prod.yml exec app tail -f /var/log/php_errors.log
```

## 🔄 Mise à jour

Pour mettre à jour l'application :

1. Récupérez les dernières modifications :
   ```bash
   git pull origin main
   ```

2. Reconstruisez et redéployez :
   ```bash
   ./deploy.sh
   ```

## 📈 Performance

### Optimisations incluses

- **OPcache** : Cache des opcodes PHP activé
- **Nginx** : Compression gzip et cache des fichiers statiques
- **Redis** : Cache des sessions et données
- **Multi-stage build** : Images Docker optimisées

### Monitoring des performances
```bash
# Temps de réponse
curl -w "@curl-format.txt" -o /dev/null -s http://localhost/

# Utilisation mémoire PHP
docker-compose -f docker-compose.prod.yml exec app php -i | grep memory_limit
```

## 🔐 Sécurité

### Mesures de sécurité incluses

- Headers de sécurité HTTP
- Rate limiting sur les endpoints sensibles
- Isolation des conteneurs
- Utilisateur non-root dans les conteneurs
- Chiffrement des communications (HTTPS)

### Recommandations

1. Changez tous les mots de passe par défaut
2. Utilisez des certificats SSL valides en production
3. Configurez un firewall
4. Activez les logs d'audit
5. Mettez à jour régulièrement les images Docker

## 📞 Support

En cas de problème :

1. Vérifiez les logs avec `docker-compose logs`
2. Consultez la documentation Symfony officielle
3. Vérifiez les issues GitHub du projet

---

**Note** : Cette configuration est optimisée pour la production. Pour le développement, utilisez `docker-compose.yml` avec des paramètres adaptés.

