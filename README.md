# Butter Cream

A CakePHP 5 Plugin with Bootstrap 5 theme, custom helpers, components, and bake templates for rapid application development.

## Requirements

- PHP 8.2 or higher
- CakePHP 5.1 or higher
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

### View Helpers

#### Ajax Helper
- **`ButterCream\View\Helper\AjaxHelper`**
- `relatedData()` - Create containers for AJAX-loaded content

#### Flash Helper
- **`ButterCream\View\Helper\FlashHelper`**
- Enhanced flash message rendering with Bootstrap 5 styling

#### Form Helper
- **`ButterCream\View\Helper\FormHelper`**
- Extended Bootstrap 5 form helper
- `create()` - Automatic referer tracking in forms
- `postLink()` - Enhanced POST links with modal confirmation
- `deleteBtn()` - Pre-configured delete button
- `saveButton()` - Pre-configured save button with icon
- Custom postLink with Bootstrap modal integration

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

#### Paginator Helper
- **`ButterCream\View\Helper\PaginatorHelper`**
- Bootstrap 5 styled pagination
- `ajaxTemplateOptions()` - Configure AJAX pagination links
- Custom Font Awesome icons for prev/next
- Enhanced `prev()`, `next()`, `numbers()` methods

#### Table Helper
- **`ButterCream\View\Helper\TableHelper`**
- `header()` - Generate sortable table headers
- Bootstrap 5 table styling
- Automatic sort integration

#### Time Helper
- **`ButterCream\View\Helper\TimeHelper`**
- Extended Bootstrap 5 time helper

#### URL Helper
- **`ButterCream\View\Helper\UrlHelper`**
- Extended URL helper

#### Gravatar Helper
- **`ButterCream\View\Helper\GravatarHelper`**
- Generate Gravatar image URLs

#### Nested Tree Helper
- **`ButterCream\View\Helper\NestedTreeHelper`**
- Render nested tree structures

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

## License

MIT License. See LICENSE file for details.

## Support

- Issues: [GitHub Issues](https://github.com/QueenCityCodeFactory/butter-cream/issues)
- Documentation: [GitHub Wiki](https://github.com/QueenCityCodeFactory/butter-cream)

## Contributing

Contributions are welcome! Please submit pull requests or open issues on GitHub.
