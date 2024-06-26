# Symfony Rest Api

# Files
Run this command in where you want to install Symfony<br>
```sudo docker run --rm -v $(pwd):/app composer create-project symfony/website-skeleton project_name```
<br/>
<br/>
To create the docker containers <br/>
```sudo docker-compose build --no-cache```
<br/>
<br/>
Up container<br/>
```sudo docker-compose up```

The file ```.env``` it should be ignored in git but to show you the config.

## Create Database

To create the database tables: <br/>
```php bin/console doctrine:migrations:migrate``` <br/>
<br/>
<br/>
To populate the database with fake data: <br/>
```php bin/console doctrine:fixtures:load```
<br/>
<br/>
## Test Api
```php bin/phpunit tests/Controller/ApiControllerTest.php```
