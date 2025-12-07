<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Additional Domains
    |--------------------------------------------------------------------------
    |
    | Add custom domains to filter alongside the built-in defaults. These are
    | merged with the package's 100+ default public email providers.
    |
    | Useful for:
    | - Industry-specific email providers not in the default list
    | - Internal domains you want to exclude (e.g., your own company)
    | - Competitor domains you don't want to accept
    |
    */
    'additional_domains' => [
        // 'example.org',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Default Domains
    |--------------------------------------------------------------------------
    |
    | Remove specific domains from the default filter list. Use this to allow
    | certain public providers that you want to treat as business emails.
    |
    | The package includes 100+ public email providers by default (Gmail,
    | Yahoo, Outlook, ProtonMail, etc.). Add domains here to whitelist them.
    |
    */
    'exclude_default_domains' => [
        // 'protonmail.com',
    ],

];
