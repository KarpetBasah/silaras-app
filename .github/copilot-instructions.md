# CodeIgniter 4 Silaras App - AI Agent Instructions

This is a **CodeIgniter 4** web application using the standard framework architecture with minimal customizations.

## Architecture Overview

- **Framework**: CodeIgniter 4 (PHP 8.1+) - follows MVC pattern
- **Entry Point**: `public/index.php` - web server should point to `public/` directory
- **CLI Tool**: `./spark` - CodeIgniter's command-line interface for migrations, scaffolding, etc.
- **Autoloading**: PSR-4 with `CodeIgniter\` namespace for system files
- **Configuration**: All config files in `app/Config/` - modify these instead of system defaults

## Key Directory Structure

```
app/
├── Config/        # App configuration (Database, Routes, Services, etc.)
├── Controllers/   # HTTP request handlers extending BaseController
├── Models/        # Data models extending CodeIgniter\Model
├── Views/         # Template files (.php)
├── Database/      # Migrations and Seeds
└── Common.php     # Global helper functions

system/            # Framework core (DO NOT MODIFY)
public/            # Web root with index.php
tests/             # PHPUnit tests
vendor/            # Composer dependencies
```

## Development Patterns

### Controllers
- Extend `BaseController` which provides common functionality
- Use dependency injection in `initController()` method for services
- Add shared helpers to `$helpers` array in BaseController

### Database Configuration
- Edit `app/Config/Database.php` for database settings
- Testing uses SQLite in-memory database (`tests` connection group)
- Environment automatically switches to `tests` group when `ENVIRONMENT === 'testing'`

### Testing
- Run tests: `composer test` or `./vendor/bin/phpunit` (Windows: `vendor\bin\phpunit`)
- Test database configured separately in Database config
- Coverage reports: `--coverage-html=tests/coverage/`
- XDebug required for code coverage

### CLI Commands
- Use `./spark` for all CodeIgniter CLI operations:
  - `./spark list` - show available commands
  - `./spark make:controller ControllerName` - generate controller
  - `./spark migrate` - run database migrations
  - `./spark db:seed SeedName` - run database seeder

## Configuration Specifics

### Services (Dependency Injection)
- Override framework services in `app/Config/Services.php`
- Use `service('name')` function to access services
- Services are singletons by default (`$getShared = true`)

### Routing
- All routes defined in `app/Config/Routes.php`
- Default route: `$routes->get('/', 'Home::index')`
- Use route groups for organization and middleware

### Environment
- Database connection automatically switches to `tests` group during testing
- Base URL defaults to `http://localhost:8080/`
- CSP disabled by default (`$CSPEnabled = false`)

## Performance Optimization
- `preload.php` configured for OPcache preloading (production)
- Excludes test files, CLI components, and unused database drivers
- Composer autoloader optimization enabled

## File Conventions
- Controllers: PascalCase, extend BaseController
- Models: PascalCase, extend CodeIgniter\Model  
- Views: snake_case.php files
- Config files: PascalCase classes in Config namespace
- Follow CodeIgniter 4 naming conventions throughout

## Common Gotchas
- Web server must point to `public/` folder, not project root
- Use `APPPATH`, `SYSTEMPATH`, `ROOTPATH` constants for file paths
- Database migrations stored in `app/Database/Migrations/`
- Views loaded with `view('view_name')` function (no .php extension)
- Always use `service()` function instead of direct instantiation for framework services