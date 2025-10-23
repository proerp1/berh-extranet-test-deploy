# Suggested Commands

## Database Migrations
```bash
php vendor/bin/phinx migrate
php vendor/bin/phinx rollback
php vendor/bin/phinx create MigrationName
```

## Composer
```bash
php composer.phar install
php composer.phar update
```

## Git Remotes
```bash
# Production
git remote add prod BERH@52.4.33.164:/var/www/vhosts/berh.com.br/httpdocs/repo/extranet.git

# Homologation
git remote add homolog BERH@54.210.157.31:/var/www/vhosts/homologacao.berh.com.br/repo/extranet.git
```

## Development
- Entry point: index.php
- Local config: config.local.php (based on config.local.php.sample)