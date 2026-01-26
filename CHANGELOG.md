# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.9.2] - 2025-01-26

### Added
- Official package logo with multiple variations (standard, dark, Laravel red, banner)
- Badges: Packagist version, downloads, DOI citation, license, Laravel News featured
- Professional centered README layout
- CONTRIBUTING.md for contribution guidelines
- SECURITY.md for security policy
- GitHub issue templates (bug report, feature request)
- PHPStan for static analysis
- Laravel 12 support in CI

## [3.9.1] - 2025-01-26

### Added
- Zenodo integration for DOI citation (10.5281/zenodo.18204415)

## [3.9.0] - 2025-01-07

### Added
- WebSocket tester in Swagger UI for real-time API testing
- API versioning support with version switcher dropdown
- Dark/Light theme toggle with system preference detection
- Export functionality (OpenAPI JSON, YAML, Postman, Insomnia)

### Fixed
- JSON syntax highlighting colors in Swagger UI code blocks
- Brackets and punctuation visibility on dark backgrounds
- Background color on text spans in code blocks

### Changed
- Simplified theme toggle to dark/light only with system default
- Removed unnecessary code comments

## [3.0.0] - 2024-01-01

### Added
- Initial release
- `ApiResponse` class with fluent interface
- `ApiResponse` Facade for static access
- `HasApiResponse` trait for controllers
- `ApiExceptionHandler` for consistent error handling
- `ForceJsonResponse` middleware
- Support for Laravel 10 and 11
- Automatic pagination metadata
- Configurable response keys
- Comprehensive test suite

[Unreleased]: https://github.com/stackmasteraliza/laravel-api-response/compare/v3.9.2...HEAD
[3.9.2]: https://github.com/stackmasteraliza/laravel-api-response/compare/v3.9.1...v3.9.2
[3.9.1]: https://github.com/stackmasteraliza/laravel-api-response/compare/v3.9.0...v3.9.1
[3.9.0]: https://github.com/stackmasteraliza/laravel-api-response/compare/v3.0.0...v3.9.0
[3.0.0]: https://github.com/stackmasteraliza/laravel-api-response/releases/tag/v3.0.0
