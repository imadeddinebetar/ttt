# Linux Development Environment Installation Guide

This guide walks you through setting up a secure Linux-based development environment with **Nginx, MySQL, PHP (LEMP stack)**, firewall hardening, and SSL using Certbot.

---

## 1. System Preparation

```bash
apt update
apt install sudo nano curl -y
```

### Create a New User
```bash
adduser imad
usermod -aG sudo imad
```

Log out and log back in as the new user.

---

## 2. Firewall Configuration (UFW)

```bash
sudo apt update && sudo apt install ufw -y
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 3. Install Web Stack (LEMP)

### Nginx
```bash
sudo apt install nginx -y
sudo service nginx start
```

### MySQL
```bash
sudo apt install mysql-server -y
sudo service mysql start
sudo service mysql status
```

### PHP
```bash
sudo apt install php-fpm php-mysql -y
```

---

## 4. Project Structure

```bash
sudo mkdir -p /var/www/tcrcm/public
cd /var/www/tcrcm/public
```

### Test PHP File
```bash
echo "<?php echo '<h1>TCRCM Project is Working</h1>'; phpinfo(); ?>" > index.php
```

---

## 5. Nginx Virtual Host Configuration

Create config:
```bash
sudo nano /etc/nginx/sites-available/tcrcm
```

Paste:
```nginx
server {
    listen 80 default_server;
    server_name _;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    root /var/www/tcrcm/public;
    index index.php index.html;

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* /(config|tests|vendor)/.*\.php$ {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_hide_header X-Powered-By;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/tcrcm /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo service php8.1-fpm start
sudo service nginx restart
```

---

## 6. Permissions

```bash
sudo chown -R www-data:www-data /var/www/tcrcm
sudo find /var/www/tcrcm -type d -exec chmod 755 {} \;
sudo find /var/www/tcrcm -type f -exec chmod 644 {} \;
```

---

## 7. Security Hardening

### Fail2Ban
```bash
sudo apt install fail2ban -y
```

### Automatic Security Updates
```bash
sudo apt install unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

---

## 8. Database Setup

```bash
sudo mysql
```

```sql
CREATE DATABASE tcrcm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tcrcm_user'@'localhost' IDENTIFIED BY 'Strong_Password_Here';
GRANT ALL PRIVILEGES ON tcrcm_db.* TO 'tcrcm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
sudo chmod 755 /var/run/mysqld
```

---

## 9. Secure PHP Database Test

```php
<?php
$host = '127.0.0.1';
$db   = 'tcrcm_db';
$user = 'tcrcm_admin';
$pass = 'Strong_Password_123';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);

    echo "<h1 style='color: navy;'>TCRCM Secure Environment</h1>";
    echo "<p style='color: green; font-weight: bold;'>✅ Web Server & PHP OK</p>";
    echo "<p style='color: green; font-weight: bold;'>✅ Database Connected</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ " . $e->getMessage() . "</p>";
}
?>
```

---

## 10. SSL with Certbot (HTTPS)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

---

## ✅ Final Result

- Secure Linux server
- Nginx + PHP 8.1 + MySQL
- Hardened firewall & SSH protection
- Automatic security updates
- HTTPS enabled

---

**Environment ready for production or development use.**
