# Project: TrailMarker - Backend

TrailMarker is a Laravel-based API providing backend services for a trail tracking and discovery application. It handles trail data, GPX trace processing, user reviews, and custom trail lists.

## Tech Stack

- **Framework:** Laravel 12.x
- **Language:** PHP 8.2+
- **Authentication:** Laravel Sanctum (configured for first-party SPA session-based auth)
- **Database:** MySQL/MariaDB
- **Key Libraries:**
    - `sibyx/phpgpx`: For parsing and extracting statistics from GPX files.
    - `laravel/breeze`: For authentication scaffolding.
    - `laravel/pint`: For code style enforcement.
- **Testing:** PHPUnit 11.x

## Core Domain Concepts

- **Trails (`Trail`):** The central entity. Includes metadata (title, description, difficulty) and a GPX trace file.
- **GPX Processing:** Handled via `TrailObserver`. When a trail is created or updated with a new trace file, the observer uses `phpGPX` to calculate distance, elevation gain (denivele), and starting coordinates.
- **Avis (`Avis`):** User reviews and ratings for trails.
- **Trail Lists (`TrailList`):** User-curated collections of trails.
- **Trail Images (`TrailImage`):** Multiple images can be associated with a trail, also managed via `TrailObserver`.

## Architectural Patterns

- **API-First:** Uses Laravel Resources (`app/Http/Resources`) for consistent JSON output. `JsonResource::withoutWrapping()` is enabled in `AppServiceProvider`.
- **Observers:** Business logic for file processing and storage (GPX and images) is encapsulated in `app/Observers/TrailObserver.php`.
- **Validation:** Strictly enforced using Form Requests (`app/Http/Requests`).
- **Authorization:** Managed through Laravel Policies (`app/Policies`).
- **Enums:** Uses PHP 8.2 Enums for fixed sets like `TrailDifficulty`.

## General Instructions

- **Documentation:** Ensure all functions and classes are PhpDoc documented.
- **Testing:** Always look for existing tests in `tests/Feature` or `tests/Unit` before making changes. Add new test cases for every feature or bug fix.
- **File Storage:** The `public` disk is used for GPX traces and images.

## Coding Style

- **Standard:** Modern PHP, PSR-12.
- **Style:** Laravel coding style, enforced by Laravel Pint.
- **Type Safety:** Use strict typing where possible (return types, parameter types, property types).
