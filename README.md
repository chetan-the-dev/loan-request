## Loan Request Api

This is sample api for customer to apply for loan. Which has below features.

## Features
-> Customer can apply for loan with tenure selection
-> Admin will approve loan request
-> When customer paid all EMI loan status will be autometicaly updated

## Installation Instructions

- Run `composer install`
- Run `cp .env.example .env`
- Run `php artisan key:generate`
- Run `php artisan migrate`
- Run `php artisan serve`
- Run `php artisan passport:install`
- Run `php artisan passport:keys`

## Postman Collection

- [Postman Collection](https://www.getpostman.com/collections/1865a4ef920033776cef)

## Third-party Packages Used

- [Laravel Passport](https://laravel.com/docs/passport)
