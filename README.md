# Share package for distribution financing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/atInisiatifZakat/distribution-financings.svg?style=flat-square)](https://packagist.org/packages/inisiatif/distribution-financings)
[![PHPUnit](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/run-tests.yml)
[![Laravel Pint](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/fix-php-code-style-issues.yml)
[![Psalm](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/run-psalm-static-analyst.yml/badge.svg?branch=main)](https://github.com/atInisiatifZakat/distribution-financings/actions/workflows/run-psalm-static-analyst.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/atInisiatifZakat/distribution-financings.svg?style=flat-square)](https://packagist.org/packages/inisiatif/distribution-financings)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require atInisiatifZakat/distribution-financings
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="distribution-financings-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="distribution-financings-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="distribution-financings-views"
```

## Usage

```php
$financing = new Inisiatif\\Distribution\\Financings\Financing();
echo $financing->echoPhrase('Hello, Inisiatif\\Distribution\\Financings!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Annisa Nadia N](https://github.com/nadiannisaqilah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
