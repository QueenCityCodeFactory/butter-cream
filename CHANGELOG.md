# Changelog

All notable changes to Butter Cream will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `FileService` class — new service-layer replacement for `FileApi`, using Flysystem for file storage, retrieval, resizing, and deletion
- `ButterCreamHelpersTrait` — reusable trait for loading the standard ButterCream helper stack in any View class
- `FiltersHelper` — Bootstrap 5 dropdown-based filter drawer for index pages
- `ButterCreamException` base exception class
- `CreateFiles` migration (`config/Migrations/20260405000000_CreateFiles.php`) for the files table
- JSON decode error checking in `JsonArrayType::toPHP()` and `manyToPHP()`
- XSS protection: `h()` escaping in `NestedTreeHelper` tree item names
- Security documentation on `FlashComponent` `escape => false` default
- `@property` annotations on base `Controller` for component IDE support
- Comprehensive test coverage for FlashComponent, RefererComponent, JsonArrayType, TreeviewTrait, AppTable, StatusMessage, StatusMessageException, ButterCreamPlugin
- CategoriesFixture class for TreeviewTrait tests
- `league/flysystem` added as a suggested dependency for file management
- `AlertHelper` — Bootstrap 5 alert rendering (`success()`, `error()`, `warning()`, `info()`, `callout()`, dismissible/non-dismissible)
- `BadgeHelper` — Bootstrap 5 badge rendering with `status()`, `priority()`, `boolean()`, and generic `badge()` methods; includes configurable color maps
- `CardHelper` — Bootstrap 5 card rendering (`card()`, `statsCard()`, `listGroupCard()`)
- `ProgressHelper` — Bootstrap 5 progress bars (`bar()`, `stacked()`, striped/animated variants)
- `ExportComponent` — Export controller support for spreadsheet and PDF downloads
- `FormHelper::switch()` — Bootstrap 5 form-switch toggle shorthand
- `FormHelper::colorPicker()` — `<input type="color">` with Bootstrap styling
- `FormHelper::resetButton()` — reset button with icon
- `FormHelper::confirmButton()` — modal-confirm support
- `FormHelper::control()` — enhanced with input group support (`prepend`/`append`) and floating label support
- `HtmlHelper::nullSafe()` — display em-dash for null values
- `HtmlHelper::accordion()` — Bootstrap 5 accordion from array config
- `HtmlHelper::viewBtn()` / `editBtn()` / `addBtn()` — pre-styled action buttons
- `HtmlHelper::actionDropdownMenu()` — Bootstrap 5 dropdown action menu
- `HtmlHelper::icon()` — Font Awesome icon shorthand
- `PaginatorHelper::ajaxTemplateOptions()` — configure paginator link templates for AJAX pagination
- `TableHelper::header()` — sortable table column headers with Bootstrap-styled sort indicators, AJAX-aware
- `TimeHelper::semantic()` — `<time>` element with relative time tooltip
- `TimeHelper::relativeTime()` — human-friendly relative time strings
- `TimeHelper::userFormat()` — configurable date/datetime formatting
- Updated `ButterCreamHelpersTrait` to load new helpers (Alert, Badge, Card, Progress, Filters)
- Updated bake templates: entity template with typed properties and virtual fields, improved index/view templates with ButterCream helpers

### Changed
- **BREAKING:** Minimum PHP version raised to 8.4
- **BREAKING:** `FileApi` removed — use `FileService` instead
- `FileService` uses `EntityInterface` type hints instead of concrete `File` entity where appropriate
- `FileService` disables model listeners during internal save operations to avoid side-effects
- `FilesTable` expanded with validation rules, `beforeSave`/`afterSave`/`afterDelete` callbacks, and Flysystem integration
- `StatusMessage` refactored for improved return types and method signatures
- Renamed `NestedTreeHelper::sorter()` (protected) to `buildList()` to fix duplicate method name fatal error
- `RefererComponent` modernized: replaced `_registry->getController()` with `getController()`, `_config` with `getConfig()`/`setConfig()`
- Simplified `Validation::birthdate()` and `Validation::check()` methods
- `FormHelper::cancelButton()` now uses `'#'` instead of `javascript:void()` (CSP safe)
- `composer.json` scripts simplified to use `phpcs.xml` config automatically
- PHPUnit schema updated to 11.5
- `.gitattributes` updated to export-ignore additional dev files (CHANGELOG, CONTRIBUTING, SECURITY, phpcs.xml)

### Fixed
- `TimeHelper` now resolves user timezones from helper config, request context, identity, or legacy session;
  preserves the instant of native `DateTimeInterface` values; and correctly supports integer timestamps including `0`
- **Security:** XSS vulnerability in `templates/layout/main.php` — session data now encoded via `json_encode()` instead of raw string interpolation
- **Security:** XSS vulnerability in `NestedTreeHelper` — tree item names now escaped with `h()`
- **Security:** `javascript:void()` replaced with `'#'` in `FormHelper::cancelButton()`
- Migration `CreateFiles` corrected to extend `Migrations\BaseMigration` (was incorrectly using `Migrations\AbstractMigration`)
- Fatal error: `NestedTreeHelper` had two methods named `sorter()` (public and protected)
- `RefererComponent::normalizeUrl()` removed unreachable `is_array($url)` check
- `AppTable::beforeDelete` docblock type corrected to `EntityInterface`
- `FormatHelper` `@see` annotations corrected from `\App\` to `\ButterCream\` namespace
- `FilesTable` removed incorrect `@property` annotations referencing `\App\Model\Table\*`
- `templates/layout/error.php` added `isset($error)` guard and fixed template path
- Stale entries cleared from `psalm-baseline.xml`
- Replaced `join()` alias with `implode()` in `NestedTreeHelper`

### Removed
- `FileApi` class — replaced entirely by `FileService`
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
