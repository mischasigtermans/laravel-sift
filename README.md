# Laravel Sift

Lightweight email domain extraction and filtering for Laravel. Automatically distinguishes business emails from public providers.

Sift extracts domains from email addresses and filters out common public providers (Gmail, Yahoo, Outlook, etc.), making it easy to identify business email domains for lead qualification, analytics, or validation workflows.

## Installation

```bash
composer require mischasigtermans/laravel-sift
```

## Quick Start

```php
use MischaSigtermans\Sift\Facades\Sift;

// Extract business domains (filters public providers by default)
Sift::domain('user@company.com');     // 'company.com'
Sift::domain('user@gmail.com');       // null (filtered)

// Check if email is business or personal
Sift::isBusiness('user@company.com'); // true
Sift::isBusiness('user@gmail.com');   // false

// Validate in form requests
$request->validate([
    'email' => ['required', 'email', Sift::rule()],
]);
```

## Why Sift?

When collecting emails from users, you often need to distinguish between business and personal addresses. Public email providers like Gmail and Yahoo don't tell you anything about the user's company, while business domains (`user@acme.com`) identify the organization.

**Common use cases:**

- **Lead qualification**: Filter out personal emails to focus on business leads
- **Domain analytics**: Group users by company domain
- **B2B validation**: Ensure only business emails are accepted
- **CRM enrichment**: Extract company domains for account matching

## Features

### Domain Extraction

Extract the domain portion from any email address:

```php
Sift::domain('john.doe@example.com');   // 'example.com'
Sift::domain('support@sub.domain.org'); // 'sub.domain.org'
```

### Smart Filtering

Public email providers are filtered by default. The package includes 100+ common providers:

```php
// These return null (filtered as public providers)
Sift::domain('user@gmail.com');
Sift::domain('user@yahoo.com');
Sift::domain('user@outlook.com');

// Business domains pass through
Sift::domain('user@stripe.com');    // 'stripe.com'
Sift::domain('user@company.io');    // 'company.io'

// Include public domains when needed
Sift::domain('user@gmail.com', true); // 'gmail.com'
```

### Domain Checking

Check if a domain or email belongs to a public provider:

```php
Sift::isCommon('gmail.com');           // true
Sift::isCommon('user@protonmail.com'); // true
Sift::isCommon('company.com');         // false

// Or check if it's a business email
Sift::isBusiness('user@company.com');  // true
Sift::isBusiness('user@gmail.com');    // false
```

### Validation Rule

Require business email addresses in form requests:

```php
use MischaSigtermans\Sift\Rules\BusinessEmail;

// Using the rule class directly
$request->validate([
    'email' => ['required', 'email', new BusinessEmail],
]);

// Or via the facade helper
$request->validate([
    'email' => ['required', 'email', Sift::rule()],
]);
```

Error message: "The email must be a business email address."

### Batch Processing

Process multiple emails at once:

```php
$emails = [
    'john@acme.com',
    'jane@stripe.com',
    'bob@gmail.com',
    'alice@acme.com',
];

// Extract unique business domains (filters personal emails)
Sift::domains($emails);
// ['acme.com', 'stripe.com']

// Extract all unique domains without filtering
Sift::extractAll($emails);
// ['acme.com', 'stripe.com', 'gmail.com']

// Include personal domains
Sift::domains($emails, includeCommon: true);
// ['acme.com', 'stripe.com', 'gmail.com']
```

### Statistics

Analyze email collections:

```php
$emails = [
    'john@acme.com',
    'jane@acme.com',
    'bob@stripe.com',
    'alice@gmail.com',
    'charlie@yahoo.com',
];

$stats = Sift::stats($emails);
// [
//     'total' => 5,
//     'business' => 3,
//     'personal' => 2,
//     'business_rate' => 60.0,
//     'top_domains' => [
//         'acme.com' => 2,
//         'stripe.com' => 1,
//     ],
// ]

// Limit top domains returned
$stats = Sift::stats($emails, topDomainsLimit: 5);
```

### Case Insensitive

All comparisons are case-insensitive:

```php
Sift::domain('User@GMAIL.COM');   // null
Sift::domain('User@Company.COM'); // 'company.com'
Sift::isCommon('YAHOO.COM');      // true
```

### Blade Support

Use directly in Blade templates:

```blade
@if(Sift::isBusiness($user->email))
    <span>{{ Sift::domain($user->email) }}</span>
@else
    <span class="text-muted">Personal email</span>
@endif
```

## Configuration

The package includes 100+ public email providers by default. You automatically get updates when the package is updated.

Publish the config file to customize:

```bash
php artisan vendor:publish --tag=sift-config
```

```php
// config/sift.php
return [
    // Add extra domains to filter (on top of package defaults)
    'additional_domains' => [
        'competitor.com',
        'internal-tool.io',
    ],

    // Whitelist specific defaults (allow them as business emails)
    'exclude_default_domains' => [
        'protonmail.com', // Allow privacy-focused provider
        'fastmail.com',
    ],
];
```

### View All Default Domains

```php
use MischaSigtermans\Sift\DefaultDomains;

// Get the full list of 100+ default domains
DefaultDomains::LIST;

// Or get the merged list (defaults + additional - excluded)
Sift::getCommonDomains();
```

## Use Cases

### Lead Form Validation

```php
public function store(Request $request)
{
    $request->validate([
        'email' => ['required', 'email', Sift::rule()],
    ]);

    Lead::create([
        'email' => $request->email,
        'company_domain' => Sift::domain($request->email),
    ]);
}
```

### User Analytics Dashboard

```php
public function emailStats()
{
    $emails = User::pluck('email');

    return Sift::stats($emails);
    // Shows business vs personal breakdown with top company domains
}
```

### User Grouping

```php
$users = User::all()->groupBy(function ($user) {
    return Sift::domain($user->email, true) ?? 'personal';
});

// [
//     'acme.com' => [...users from acme.com...],
//     'stripe.com' => [...users from stripe.com...],
//     'personal' => [...users with gmail, yahoo, etc...],
// ]
```

### Conditional Pricing

```php
public function calculateDiscount(User $user): int
{
    if (Sift::isBusiness($user->email)) {
        return 20; // 20% enterprise discount
    }

    return 0;
}
```

## Testing

```bash
composer test
```

## Requirements

- PHP 8.2+
- Laravel 9, 10, 11, or 12

## License

MIT