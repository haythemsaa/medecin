# Guide de Déploiement - Seha Digital v4.0

Guide complet pour déployer Seha Digital en production.

## 📋 Prérequis

### Infrastructure

- **Serveur Web**: Nginx ou Apache
- **PHP**: 8.3+
- **PostgreSQL**: 16+
- **Redis**: 7+
- **Node.js**: 18+ (pour build frontend)
- **SSL/TLS**: Certificat HTTPS
- **RAM**: Minimum 4GB (8GB recommandé)
- **Stockage**: Minimum 50GB SSD

### Services Tiers

- **Google Calendar** (optionnel)
- **Microsoft Outlook** (optionnel)
- **SMS Provider** (Tunisie Télécom, Ooredoo, Orange)
- **Payment Gateway** (SMT, e-Dinar)
- **Email Service** (SMTP)

---

## 🚀 Étape 1: Préparer le Serveur

### 1.1 Installer les dépendances

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3
sudo apt install -y php8.3 php8.3-fpm php8.3-pgsql php8.3-redis \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
  php8.3-intl php8.3-bcmath

# Install PostgreSQL 16
sudo apt install -y postgresql-16 postgresql-contrib-16

# Install Redis
sudo apt install -y redis-server

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx
```

### 1.2 Configurer PostgreSQL

```bash
# Create database and user
sudo -u postgres psql

CREATE DATABASE sehadigital;
CREATE USER sehadigital WITH PASSWORD 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON DATABASE sehadigital TO sehadigital;
ALTER DATABASE sehadigital OWNER TO sehadigital;
\q
```

### 1.3 Configurer Redis

```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Set password
requirepass YOUR_REDIS_PASSWORD

# Restart Redis
sudo systemctl restart redis-server
```

---

## 📦 Étape 2: Déployer le Backend

### 2.1 Cloner le repository

```bash
cd /var/www
sudo git clone https://github.com/haythemsaa/medecin.git sehadigital
cd sehadigital/backend
sudo chown -R www-data:www-data /var/www/sehadigital
```

### 2.2 Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 2.3 Configuration .env

```bash
cp .env.example .env
nano .env
```

**Configuration minimale**:
```env
APP_NAME="Seha Digital"
APP_ENV=production
APP_KEY=  # Généré plus tard
APP_DEBUG=false
APP_URL=https://sehadigital.tn

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=sehadigital
DB_USERNAME=sehadigital
DB_PASSWORD=YOUR_DB_PASSWORD

REDIS_HOST=localhost
REDIS_PASSWORD=YOUR_REDIS_PASSWORD

# OAuth (si utilisé)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=

# SMS & Payment
SMS_PROVIDER=tunisie_telecom
SMS_API_KEY=
PAYMENT_GATEWAY=smt
PAYMENT_API_KEY=
```

### 2.4 Générer la clé d'application

```bash
php artisan key:generate
```

### 2.5 Exécuter les migrations

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 2.6 Optimiser pour production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2.7 Configurer les permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🌐 Étape 3: Déployer le Frontend

### 3.1 Build le frontend

```bash
cd /var/www/sehadigital/frontend
npm install
npm run build
```

Le dossier `dist/` contiendra les fichiers statiques à servir.

### 3.2 Copier vers le répertoire public

```bash
sudo mkdir -p /var/www/sehadigital/public
sudo cp -r dist/* /var/www/sehadigital/public/
```

---

## ⚙️ Étape 4: Configurer Nginx

### 4.1 Créer la configuration

```bash
sudo nano /etc/nginx/sites-available/sehadigital
```

**Configuration**:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name sehadigital.tn www.sehadigital.tn;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name sehadigital.tn www.sehadigital.tn;

    root /var/www/sehadigital/public;
    index index.html index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/sehadigital.tn/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/sehadigital.tn/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;

    # API routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Frontend routes (SPA)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

### 4.2 Activer le site

```bash
sudo ln -s /etc/nginx/sites-available/sehadigital /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔐 Étape 5: SSL avec Let's Encrypt

```bash
# Install certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d sehadigital.tn -d www.sehadigital.tn

# Auto-renewal
sudo certbot renew --dry-run
```

---

## ⏰ Étape 6: Configurer les Cron Jobs

```bash
sudo crontab -e
```

Ajouter:
```cron
# Laravel Scheduler
* * * * * cd /var/www/sehadigital/backend && php artisan schedule:run >> /dev/null 2>&1

# Appointment Reminders (every 5 minutes)
*/5 * * * * cd /var/www/sehadigital/backend && php artisan reminders:send >> /dev/null 2>&1

# Medication Reminders (every 15 minutes)
*/15 * * * * cd /var/www/sehadigital/backend && php artisan reminders:send-medications >> /dev/null 2>&1

# Database Backup (daily at 2 AM)
0 2 * * * pg_dump sehadigital > /var/backups/sehadigital_$(date +\%Y\%m\%d).sql
```

---

## 🔄 Étape 7: Queue Workers

### 7.1 Créer le service

```bash
sudo nano /etc/systemd/system/sehadigital-worker.service
```

**Contenu**:
```ini
[Unit]
Description=Seha Digital Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/sehadigital/backend
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

### 7.2 Démarrer le service

```bash
sudo systemctl daemon-reload
sudo systemctl enable sehadigital-worker
sudo systemctl start sehadigital-worker
```

---

## 📊 Étape 8: Monitoring & Logs

### 8.1 Configurer les logs

```bash
# Laravel logs
sudo mkdir -p /var/log/sehadigital
sudo chown www-data:www-data /var/log/sehadigital

# Configure Laravel logging
# Dans .env
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### 8.2 Logrotate

```bash
sudo nano /etc/logrotate.d/sehadigital
```

**Contenu**:
```
/var/www/sehadigital/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## 🛡️ Étape 9: Sécurité

### 9.1 Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 9.2 Fail2ban

```bash
sudo apt install -y fail2ban

# Configure Nginx jail
sudo nano /etc/fail2ban/jail.local
```

**Contenu**:
```ini
[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 5
findtime = 600
bantime = 3600
```

### 9.3 Database Backup

Script automatique:
```bash
sudo nano /usr/local/bin/backup-sehadigital.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/sehadigital"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Database backup
pg_dump sehadigital | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/sehadigital/backend/storage

# Keep only last 30 days
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-sehadigital.sh
```

---

## ✅ Étape 10: Post-Déploiement

### 10.1 Vérifications

```bash
# Check services
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status postgresql
sudo systemctl status redis-server
sudo systemctl status sehadigital-worker

# Check logs
tail -f /var/www/sehadigital/backend/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Health check
curl https://sehadigital.tn/api/health
```

### 10.2 Tests

1. Créer un compte patient
2. Créer un compte médecin
3. Prendre un rendez-vous
4. Effectuer une consultation test
5. Tester le paiement (sandbox)
6. Vérifier les notifications email/SMS

### 10.3 Performance

```bash
# Install and run Laravel Telescope (development only)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Cache optimization
php artisan optimize
```

---

## 🔄 Mise à Jour (Update)

Pour mettre à jour vers une nouvelle version:

```bash
cd /var/www/sehadigital

# Backup first!
/usr/local/bin/backup-sehadigital.sh

# Pull latest code
git pull origin main

# Backend
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
cd ../frontend
npm install
npm run build
sudo cp -r dist/* /var/www/sehadigital/public/

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl restart sehadigital-worker
sudo systemctl reload nginx
```

---

## 📈 Monitoring Recommandé

### Services à intégrer

1. **Uptime Monitoring**: UptimeRobot, Pingdom
2. **Error Tracking**: Sentry, Bugsnag
3. **Performance**: New Relic, DataDog
4. **Analytics**: Google Analytics, Matomo

### Métriques Clés

- Response Time API (<200ms)
- Database Query Time (<50ms)
- Queue Processing Rate
- Error Rate (<1%)
- Uptime (>99.9%)

---

## 🆘 Dépannage

### Redis not connecting

```bash
sudo systemctl status redis-server
redis-cli ping
```

### Queue workers not processing

```bash
sudo systemctl restart sehadigital-worker
php artisan queue:restart
```

### 500 Internal Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check PHP-FPM
sudo tail -f /var/log/php8.3-fpm.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Database connection failed

```bash
# Test connection
psql -U sehadigital -h localhost -d sehadigital

# Check pg_hba.conf
sudo nano /etc/postgresql/16/main/pg_hba.conf
```

---

## 📞 Support

Pour assistance technique:
- Email: support@sehadigital.tn
- Documentation: https://docs.sehadigital.tn
- Github Issues: https://github.com/haythemsaa/medecin/issues

---

**Déploiement réussi ! 🎉**

*Dernière mise à jour: 17 novembre 2025*
