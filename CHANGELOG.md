# Changelog

All notable changes to this project will be documented in this file.

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
