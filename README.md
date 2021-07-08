## Table of contents
* [Technologies](#technologies)
* [Setup](#setup)

## General info
This project is an employment portal.  
	
## Technologies
Project is created with:
* Laravel version : 8

	
## Setup
To run this project, install it locally:

```
$ cd ../project_directory
$ git clone https://dusan-nikcevic@bitbucket.org/vebcentar/cvprica-api.git
$ composer install --ignore-platform-reqs
$ composer require doctrine/dbal
$ create .env file and enter nesscessary data
$ php artisan key:generate
$ php artisan migrate
$ php artisan passport:install
$ php artisan db:seed

