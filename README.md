# Laravel API JWT ▲

## Introduction

This repository is an implementation of the [Laravel Breeze](https://laravel.com/docs/starter-kits) application / authentication starter kit frontend in [Next.js](https://nextjs.org). All of the authentication boilerplate is already written for you - powered by [Laravel Sanctum](https://laravel.com/docs/sanctum), allowing you to quickly begin pairing your beautiful Next.js frontend with a powerful Laravel backend.

## Official Documentation

### Installation

Laravel, clone this repository and install its dependencies with `composer install` or `composer update`. Then, copy the `.env.example` file to `.env.local` and supply the URL of your backend:

```
cd sample-api-project
```

```
cp .env.example .env
```

```
php artisan migrate
```

```
php artisan migrate:fresh --seed
```

```
php artisan key:generate
```

```
php artisan jwt:secret
```

```
php artisan serve
```
