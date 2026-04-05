# Changelog

All notable changes to Butter Cream will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- JSON decode error checking in `JsonArrayType::toPHP()` and `manyToPHP()`
- XSS protection: `h()` escaping in `NestedTreeHelper` tree item names
- Security documentation on `FlashComponent` `escape => false` default
- `@property` annotations on base `Controller` for component IDE support
- Comprehensive test coverage for FlashComponent, RefererComponent, JsonArrayType, TreeviewTrait, AppTable, StatusMessage, StatusMessageException, ButterCreamPlugin
- CategoriesFixture class for TreeviewTrait tests

### Changed
- **BREAKING:** Minimum PHP version raised to 8.4
- Renamed `NestedTreeHelper::sorter()` (protected) to `buildList()` to fix duplicate method name fatal error
- `RefererComponent` modernized: replaced `_registry->getController()` with `getController()`, `_config` with `getConfig()`/`setConfig()`
- Simplified `Validation::birthdate()` and `Validation::check()` methods
- Improved `StatusMessage` return types from `mixed` to specific types
- `FormHelper::cancelButton()` now uses `'#'` instead of `javascript:void()` (CSP safe)
- `composer.json` scripts simplified to use `phpcs.xml` config automatically
- PHPUnit schema updated to 11.5
- `.gitattributes` updated to export-ignore additional dev files (CHANGELOG, CONTRIBUTING, SECURITY, phpcs.xml)

### Fixed
- **Security:** XSS vulnerability in `templates/layout/main.php` — session data now encoded via `json_encode()` instead of raw string interpolation
- **Security:** XSS vulnerability in `NestedTreeHelper` — tree item names now escaped with `h()`
- **Security:** `javascript:void()` replaced with `'#'` in `FormHelper::cancelButton()`
- Fatal error: `NestedTreeHelper` had two methods named `sorter()` (public and protected)
- `RefererComponent::normalizeUrl()` removed unreachable `is_array($url)` check
- `AppTable::beforeDelete` docblock type corrected to `EntityInterface`
- `FormatHelper` `@see` annotations corrected from `\App\` to `\ButterCream\` namespace
- `FilesTable` removed incorrect `@property` annotations referencing `\App\Model\Table\*`
- `templates/layout/error.php` added `isset($error)` guard and fixed template path
- Stale entries cleared from `psalm-baseline.xml`
- Replaced `join()` alias with `implode()` in `NestedTreeHelper`

### Removed
- Empty `routes()` and `middleware()` overrides from `ButterCreamPlugin`
- Empty `beforeFilter()` from base `Controller`
- Empty `$filterArgs` from `FilesTable`

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
