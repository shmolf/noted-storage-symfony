# Set up

## Database
If not using Docker, set up a local database.
```sql
CREATE USER 'notesstorageuser'@'%' IDENTIFIED BY 'my-secret-password';
CREATE DATABASE notedstorage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON notedstorage.* TO 'notesstorageuser'@'%';
```

Use this information to populate your DSN within the `.env.local` file.
Example:
```ini
DATABASE_URL='mysql://notesstorageuser:my-secret-password@127.0.0.1:3306/notedstorage?serverVersion=mariadb-10.5.12&charset=utf8"'
```

## Dependencies
```shell
composer install && yarn && yarn build
```
