# Upgrade Guide

## Upgrading from 0.2.x to 0.3.x

### Breaking Change: Config Structure

The `common_domains` config option has been removed. Default domains are now managed internally by the package, which means:

- You automatically get new providers when updating the package
- No more maintaining a 100+ item array in your config
- Simpler config with just two options

### Migration Steps

**If you never published the config:** No action needed.

**If you published the config:** Update your `config/sift.php`:

#### 1. Remove `common_domains`

Delete the entire `common_domains` array from your config.

#### 2. Add `exclude_default_domains`

Add the new config key (can be empty if you don't need to whitelist any defaults):

```php
// Before (0.2.x)
return [
    'additional_domains' => [
        // your custom domains...
    ],
    'common_domains' => [
        // 100+ domains...
    ],
];

// After (0.3.x)
return [
    'additional_domains' => [
        // your custom domains (unchanged)
    ],
    'exclude_default_domains' => [
        // new: whitelist specific defaults if needed
    ],
];
```

#### 3. If you customized `common_domains`

If you had modified the default list:
- Domains you **added** → keep in `additional_domains`
- Domains you **removed** → add to `exclude_default_domains`

```php
// Example: you removed protonmail.com and added competitor.com
return [
    'additional_domains' => [
        'competitor.com',
    ],
    'exclude_default_domains' => [
        'protonmail.com',
    ],
];
```

### New Features Available

After upgrading, you can use:

```php
// Validation rule
$request->validate([
    'email' => ['required', 'email', Sift::rule()],
]);

// Check if business email
Sift::isBusiness('user@company.com'); // true

// Batch processing
Sift::domains(['a@company.com', 'b@gmail.com']); // ['company.com']

// Statistics
Sift::stats($emails); // ['total' => 5, 'business' => 3, ...]

// View all filtered domains
Sift::getCommonDomains();
```

### Questions?

If you have issues upgrading, please open an issue on GitHub.