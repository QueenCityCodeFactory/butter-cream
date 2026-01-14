# Contributing to Butter Cream

Thank you for your interest in contributing to Butter Cream! This document provides guidelines and instructions for contributing to this CakePHP plugin.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for all contributors.

## How to Contribute

### Reporting Bugs

Before creating a bug report:
- Check the [existing issues](https://github.com/QueenCityCodeFactory/butter-cream/issues) to avoid duplicates
- Ensure you're using the latest version of the plugin

When submitting a bug report, include:
- Clear description of the issue
- Steps to reproduce the problem
- Expected vs actual behavior
- CakePHP and PHP versions
- Code samples if applicable

### Suggesting Enhancements

Enhancement suggestions are welcome! Please:
- Use a clear and descriptive title
- Provide detailed explanation of the proposed feature
- Explain why this enhancement would be useful
- Include code examples if possible

### Pull Requests

1. **Fork the repository** and create your branch from `main`
   ```bash
   git checkout -b feature/my-new-feature
   ```

2. **Follow coding standards**
   - Use CakePHP coding standards (PSR-12)
   - Run `composer cs-check` before committing
   - Fix any issues with `composer cs-fix`

3. **Write tests**
   - Add PHPUnit tests for new features
   - Ensure all tests pass with `composer test`
   - Maintain or improve code coverage

4. **Run static analysis**
   ```bash
   composer stan  # PHPStan
   composer psalm # Psalm
   ```

5. **Commit your changes**
   - Use clear and meaningful commit messages
   - Reference issue numbers if applicable

6. **Push to your fork and submit a pull request**

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/QueenCityCodeFactory/butter-cream.git
   cd butter-cream
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Run the test suite:
   ```bash
   composer test
   ```

4. Check coding standards:
   ```bash
   composer cs-check
   ```

5. Run static analysis:
   ```bash
   composer check  # Runs all checks
   ```

## Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Follow [CakePHP conventions](https://book.cakephp.org/5/en/contributing/cakephp-coding-conventions.html)
- Use meaningful variable and method names
- Add docblocks for classes and methods
- Keep methods focused and concise

## Testing Guidelines

- Write unit tests for all new features
- Write integration tests for components and helpers
- Use descriptive test method names
- Test both success and failure scenarios
- Mock external dependencies

## Documentation

- Update README.md for new features
- Add inline code comments for complex logic
- Include usage examples for new helpers/components
- Update CHANGELOG.md with your changes

## Questions?

If you have questions about contributing, feel free to:
- Open an issue for discussion
- Reach out through the repository

Thank you for contributing to Butter Cream! 🎉
