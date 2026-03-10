# TrailMarker: Tech Lead Refactoring Plan

This document outlines the step-by-step process to align the TrailMarker backend with elite Laravel standards. Each step is designed to improve maintainability, performance, and architectural clarity.

---

## Step 1: Domain Renaming (English Standardization)
**Objective:** Standardize the codebase to English to match Laravel's internal conventions.

- [ ] **Models:** Rename `Avis.php` to `Review.php`. Update class name and namespace.
- [ ] **Controllers:** Rename `AvisController.php` to `ReviewController.php`.
- [ ] **Requests:** Rename `app/Http/Requests/Avis/` to `app/Http/Requests/Review/`. 
    - Rename `AvisStoreRequest` -> `ReviewStoreRequest`.
    - Rename `AvisUpdateRequest` -> `ReviewUpdateRequest`.
- [ ] **Policies:** Rename `AvisPolicy.php` to `ReviewPolicy.php`.
- [ ] **Database (Table):** Create a migration to rename the `avis` table to `reviews`.
- [ ] **Database (Columns):** Create a migration to rename `denivele` to `elevation_gain` in the `trails` table.
- [ ] **Relationships:** 
    - In `Trail.php`: Rename `avis()` to `reviews()`.
    - In `User.php`: Rename `avis()` to `reviews()`.
- [ ] **Resources:** Update `TrailResource.php` to use the `reviews` key instead of `avis`.

---

## Step 2: Infrastructure Layer (Services)
**Objective:** Encapsulate third-party library logic (phpGPX).

- [ ] Create `app/Services/GpxService.php`.
- [ ] Move the logic from `Trail::getStatsForTrace()` into this service.
- [ ] The service should take an `UploadedFile` and return a DTO or a structured array containing `distance`, `elevation_gain`, `latitude`, and `longitude`.

---

## Step 3: Business Logic Layer (Actions)
**Objective:** Decouple model events from the HTTP request and move logic out of Observers.

- [ ] Create folder `app/Actions/Trail`.
- [ ] **Create `StoreTrailAction.php`:**
    - This class should handle:
        1. Calling the `GpxService` to get stats.
        2. Storing the GPX file.
        3. Creating the `Trail` record.
        4. Processing and storing `TrailImages`.
- [ ] **Create `UpdateTrailAction.php`:** Similar to store, but handling existing file deletion.
- [ ] **Refactor `TrailObserver.php`:** Remove all logic that relies on the `request()` helper. Keep only pure "side-effect" logic or delete if redundant with Actions.

---

## Step 4: Routing & Middleware
**Objective:** Correctly categorize API endpoints.

- [ ] Move all resource routes (`trails`, `reviews`, `lists`) from `routes/web.php` to `routes/api.php`.
- [ ] Ensure all routes are wrapped in the `auth:sanctum` middleware where applicable.
- [ ] Verify that `RouteServiceProvider` correctly applies the `api` prefix and middleware group.

---

## Step 5: Advanced Filtering (Spatie Query Builder)
**Objective:** Remove manual filtering logic from controllers.

- [ ] Install `spatie/laravel-query-builder` (if not already done).
- [ ] In `TrailController@index`:
    - Replace the manual `$fillables` loop and `whereBetween` logic with:
    ```php
    $trails = QueryBuilder::for(Trail::class)
        ->allowedFilters([
            'title',
            'difficulty',
            AllowedFilter::callback('location', function (Builder $query, $value) {
                // Handle lat_min, lat_max, etc.
            }),
        ])
        ->allowedIncludes(['images', 'reviews'])
        ->paginate();
    ```

---

## Step 6: Performance & N+1 Fixes
**Objective:** Optimize JSON responses.

- [ ] **Eager Loading:** In `TrailController`, ensure relationships are eager-loaded.
- [ ] **Aggregates:** Use `withAvg('reviews', 'note')` in the controller query.
- [ ] **Resource Update:** In `TrailResource.php`, replace `$this->avis->avg('note')` with `$this->reviews_avg_note` to utilize the database-calculated average instead of triggering a collection-level calculation for every row.

---

## Final Validation
- [ ] Run `php artisan pint` to ensure coding style is consistent.
- [ ] Ensure all PhpDoc blocks are updated to reflect class renames.
- [ ] Execute existing tests (`php artisan test`) and create new tests for the `GpxService` and `TrailActions`.
