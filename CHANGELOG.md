# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

## [3.9.0] - 2024-XX-XX

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
