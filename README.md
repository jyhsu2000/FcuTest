# Laravel Base (5.3)
[![StyleCI(5.3)](https://styleci.io/repos/65561499/shield?branch=5.3)](https://styleci.io/repos/65561499)
[![License](https://img.shields.io/github/license/HackerSir/laravel-base.svg)](https://raw.githubusercontent.com/HackerSir/laravel-base/master/LICENSE)

A website base on Laravel and Bootstrap for HackerSir.

## Framework
- Laravel 5.3
- Bootstrap 4

## Including
- Packages
  - laravelcollective/html: "^5.3"
  - predis/predis: "^1.1"
  - barryvdh/laravel-ide-helper: "^2.2"
  - doctrine/dbal: "^2.5"
  - recca0120/laravel-tracy: "^1.5.6"
  - thomaswelton/laravel-gravatar: "^1.1"
  - klaravel/ntrust: "^1.1"
  - graham-campbell/throttle: "^5.2"
- System
  - User
  - Role
  - Permission

## Installation Guide
1. Run the following commands.
```bash
composer install  
npm install  
gulp
```

2. Configure environment variables in `.env`.

3. Run migrations to setup tables.
```bash
php artisan migrate
```

## TODO
- Tasks
  - [ ] AuthListener：Log time and ip when login
  - Unify page style
    - [ ] Normal pages
    - [ ] Form pages
- Packages
  - arcanedev/log-viewer: "~3.0"
  - laravolt/semantic-form: "^1.3"
  - lavary/laravel-menu: "dev-master"
  - landish/pagination: "^1.3"

## License
This project is open-source under the [MIT license](http://opensource.org/licenses/MIT).
