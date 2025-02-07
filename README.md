# Laravel API JWT ▲

## Introduction

This repository is an implementation of the [Laravel JWT](https://packagist.org/packages/tymon/jwt-auth) Laravel backend.

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
