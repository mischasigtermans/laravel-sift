# Sift – Simple Email Domain Extraction & Filtering

Sift is a **Laravel package** for extracting and filtering email domains. Whether you're handling **user registrations**, **blocking public email providers**, or ensuring only **business emails** are used, **Sift** makes it simple.

## Features
- **Domain Extraction** → Extracts the domain from any email address.
- **Smart Filtering** → Automatically detects and filters out public/free email providers.
- **Handles Major Providers** → Includes Gmail, Yahoo, Outlook, and many more out of the box.
- **Customizable** → Easily modify the list of common domains in the config file.
- **Laravel-Optimized** → Designed for seamless integration with Laravel.

## Installation
Install Sift via Composer:

```sh
composer require mischasigtermans/laravel-sift
```

> **Note:** Laravel auto-discovers the package, so no manual setup is needed.

To publish the config file:

```sh
php artisan vendor:publish --tag=sift-config
```

This creates `config/sift.php`, allowing customization of filtered domains.

## Usage
### **Extract an email's domain**
```php
use Sift;

Sift::domain('user@example.com'); // Returns 'example.com'

// By default, public email domains are filtered:
Sift::domain('user@gmail.com'); // Returns null

// Allow public domains explicitly:
Sift::domain('user@gmail.com', true); // Returns 'gmail.com'

// Business email remains unaffected:
Sift::domain('user@company.com'); // Returns 'company.com'
```

### **Check if a domain is common**
```php
Sift::isCommon('gmail.com'); // true
Sift::isCommon('user@company.com'); // false
Sift::isCommon('invalid-email'); // null
```

### **Blade Example**
```blade
{{ Sift::domain('hello@company.org') }}
```

## Configuration
Modify the common domain list in `config/sift.php` after publishing the config:

```php
return [
    'additional_domains' => [
        // 'example.org', 'testmail.com'
    ],
    'common_domains' => [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com',
        'protonmail.com', 'zoho.com', 'gmx.com', 'aol.com', 'yandex.com', 
        'qq.com', 'live.com', 'rediffmail.com', 'mail.com', 'bigmir.net',
        // (Full list continues...)
    ],
];
```
View the full list of common domains in the configuration file [here](https://github.com/mischasigtermans/laravel-sift/blob/main/config/sift.php).

## Running Tests
Sift includes lightweight but effective tests using Pest. To run them:

```sh
vendor/bin/pest
```

## Contributing
Contributions are welcome. If you spot missing providers or have improvements, feel free to:
- Open an issue on GitHub
- Submit a pull request

## License
Sift is open-source software licensed under the MIT License.
