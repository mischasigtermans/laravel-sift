# Changelog

All notable changes to this project will be documented in this file.

## [0.3.0] - 2025-12-07

### Added
- `BusinessEmail` validation rule for requiring business email addresses
- `Sift::rule()` helper to get the validation rule instance
- `Sift::isBusiness()` method to check if email is from a business domain
- `Sift::domains()` for batch extraction from multiple emails
- `Sift::extractAll()` for extracting all domains without filtering
- `Sift::stats()` for email collection statistics (total, business, personal, rate, top domains)
- `Sift::getCommonDomains()` to retrieve the full merged list of filtered domains
- `exclude_default_domains` config option to whitelist specific default providers
- Pest 4 support
- 30+ additional email providers including:
  - US ISPs (AT&T, Comcast, Verizon, etc.)
  - Privacy-focused (Mailfence, Posteo, Disroot, Countermail)
  - Regional providers for Netherlands, Brazil, Spain
  - Modern providers (Proton.me, Naver)
  - More disposable email services

### Changed
- Default domains now live in package (`DefaultDomains::LIST`) - users automatically get updates
- Config simplified to `additional_domains` and `exclude_default_domains`
- Expanded common domains list from 70+ to 100+ providers

### Removed
- `common_domains` config option (now managed by package internally)

**See [UPGRADE.md](UPGRADE.md) for migration instructions.**

## [0.2.0] - 2025-09-18

### Added
- Laravel 12 support

### Changed
- Minimum PHP version raised to 8.2
- Updated Pest to v3 support
- Updated Orchestra Testbench to v9/v10 support

## [0.1.0] - 2025-02-06

### Added
- Initial release
- Domain extraction from email addresses via `Sift::domain()`
- Smart filtering of public email providers (Gmail, Yahoo, Outlook, etc.)
- Domain checking via `Sift::isCommon()`
- 70+ pre-configured public email providers
- Customizable domain list via configuration
- Case-insensitive domain matching
- Laravel 9, 10, and 11 support
- PHP 8.0+ support
- Facade for convenient static access
- Blade template support
