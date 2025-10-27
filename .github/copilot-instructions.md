# SiLaras App - AI Agent Instructions

This is a **CodeIgniter 4** web application for geospatial-based planning and monitoring of regional development programs in Banjarbaru City.

## Architecture Overview

- **Framework**: CodeIgniter 4 (PHP 8.1+) with MVC architecture
- **Entry Point**: `public/index.php` - web server points to `public/` directory
- **Key Features**: Geospatial analysis, program planning, real-time monitoring
- **Database**: MySQL/MariaDB with GIS capabilities
- **Frontend**: HTML5, CSS3, JavaScript (ES6+) with Leaflet.js for maps
- **CLI Tool**: `./spark` - CodeIgniter's command-line interface for all operations
- **Autoloading**: PSR-4 with `CodeIgniter\` namespace for system files

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

## Core Components

### 1. Program Management Module
- **Controller**: `app/Controllers/InputProgram.php`
- **Model**: `app/Models/ProgramModel.php`
- **Views**: `app/Views/input_program/`
- Handles CRUD operations for development programs

### 2. Geospatial Module (RPJMD)
- **Controller**: `app/Controllers/RPJMD.php`
- **Model**: `app/Models/RpjmdPriorityZoneModel.php`
- Manages priority zones and spatial analysis
- See `RPJMD_MODULE_DOCUMENTATION.md` for detailed schema

### 3. Monitoring System
- **Controller**: `app/Controllers/Monitoring.php`
- Real-time tracking of program implementation
- Integration with program evaluation metrics

## Development Patterns

### Controllers
- Extend `BaseController` which provides common functionality
- Use dependency injection in `initController()` method for services
- Add shared helpers to `$helpers` array in BaseController

### Database Configuration
- Configure MySQL/MariaDB with GIS extensions in `app/Config/Database.php`
- Testing uses SQLite in-memory database (`tests` connection group)
- Environment auto-switches to `tests` group when `ENVIRONMENT === 'testing'`

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

## Project Conventions

### Routes
- Defined in `app/Config/Routes.php`
- Group-based routing for modules:
  - `/input-program/` - Program management
  - `/peta-program/` - Map visualization
  - `/rpjmd/` - Development planning
- RESTful API endpoints under `/api` groups

### Models
- Extend `CodeIgniter\Model`
- Use soft deletes (`deleted_at`)
- GIS-related models include spatial data fields

### Views
- Located in `app/Views/<module_name>/`
- Use partial views for reusable components
- JavaScript modules in `public/assets/js/`

## Integration Points

### GIS Integration
- Leaflet.js for map visualization
- GeoJSON for spatial data exchange
- Priority zone analysis using point-in-polygon

### External Dependencies
- OpenStreetMap/Satellite base layers
- Font Awesome 6 for icons
- Chart.js for analytics

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