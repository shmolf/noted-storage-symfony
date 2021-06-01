# Summary

We'll be using Doctrin + MySQL for our setup. But Doctrine can integrate with other Databases besides MySQL.

# Database

Need to create a new Schema whereby the application (Doctrine) will auto-generate tables.

<details>
<summary>Example with placeholders</summary>

```sql
CREATE USER '<your-app-user>'@'localhost' IDENTIFIED WITH mysql_native_password BY '<your-app-password';
DROP DATABASE IF EXISTS <your-new-schema>;
CREATE DATABASE <your-new-schema> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON <your-new-schema>.* TO '<your-app-user>'@'localhost';
```
</details>

<details>
<summary>More realistic example</summary>

```sql
CREATE USER 'noteduser'@'localhost' IDENTIFIED WITH mysql_native_password BY 'passwordofmanycharacters';
DROP DATABASE IF EXISTS notedapp;
CREATE DATABASE notedapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON notedapp.* TO 'noteduser'@'localhost';
```
</details>

# Doctrine
[Symfony Doc](https://symfony.com/doc/4.4/doctrine.html)
```bash
composer require symfony/orm-pack && composer require --dev symfony/maker-bundle
```

Copy the generated `.env` file as `.env.local`, and update the `DATABASE_URL` environment variable based on your database & credentials.

From the Symfony Documentation, you can skip the database creation step, since we achieved that ourselves.
- The reason we handle that manually, is for the proper UTF8 support.

# Security
[Symfony Doc](https://symfony.com/doc/4.4/security.html)
```bash
composer require symfony/security-bundle
```

Create the User Entity
```bash
php bin/console make:user
```
Press `Enter` all the way through, to accept the defaults.

Then, create the Migration...
```bash
php bin/console make:migration
```
...and Migrate the database.
```bash
php bin/console doctrine:migrations:migrate
```
