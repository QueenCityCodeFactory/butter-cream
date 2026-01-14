# Changelog

All notable changes to Butter Cream will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- GitHub Actions CI/CD workflow with PHP 8.2, 8.3, 8.4 support
- PHPUnit test suite configuration
- PHPStan and Psalm static analysis
- Comprehensive documentation
- Contributing guidelines
- CI status badges

### Changed
- Updated minimum PHP requirement to 8.2
- Updated Psalm to version 6.x for PHP 8.4 compatibility

## [1.0.0] - 2024-XX-XX

### Added
- Initial public release
- Base Controller with AJAX pagination and flash messaging
- Flash Component with enhanced messaging
- Referer Component for smart navigation
- Custom View Helpers:
  - AjaxHelper
  - FlashHelper
  - FormHelper with modal confirmations
  - FormatHelper for US formatting (SSN, phone, ZIP)
  - HtmlHelper
  - PaginatorHelper with Bootstrap 5
  - TableHelper for sortable tables
  - TimeHelper
  - UrlHelper
  - GravatarHelper
  - NestedTreeHelper
- Model classes:
  - AppTable with data cleaning
  - TreeviewTrait for nested data
  - Validation for US data formats
  - File Entity with Flysystem support
- Middleware:
  - SessionTimeoutMiddleware
  - TrustProxyMiddleware
- Utility classes:
  - Format for string formatting
  - Muddle for array manipulation
- Database Types:
  - JsonArrayType
- Views:
  - AppView with all helpers
  - PdfView for PDF generation
- Bake Templates:
  - Controller templates
  - Model templates
  - Bootstrap 5 styled CRUD templates
- Custom ExceptionRenderer
- Bootstrap 5 integration via FriendsOfCake
- PDF generation support
- Spreadsheet generation support

[Unreleased]: https://github.com/QueenCityCodeFactory/butter-cream/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/QueenCityCodeFactory/butter-cream/releases/tag/v1.0.0
