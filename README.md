# PHP/MySQL 2-Tier CRUD Application

This application provides create, read, update, and delete operations for an `items` table in Amazon RDS MySQL.

## 1. Create the database table

Connect to RDS with MySQL Client or MySQL Workbench, then run `schema.sql`.

## 2. Configure Apache environment variables

Add the following to the Apache virtual host or server configuration, replacing the password:

```apache
SetEnv DB_HOST "database-2tier.c21muigc2r4y.us-east-1.rds.amazonaws.com"
SetEnv DB_PORT "3306"
SetEnv DB_USER "admin"
SetEnv DB_PASSWORD "your-new-rds-password"
SetEnv DB_NAME "database-2tier"
```

Restart Apache after configuration:

```bash
sudo systemctl restart httpd
```

For PHP-FPM, environment variables in the Apache configuration may require passing them through your FPM pool configuration instead. Alternatively, configure the variables as EC2 instance environment variables through your deployment or secret-management process.

## 3. Deploy the files

Copy these files to the Apache web root, commonly `/var/www/html`:

- `index.php`
- `db.php`
- `style.css`
- `schema.sql`
- `.env.example`
- `.gitignore`
- `README.md`

## Security note

The database password previously exposed in the application source and chat should be considered compromised. Rotate it in RDS, update Apache configuration, and never commit credentials to Git.
