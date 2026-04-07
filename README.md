# Butter Cream

![CI](https://github.com/QueenCityCodeFactory/butter-cream/workflows/ButterCream%20CI/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A CakePHP 5 Plugin with Bootstrap 5 theme, custom helpers, components, and bake templates for rapid application development.

## Requirements

- PHP 8.4 or higher
- CakePHP 5.3 or higher
- Bootstrap 5 (via FriendsOfCake/bootstrap-ui)

## Installation

Install the plugin via Composer:

```bash
composer require queencitycodefactory/butter-cream
```

Load the plugin in your application's `src/Application.php`:

```php
public function bootstrap(): void
{
    parent::bootstrap();
    $this->addPlugin('ButterCream');
}
```

## Features

### Custom Base Classes

#### Controller
- **`ButterCream\Controller\Controller`** - Extended base controller with:
  - Ajax pagination support with configurable limits
  - Automatic ajax layout switching
  - Enhanced pagination with page-out-of-bounds handling
  - Auto-loaded Flash and Referer components
  - PDF and Spreadsheet view support

### Components

#### Flash Component
- **`ButterCream\Controller\Component\FlashComponent`**
- Enhanced flash messaging with HTML support by default
- Methods: `success()`, `error()`, `set()`

#### Referer Component
- **`ButterCream\Controller\Component\RefererComponent`**
- Smart referer tracking and redirection
- Configurable ignored URLs
- Normalizes URLs for consistent matching
- Methods:
  - `setReferer()` - Store referer for later use
  - `getReferer()` - Retrieve current referer
  - `redirect()` - Redirect to referer or fallback
  - `isMatch()` - Check if referer matches a URL
  - `ignore()` - Add URL to ignore list

#### Export Component
- **`ButterCream\Controller\Component\ExportComponent`**
- Export controller support for spreadsheet and PDF downloads

### View Helpers

#### Ajax Helper
- **`ButterCream\View\Helper\AjaxHelper`**
- `relatedData()` - Create containers for AJAX-loaded content

#### Alert Helper
- **`ButterCream\View\Helper\AlertHelper`**
- Bootstrap 5 alert and callout rendering
- `success()`, `error()`, `warning()`, `info()` - Typed alerts with icons
- `callout()` - Callout-style alerts
- Dismissible and non-dismissible variants

#### Badge Helper
- **`ButterCream\View\Helper\BadgeHelper`**
- Bootstrap 5 badge rendering with configurable color maps
- `badge()` - Generic badge with any variant
- `status()` - Status badges with automatic color mapping
- `priority()` - Priority badges with automatic color mapping
- `boolean()` - Yes/No badges for boolean values

#### Card Helper
- **`ButterCream\View\Helper\CardHelper`**
- Bootstrap 5 card rendering
- `card()` - Standard card with header, body, footer
- `statsCard()` - Stats/metric display card
- `listGroupCard()` - Card with list group body

#### Flash Helper
- **`ButterCream\View\Helper\FlashHelper`**
- Enhanced flash message rendering with Bootstrap 5 styling

#### Filters Helper
- **`ButterCream\View\Helper\FiltersHelper`**
- Bootstrap 5 dropdown-based filter drawer for index pages
- `setup()` - Configure drawer ID, title, update selector, URL
- `addControl()` - Register filter fields (passed through to FormHelper)
- `render()` - Output toggle button and drawer panel with GET form
- Works in both standalone page and AJAX (relatedData) contexts
- Active filter count shown as badge on toggle button

#### Form Helper
- **`ButterCream\View\Helper\FormHelper`**
- Extended Bootstrap 5 form helper
- `create()` - Automatic referer tracking in forms
- `postLink()` - Enhanced POST links with modal confirmation
- `deleteBtn()` - Pre-configured delete button
- `saveButton()` - Pre-configured save button with icon
- `cancelButton()` - Cancel with referer redirect
- `backButton()` - Back navigation button
- `continueButton()` - Continue/next step button
- `confirmButton()` - Modal-confirm enabled button
- `resetButton()` - Form reset button with icon
- `switch()` - Bootstrap 5 form-switch toggle
- `colorPicker()` - Color picker input with Bootstrap styling
- `control()` - Enhanced with `enhancedSelect` (auto TomSelect), input groups (`prepend`/`append`), floating labels

#### Format Helper
- **`ButterCream\View\Helper\FormatHelper`**
- `ssn()` - Format US Social Security Numbers
- `zip()` - Format US ZIP codes (5 or 9 digit)
- `phone()` - Format US phone numbers
- `parsePhone()` - Parse phone number parts
- `formatString()` - Apply custom formatting patterns
- `maskString()` - Mask sensitive data

#### HTML Helper
- **`ButterCream\View\Helper\HtmlHelper`**
- Extended Bootstrap 5 HTML helper
- `icon()` - Font Awesome icon shorthand
- `nullSafe()` - Display em-dash for null values
- `accordion()` - Bootstrap 5 accordion from array config
- `viewBtn()` / `editBtn()` / `addBtn()` - Pre-styled action buttons
- `actionDropdownMenu()` - Bootstrap 5 dropdown action menu

#### Paginator Helper
- **`ButterCream\View\Helper\PaginatorHelper`**
- Bootstrap 5 styled pagination
- `ajaxTemplateOptions()` - Configure AJAX pagination link templates with `data-update` attribute
- Custom Font Awesome icons for prev/next
- Enhanced `prev()`, `next()`, `numbers()` methods

#### Progress Helper
- **`ButterCream\View\Helper\ProgressHelper`**
- Bootstrap 5 progress bar rendering
- `bar()` - Single progress bar with variant, label, striped, animated options
- `stacked()` - Multiple stacked progress bars

#### Table Helper
- **`ButterCream\View\Helper\TableHelper`**
- `header()` - Generate sortable table column headers with sort direction indicators
- Bootstrap 5 table styling
- AJAX-aware: generates `ajax-pagination-link` class and `data-update` attributes when in AJAX context

#### Time Helper
- **`ButterCream\View\Helper\TimeHelper`**
- `semantic()` - `<time>` element with Bootstrap tooltip showing relative time
- `relativeTime()` - Human-friendly relative time strings
- `userFormat()` - Configurable date/datetime formatting

#### URL Helper
- **`ButterCream\View\Helper\UrlHelper`**
- Extended URL helper

#### Gravatar Helper
- **`ButterCream\View\Helper\GravatarHelper`**
- Generate Gravatar image URLs

#### Filters Helper
- **`ButterCream\View\Helper\FiltersHelper`**
- Bootstrap 5 dropdown-based filter drawer for index pages
- Register filter controls in templates; the layout calls `render()` for the toggle button and drawer panel
- Works in both standalone page and AJAX (relatedData) contexts

#### Nested Tree Helper
- **`ButterCream\View\Helper\NestedTreeHelper`**
- Render nested tree structures

### View Traits

#### ButterCreamHelpersTrait
- **`ButterCream\View\ButterCreamHelpersTrait`**
- Reusable trait for loading the standard ButterCream helper stack in any View class
- Call `loadButterCreamHelpers()` from your View's `initialize()` method

### Model Layer

#### AppTable
- **`ButterCream\Model\Table\AppTable`**
- Automatic data cleaning for weird character encodings (smart quotes, em dashes, etc.)
- Base table class with reusable callbacks

#### TreeviewTrait
- **`ButterCream\Model\TreeviewTrait`**
- `findTreeview()` - Custom finder for nested tree data
- Configurable key fields and nesting

#### Validation
- **`ButterCream\Model\Validation`**
- `phone()` - Validate US phone numbers
- `postal()` - Validate US ZIP codes
- `ssn()` - Validate US Social Security Numbers
- `birthdate()` - Validate birthdate (not in future)

#### File Entity
- **`ButterCream\Model\Entity\File`**
- File management entity with path and base64 getters
- Flysystem integration

### Services

#### FileService
- **`ButterCream\Service\FileService`**
- Service-layer class for file storage, retrieval, resizing, and deletion
- Uses Flysystem for filesystem abstraction
- Model/foreign key pattern to associate files with any table record
- Image resizing with Imagick support
- Replaces the deprecated `FileApi`

### Exceptions

#### ButterCreamException
- **`ButterCream\Http\Exception\ButterCreamException`**
- Base exception class for the plugin

#### StatusMessageException
- **`ButterCream\Message\Exception\StatusMessageException`**
- Exception for user-facing errors with flash message support

### Middleware

#### Session Timeout Middleware
- **`ButterCream\Routing\Middleware\SessionTimeoutMiddleware`**
- Configurable session timeout management
- AJAX-aware session extension
- Automatic session destruction on timeout

#### Trust Proxy Middleware
- **`ButterCream\Routing\Middleware\TrustProxyMiddleware`**
- Configure proxy trust settings

### Utility Classes

#### Format
- **`ButterCream\Utility\Format`**
- Static formatting methods for SSN, phone, ZIP codes
- Pattern-based string formatting and masking

#### Muddle
- **`ButterCream\Utility\Muddle`**
- Array manipulation utilities
- `insert()` - Insert values using path notation
- `buildDotNotationPath()` - Build paths from arrays

### Database Types

#### JsonArrayType
- **`ButterCream\Database\Type\JsonArrayType`**
- Custom JSON array type with batch casting support

### Views

#### AppView
- **`ButterCream\View\AppView`**
- Pre-configured with all ButterCream helpers

#### PdfView
- **`ButterCream\View\PdfView`**
- PDF generation support via CakePdf
- Pre-configured with ButterCream helpers

### Bake Templates

Custom bake templates for rapid development with Bootstrap 5 styling:

- **Controllers** - Pre-configured with Search component and ButterCream base
- **Models/Tables** - Enhanced table templates with callbacks
- **Templates** - Bootstrap 5 styled CRUD views
  - `add.twig` - Create forms
  - `edit.twig` - Edit forms
  - `index.twig` - List views with tables
  - `view.twig` - Detail views
- **Form Elements** - Bootstrap 5 form styling

### Error Handling

#### ExceptionRenderer
- **`ButterCream\Error\ExceptionRenderer`**
- Custom error rendering with ButterCream theme
- Safe error message handling
- Debug-aware error messages

## Usage Examples

### Using the Base Controller

```php
namespace App\Controller;

use ButterCream\Controller\Controller;

class ArticlesController extends Controller
{
    // Automatic Flash and Referer components loaded
    // Ajax pagination configured
    // PDF and Spreadsheet views available
}
```

### Using Referer Component

```php
public function delete($id)
{
    $article = $this->Articles->get($id);
    if ($this->Articles->delete($article)) {
        $this->Flash->success('Article deleted.');
    }

    // Redirect back to where user came from
    return $this->Referer->redirect(['action' => 'index']);
}
```

### Using Format Helper

```php
// In a template
echo $this->Format->phone('5551234567'); // (555) 123-4567
echo $this->Format->ssn('123456789'); // 123-45-6789
echo $this->Format->zip('12345'); // 12345
echo $this->Format->zip('123456789'); // 12345-6789
```

### Using Table Helper

```php
// Create sortable table headers
<table class="table">
    <thead>
        <tr>
            <?= $this->Table->header('id', 'ID') ?>
            <?= $this->Table->header('title', 'Article Title') ?>
            <?= $this->Table->header('created', 'Created') ?>
        </tr>
    </thead>
</table>
```

### Using TreeviewTrait

```php
// In a Table class
use ButterCream\Model\TreeviewTrait;

class CategoriesTable extends AppTable
{
    use TreeviewTrait;
}

// In a controller
$categories = $this->Categories->find('treeview');
```

### Using FileService

```php
use ButterCream\Service\FileService;

$fileService = new FileService();

// Get a file record (optionally with file contents)
$file = $fileService->get($id, contents: true);

// Upload a file associated with a record
$fileService->upload($uploadedFile, 'Articles', $articleId);

// Delete a file (removes from disk and database)
$fileService->delete($id);
```

### Baking with ButterCream Templates

The plugin includes custom bake templates that generate Bootstrap 5 styled code:

```bash
bin/cake bake model Articles
bin/cake bake controller Articles
bin/cake bake template Articles
```

Generated code will include:
- Bootstrap 5 form styling
- Pre-configured Search component
- ButterCream helpers
- Responsive table layouts
- Modal confirmations for delete actions

## Configuration

### Session Timeout Middleware

Add to your `Application.php`:

```php
$middlewareQueue->add(new \ButterCream\Routing\Middleware\SessionTimeoutMiddleware([
    'timeout' => 30 // minutes
]));
```

### Trust Proxy Middleware

```php
$middlewareQueue->add(new \ButterCream\Routing\Middleware\TrustProxyMiddleware(true));
```

## Dependencies

- [friendsofcake/search](https://github.com/FriendsOfCake/search) - Search functionality
- [friendsofcake/bootstrap-ui](https://github.com/FriendsOfCake/bootstrap-ui) - Bootstrap 5 integration
- [friendsofcake/cakepdf](https://github.com/FriendsOfCake/CakePDF) - PDF generation
- [queencitycodefactory/cakespreadsheet](https://github.com/QueenCityCodeFactory/cakespreadsheet) - Spreadsheet generation

### Optional / Suggested

- [league/flysystem](https://github.com/thephpleague/flysystem) - Required for `FileService` file operations
- [cakephp/migrations](https://github.com/cakephp/migrations) - Required to run the optional files table migration

## License

MIT License. See LICENSE file for details.

## Testing

Butter Cream includes a comprehensive test suite powered by PHPUnit. The current test coverage includes:

### Tested Components
- **FlashComponent** - Escape default, HTML flash messaging, exception handling
- **RefererComponent** - URL normalization, referer tracking, redirect logic, ignore list

### Tested Helpers
- **FormatHelper** - US formatting (SSN, phone, ZIP)

### Tested Utilities
- **Format** - String formatting and masking
- **Muddle** - Array manipulation utilities

### Tested Model Features
- **Validation** - US data validation (phone, postal, SSN, birthdate)
- **TreeviewTrait** - Nested data structure nesting and custom keys
- **AppTable** - Data cleaning (smart quotes, whitespace, encoding)
- **StatusMessage** - Message registry getters and type coercion
- **StatusMessageException** - Exception with status message keys

### Tested Database Types
- **JsonArrayType** - JSON parsing, error handling, batch casting

### Tested Middleware
- **SessionTimeoutMiddleware** - Session management and timeout handling

### Tested Plugin
- **ButterCreamPlugin** - Plugin bootstrap, command discovery

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage (requires Xdebug or pcov)
vendor/bin/phpunit --coverage-html coverage/

# Run specific test file
vendor/bin/phpunit tests/TestCase/Utility/FormatTest.php
```

### Contributing Tests

When adding new features or fixing bugs, please include tests. Test files should:
- Be placed in `tests/TestCase/` matching the source structure
- Extend `Cake\TestSuite\TestCase`
- Follow PHPUnit best practices
- Test both success and failure scenarios

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details on writing tests.

## Support

- Issues: [GitHub Issues](https://github.com/QueenCityCodeFactory/butter-cream/issues)
- Documentation: [GitHub Wiki](https://github.com/QueenCityCodeFactory/butter-cream)

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines on:

- Reporting bugs
- Suggesting enhancements
- Submitting pull requests
- Development setup
- Coding standards
- Testing requirements

Please ensure all tests pass and code follows CakePHP standards before submitting a PR.
