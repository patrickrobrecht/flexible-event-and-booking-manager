# Flexible Event and Booking Manager

![PHP](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2Fpatrickrobrecht%2Fflexible-event-and-booking-manager%2Fmaster%2Fcomposer.json&query=%24.require.php&label=PHP)
[![Code style](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/code-style.yml/badge.svg)](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/code-style.yml)
[![Tests](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/tests.yml/badge.svg)](https://github.com/patrickrobrecht/flexible-event-and-booking-manager/actions/workflows/tests.yml)

This application allows to manage events, their booking forms and bookings via a web-based platform.


## Features
- Manage events and event series, locations, and organizations
  - Events and series can have a parent event (series).
  - Events, series, locations, organizations can be requested via a read-only API.
- Manage booking options and their booking forms (for events without a parent event)
  - Users editing booking forms can see a preview for the booking forms if bookings are not enabled yet.
- Bookings
  - Confirmation via email
  - Guest bookings are supported, but can be forbidden by enabling the restriction to logged-in users in the settings of the booking option.
  - Users with the corresponding access rights can view, export, edit, delete and restore bookings.
    They can also add a booking comment and set the payment status.
- Group participants for events with bookings
  - Bookings are automatically grouped by their booking option.
  - If a date of birth is submitted, the age of each participant and average age of each group is shown.
  - Sub events have the bookings from parent event.
  - Generate groups (randomized or age-based)
- Manage responsibilities (responsible users with their position) for events, event series and organizations
- Add, update, delete documents for events, event series, and organizations
- Login and logout, reset password, verify e-mail address, edit own account
- Manage users and flexible roles
  - Notify users if an account was created for them (optional)
  - Registration (if enabled via `.env`) 
- Manage personal access tokens (for [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum))
- Footer links for legal notice, privacy, terms and conditions configurable via `.env`


## Development

### Requirements
To get started, you need to install the following software:
- [Composer](https://getcomposer.org/) to manage PHP dependencies,
- [PHP](https://www.php.net/), and the PDO extension for the database of your choice,
- a relational database, such as [MariaDB](https://mariadb.org/download/)

### Used technologies
- [Alpine.js](https://alpinejs.dev/) to extend Livewire with some additional JavaScript
- [Bladestrap](https://github.com/portavice/bladestrap), Blade components for Bootstrap
- [Bootstrap](https://getbootstrap.com/), a front-end toolkit
- [Font Awesome](https://github.com/FortAwesome/Font-Awesome) for [icons](https://fontawesome.com/icons?d=gallery&m=free)
- [Laravel](https://laravel.com/docs/12.x) framework
- [Laravel Dompdf](https://github.com/barryvdh/laravel-dompdf) for PDF export
- [Laravel Livewire](https://livewire.laravel.com/docs/) for dynamic UI without leading PHP
- [Laravel Sluggable](https://github.com/spatie/laravel-sluggable) to autogenerate slugs
- [Laravel Query Builder](https://spatie.be/docs/laravel-query-builder/v5/introduction) for custom filtering and sorting
- [Laravel Zipstream](https://github.com/stechstudio/laravel-zipstream) to create and stream zip files
- [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/en/stable/) for Excel exports

### How to develop
To setup/update your development environment:
- Run `composer install` to setup autoloading and install the development dependencies for PHP.
- Create/update the `.env` configuration if necessary (see [Configuration](#configuration) below).
- Run `php artisan migrate` to migrate the database to the latest version.
- Run `composer stan` to run static analysis with PHPStan.
- Run `composer test` to execute all the Unit tests.

For development, we use
- [ESlint](https://eslint.org/) for the JavaScript code style check
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) for debugging utility
- [Laravel Mix](https://laravel-mix.com/docs/) for compiling assets
- [Laravel Pint](https://laravel.com/docs/12.x/pint) for the PHP code style check
- [Laravel Translatable String Exporter](https://github.com/kkomelin/laravel-translatable-string-exporter)
- [PHPStan](https://phpstan.org/) for static analysis
- [PHPUnit](https://docs.phpunit.de/en/12.1/) for unit tests
- [Stylelint](https://stylelint.io/) for the SASS code style check

### Code Style
Run `composer cs` to check compliance with the code style
and `composer csfix` to fix code style violations before every commit
(see `composer.json` and `package.json` for commands to run the code style check/fix for just one language).
- PHP code MUST follow the [Pint configuration](./pint.json), including [PSR-12 specification](https://www.php-fig.org/psr/psr-12/).
- Laravel code SHOULD follow the best practices from the list [by Alexey Mezenin](https://github.com/alexeymezenin/laravel-best-practices).
- JavaScript code MUST follow [the default ESLint rules](https://eslint.org/docs/rules/).
- CSS code MUST follow [the standard stylelint rules](https://stylelint.io/user-guide/rules).

Any texts in the code MUST be in English.
Use `composer translate` to extract them to `lang/de.json`.

### How to release
- Update version in `CHANGELOG.md` and `config/app.php`.
- Create a tag in Git and publish corresponding release notes in GitLab.

### How to deploy
- Run `composer production` to remove development dependencies.
- Create/update the `.env` configuration if necessary.
- Run `php artisan migrate` to migrate the database to the latest version.
- Upload files to the production system.
- Optimize performance with caching for config, events, routes, views by running `php artisan optimize`.


## Configuration
- `APP_NAME` and `APP_OWNER` define name and owner shown in header or footer.
- `APP_DEFAULT_LOCALE` defines the locale to use for translations by default.
- `APP_URL_LEGAL_NOTICE`, `APP_URL_PRIVACY_STATEMENT`, and `APP_URL_TERMS_AND_CONDITIONS` can be set to URLs 
  for the respective pages which show up in the footer of the webpages and of the emails.
- `REGISTRATION_ENABLED=true` enables registration which is disabled by default.
