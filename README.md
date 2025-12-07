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

// Include public domains when needed
Sift::domain('user@gmail.com', true); // 'gmail.com'

// Check if a domain is a public provider
Sift::isCommon('gmail.com');          // true
Sift::isCommon('company.com');        // false
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
Sift::domain('john.doe@example.com');  // 'example.com'
Sift::domain('support@sub.domain.org'); // 'sub.domain.org'
```

### Smart Filtering

Public email providers are filtered by default. The package includes 70+ common providers:

```php
// These return null (filtered as public providers)
Sift::domain('user@gmail.com');
Sift::domain('user@yahoo.com');
Sift::domain('user@outlook.com');
Sift::domain('user@hotmail.com');

// Business domains pass through
Sift::domain('user@stripe.com');    // 'stripe.com'
Sift::domain('user@company.io');    // 'company.io'
```

### Domain Checking

Check if a domain or email belongs to a public provider:

```php
Sift::isCommon('gmail.com');           // true
Sift::isCommon('user@protonmail.com'); // true
Sift::isCommon('company.com');         // false
```

### Case Insensitive

All comparisons are case-insensitive:

```php
Sift::domain('User@GMAIL.COM');    // null
Sift::domain('User@Company.COM');  // 'company.com'
Sift::isCommon('YAHOO.COM');       // true
```

### Blade Support

Use directly in Blade templates:

```blade
@if(Sift::domain($user->email))
    <span>{{ Sift::domain($user->email) }}</span>
@else
    <span class="text-muted">Personal email</span>
@endif
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=sift-config
```

```php
// config/sift.php
return [
    /*
    |--------------------------------------------------------------------------
    | Additional Domains
    |--------------------------------------------------------------------------
    |
    | Add custom domains to filter alongside the built-in common domains.
    | Useful for filtering industry-specific providers or internal domains
    | you want to exclude from business email detection.
    |
    */
    'additional_domains' => [
        // 'competitor.com',
        // 'internal-tool.io',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common Domains
    |--------------------------------------------------------------------------
    |
    | Pre-populated list of public email providers. Includes major providers
    | worldwide: Gmail, Yahoo, Outlook, Hotmail, iCloud, ProtonMail, and 60+
    | regional and temporary email services.
    |
    | You can modify this list to match your specific requirements.
    |
    */
    'common_domains' => [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com',
        // ... 70+ providers included
    ],
];
```

## Use Cases

### Lead Form Validation

```php
public function store(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $domain = Sift::domain($request->email);

    if (!$domain) {
        return back()->withErrors([
            'email' => 'Please use your business email address.'
        ]);
    }

    Lead::create([
        'email' => $request->email,
        'company_domain' => $domain,
    ]);
}
```

### User Grouping

```php
$users = User::all()->groupBy(function ($user) {
    return Sift::domain($user->email, true) ?? 'personal';
});

// Results in:
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
    // Business emails get enterprise pricing
    if (Sift::domain($user->email)) {
        return 20; // 20% discount
    }

    return 0;
}
```

## Testing

```bash
composer test
```

## License

MIT