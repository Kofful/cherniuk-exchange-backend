# cherniuk-exchange-backend

##Set up the project

### Clone project:
```
git clone https://gitlab.nixdev.co/php-skillup/cherniuk-exchange-backend
```

### Start docker container:
```
docker-compose up -d
```
### Install composer dependencies
```
composer install
```
### Make migrations
```
docker-compose exec php bin/console doctrine:migrations:migrate
```
### Generate keys for jwt
```
docker-compose exec php bin/console lexik:jwt:generate-keypair
```
### Enjoy coding!