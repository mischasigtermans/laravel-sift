<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use MischaSigtermans\Sift\Facades\Sift;
use MischaSigtermans\Sift\Rules\BusinessEmail;

it('extracts the domain from an email', function () {
    expect(Sift::domain('user@example.com'))->toBe('example.com');
});

it('filters out public email domains by default', function () {
    expect(Sift::domain('user@gmail.com'))->toBeNull();
});

it('allows public domains when explicitly allowed', function () {
    expect(Sift::domain('user@gmail.com', true))->toBe('gmail.com');
});

it('correctly identifies common domains', function () {
    expect(Sift::isCommon('aol.com'))->toBeTrue();
    expect(Sift::isCommon('live.com'))->toBeTrue();
    expect(Sift::isCommon('yandex.com'))->toBeTrue();
});

it('recognizes custom additional domains from config', function () {
    Config::set('sift.additional_domains', ['customdomain.com']);

    expect(Sift::isCommon('customdomain.com'))->toBeTrue();
    expect(Sift::domain('user@customdomain.com'))->toBeNull();
});

it('treats input without @ as domain directly', function () {
    expect(Sift::domain('example.com'))->toBe('example.com');
    expect(Sift::domain('gmail.com'))->toBeNull();
});

it('handles case insensitivity', function () {
    expect(Sift::domain('user@GMAIL.COM'))->toBeNull();
    expect(Sift::domain('user@Example.COM'))->toBe('example.com');
    expect(Sift::isCommon('GMAIL.COM'))->toBeTrue();
});

it('works with domain input directly', function () {
    expect(Sift::isCommon('gmail.com'))->toBeTrue();
    expect(Sift::isCommon('company.com'))->toBeFalse();
});

// v0.3.0 - isBusiness helper

it('checks if domain is a business email', function () {
    expect(Sift::isBusiness('company.com'))->toBeTrue();
    expect(Sift::isBusiness('gmail.com'))->toBeFalse();
    expect(Sift::isBusiness('user@company.com'))->toBeTrue();
    expect(Sift::isBusiness('user@gmail.com'))->toBeFalse();
});

// v0.3.0 - Batch processing

it('extracts domains from multiple emails', function () {
    $emails = [
        'john@acme.com',
        'jane@stripe.com',
        'bob@gmail.com',
        'alice@acme.com',
    ];

    $domains = Sift::domains($emails);

    expect($domains)->toBe(['acme.com', 'stripe.com']);
});

it('extracts domains including common providers when specified', function () {
    $emails = [
        'john@acme.com',
        'bob@gmail.com',
    ];

    $domains = Sift::domains($emails, includeCommon: true);

    expect($domains)->toBe(['acme.com', 'gmail.com']);
});

it('extracts all domains without filtering', function () {
    $emails = [
        'john@acme.com',
        'bob@gmail.com',
        'jane@acme.com',
    ];

    $domains = Sift::extractAll($emails);

    expect($domains)->toBe(['acme.com', 'gmail.com']);
});

// v0.3.0 - Statistics

it('calculates statistics for email collection', function () {
    $emails = [
        'john@acme.com',
        'jane@acme.com',
        'bob@stripe.com',
        'alice@gmail.com',
        'charlie@yahoo.com',
    ];

    $stats = Sift::stats($emails);

    expect($stats['total'])->toBe(5);
    expect($stats['business'])->toBe(3);
    expect($stats['personal'])->toBe(2);
    expect($stats['business_rate'])->toBe(60.0);
    expect($stats['top_domains'])->toBe([
        'acme.com' => 2,
        'stripe.com' => 1,
    ]);
});

it('limits top domains in statistics', function () {
    $emails = [
        'a@one.com',
        'b@two.com',
        'c@three.com',
        'd@four.com',
        'e@five.com',
    ];

    $stats = Sift::stats($emails, topDomainsLimit: 3);

    expect($stats['top_domains'])->toHaveCount(3);
});

it('handles empty email collection in statistics', function () {
    $stats = Sift::stats([]);

    expect($stats['total'])->toBe(0);
    expect($stats['business'])->toBe(0);
    expect($stats['personal'])->toBe(0);
    expect($stats['business_rate'])->toBe(0.0);
    expect($stats['top_domains'])->toBe([]);
});

// v0.3.0 - Validation rule

it('validates business email addresses', function () {
    $validator = Validator::make(
        ['email' => 'user@company.com'],
        ['email' => [new BusinessEmail]]
    );

    expect($validator->passes())->toBeTrue();
});

it('rejects common email providers in validation', function () {
    $validator = Validator::make(
        ['email' => 'user@gmail.com'],
        ['email' => [new BusinessEmail]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toBe('The email must be a business email address.');
});

it('rejects invalid email format in validation', function () {
    $validator = Validator::make(
        ['email' => 'not-an-email'],
        ['email' => [new BusinessEmail]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toBe('The email must be a valid email address.');
});

it('provides rule via facade', function () {
    expect(Sift::rule())->toBeInstanceOf(BusinessEmail::class);
});

// v0.3.0 - exclude_default_domains config

it('excludes default domains when configured', function () {
    // Gmail is a default common domain
    expect(Sift::isCommon('gmail.com'))->toBeTrue();

    // Exclude it from the defaults
    Config::set('sift.exclude_default_domains', ['gmail.com']);

    // Now it should be treated as a business domain
    expect(Sift::isCommon('gmail.com'))->toBeFalse();
    expect(Sift::isBusiness('gmail.com'))->toBeTrue();
    expect(Sift::domain('user@gmail.com'))->toBe('gmail.com');
});

it('returns merged common domains list', function () {
    $domains = Sift::getCommonDomains();

    expect($domains)->toBeArray();
    expect($domains)->toContain('gmail.com');
    expect($domains)->toContain('yahoo.com');
    expect(count($domains))->toBeGreaterThan(100);
});

it('applies both additional and excluded domains', function () {
    Config::set('sift.additional_domains', ['mycompany.com']);
    Config::set('sift.exclude_default_domains', ['protonmail.com']);

    $domains = Sift::getCommonDomains();

    expect($domains)->toContain('mycompany.com');
    expect($domains)->not->toContain('protonmail.com');
});
