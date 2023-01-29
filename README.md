# Flexible Event and Booking Manager

[![Run tests and check code style](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/tests-and-code-style.yml/badge.svg)](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/tests-and-code-style.yml)

This application allows to manage events, their booking forms and bookings via a web-based platform.


## Features
- Manage events and event series, locations, and organizations
- Manage booking options and flexible booking forms which can be reused for multiple events
- Bookings (confirmed via email)
  - Guest bookings are supported, can be forbidden by enabling the restriction to logged-in users in the settings of the booking option.
  - Editing is limited to users with an administrative role.
  - Update booking comment and payment status
- Login and logout, reset password, verify e-mail address, edit own account
- User and user role management
- Manage personal access tokens (for [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum))
- Registration (if enabled via `.env`)
- Footer links for legal notice, privacy, terms and conditions configurable via `.env`


## Development

### Requirements
To get started, you need to install the following software:
- [Composer](https://getcomposer.org/) to manage PHP dependencies,
- [PHP](https://www.php.net/), and the PDO extension for the database of your choice,
- a relational database, such as [MariaDB](https://mariadb.org/download/)

### Used technologies
- [Bootstrap](https://getbootstrap.com/), a front-end toolkit
- [Font Awesome](https://github.com/FortAwesome/Font-Awesome) for [icons](https://fontawesome.com/icons?d=gallery&m=free)
- [Laravel](https://laravel.com/docs/9.x) framework
- [Laravel Query Builder](https://spatie.be/docs/laravel-query-builder/v5/introduction) for custom filtering and sorting

### How to develop
To setup/update your development environment:
- Run `composer install` to setup autoloading and install the development dependencies for PHP.
- Create/update the `.env` configuration if necessary (see [Configuration](#configuration) below).
- Run `php artisan migrate` to migrate the database to the latest version.

For development, we use
- [ESlint](https://eslint.org/) for the JavaScript code style check
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) for debugging utility
- [Laravel Mix](https://laravel-mix.com/docs/) for compiling assets
- [Laravel Translatable String Exporter](https://github.com/kkomelin/laravel-translatable-string-exporter)
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) for the PHP code style check.
- [Stylelint](https://stylelint.io/) for the SASS code style check

### Code Style
Run `composer cs` to check compliance with the code style
and `composer csfix` to fix code style violations before every commit
(see composer.json` and `package.json` for commands to run the code style check/fix for just one language).
- PHP code MUST follow [PSR-12 specification](https://www.php-fig.org/psr/psr-12/).
- Laravel code SHOULD follow the best practices from the list
  [by Alexey Mezenin](https://github.com/alexeymezenin/laravel-best-practices).
- JavaScript code MUST follow [the default ESLint rules](https://eslint.org/docs/rules/).
- CSS code MUST follow [the standard stylelint rules](https://stylelint.io/user-guide/rules).

Any texts in the code MUST be in English.
Use `composer translate` to extract them to `lang/de.json`.

### How to deploy
- Run `composer production` to remove development dependencies.
- Create/update the `.env` configuration if necessary.
- Run `php artisan migrate` to migrate the database to the latest version.
- Upload files to the production system.


## Configuration
- `APP_NAME` and `APP_OWNER` define name and owner shown in header or footer.
- `APP_DEFAULT_LOCALE` defines the locale to use for translations by default.
- `APP_URL_LEGAL_NOTICE`, `APP_URL_PRIVACY_STATEMENT`, and `APP_URL_TERMS_AND_CONDITIONS` can be set to URLs 
  for the respective pages which show up in the footer of the webpages and of the emails.
- `REGISTRATION_ENABLED=true` enables registration which is disabled by default.
