# Todo List API

## Installation

### Requirements

1) PHP 7 or greater

### Steps

1) Clone the repository
2) Install composer dependencies with `composer install`
3) Create a new file and name it `.env`
4) Copy the `.env.example` content into `.env`
5) Generate the application key with `php artisan key:generate`
6) Generate the JWT Key with `php artisan jwt:secret`
7) Update the database information in the `.env` file
8) Run the migrations with `php artisan migrate`
