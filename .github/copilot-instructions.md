# Butter Cream — CakePHP 5 Plugin

## Project Overview

CakePHP 5 plugin providing Bootstrap 5 theme, custom helpers, components, middleware, and bake templates. Namespace: `ButterCream`.

- **PHP >= 8.4**, **CakePHP >= 5.3**
- PSR-4 autoload: `ButterCream\` → `src/`
- Tests: `ButterCream\Test\` → `tests/`

## Code Style

- Strict types on every PHP file: `declare(strict_types=1);`
- Follow CakePHP coding standards (`cakephp/cakephp-codesniffer`)
- Run `composer cs-check` / `composer cs-fix` for style validation
- Run `composer stan` for static analysis (PHPStan)
- Run `composer test` for PHPUnit tests

## Architecture Conventions

### Controllers
- Extend `ButterCream\Controller\Controller` (not `Cake\Controller\Controller` directly)
- Flash and Referer components are auto-loaded

### Models / Tables
- Extend `ButterCream\Model\Table\AppTable` (aliased as `Table`)
- Import pattern: `use ButterCream\Model\Table\AppTable as Table;`
- AppTable provides automatic data cleaning in `beforeMarshal`

### Views
- Use `ButterCreamHelpersTrait` to load the standard helper stack
- `AppView` and `PdfView` come pre-configured

### File Management
- Use `ButterCream\Service\FileService` for all file operations
- `FileApi` has been removed; use `FileService` exclusively

### Exceptions
- Throw `ButterCream\Message\Exception\StatusMessageException` for user-facing errors with flash messages
- Use `ButterCream\Http\Exception\ButterCreamException` as the base exception

## Migrations — IMPORTANT

CakePHP 5 migrations **must** extend `Migrations\BaseMigration`. Do NOT use the old `Migrations\AbstractMigration` class.

Correct example:

```php
<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateExamples extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('examples');
        $table->addColumn('name', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();
    }
}
```

## Build & Test Commands

| Command | Purpose |
|---------|---------|
| `composer test` | Run PHPUnit test suite |
| `composer cs-check` | Check coding standards |
| `composer cs-fix` | Auto-fix coding standards |
| `composer stan` | PHPStan static analysis |
| `composer psalm` | Psalm static analysis |
| `composer check` | Run test + cs-check + stan |

## Key Patterns

- Bake templates live in `templates/bake/` and generate Bootstrap 5 styled code
- Layouts use Bootstrap 5 grid and components
- Flash messages support HTML by default (`escape => false`)
- Use `StatusMessage` for structured success/error messaging in services
- `TreeviewTrait` provides `find('treeview')` custom finder for nested set data

## View Helpers Quick Reference

| Helper | Key Methods | Purpose |
|--------|-------------|---------|
| `AjaxHelper` | `relatedData()` | AJAX-loaded content containers |
| `AlertHelper` | `success()`, `error()`, `warning()`, `info()`, `callout()` | Bootstrap 5 alerts |
| `BadgeHelper` | `badge()`, `status()`, `priority()`, `boolean()` | Bootstrap 5 badges with color maps |
| `CardHelper` | `card()`, `statsCard()`, `listGroupCard()` | Bootstrap 5 cards |
| `FiltersHelper` | `setup()`, `addControl()`, `render()` | Dropdown filter drawer for index pages |
| `FormHelper` | `control()`, `switch()`, `colorPicker()`, `deleteBtn()`, `saveButton()` | Extended form helper with input groups, floating labels, TomSelect |
| `FormatHelper` | `ssn()`, `phone()`, `zip()`, `maskString()` | US data formatting |
| `HtmlHelper` | `icon()`, `nullSafe()`, `accordion()`, `viewBtn()`, `editBtn()`, `addBtn()`, `actionDropdownMenu()` | Extended HTML helper |
| `PaginatorHelper` | `ajaxTemplateOptions()` | AJAX pagination link templates |
| `ProgressHelper` | `bar()`, `stacked()` | Bootstrap 5 progress bars |
| `TableHelper` | `header()` | Sortable table column headers (AJAX-aware) |
| `TimeHelper` | `semantic()`, `relativeTime()`, `userFormat()` | Extended time formatting |

### FormHelper `control()` Options
- `enhancedSelect` (default `true`): auto-adds `enhanced-select` class for TomSelect on `<select>` inputs
- `prepend` / `append`: wraps input in Bootstrap 5 input group
- `floating`: enables floating label

### Bake Templates
Custom bake templates in `templates/bake/` generate:
- **Entity** (`entity.twig`): Typed properties, accessibility arrays, virtual fields
- **Table** (`table.twig`): Enhanced table with callbacks
- **Index** (`index.twig`): ButterCream index layout with `Table->header()`, filters, action buttons
- **View** (`view.twig`): ButterCream view layout with detail fields, related data, accordion
