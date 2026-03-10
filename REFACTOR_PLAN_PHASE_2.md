# TrailMarker: Refactoring Plan - Phase 2 (Architectural Excellence)

This plan focuses on clearing language debt, improving type safety with DTOs, and bringing the `TrailList` domain up to the same elite standard as the `Trail` domain.

---

## Step 1: Global Domain Renaming (English Standardization)
**Objective:** Eliminate the "Franglish" in the codebase to match Laravel's internal conventions.

- [ ] **Rename "Avis" to "Review"**
    - [ ] **Model:** Rename `app/Models/Avis.php` to `Review.php`. Update class name/namespace.
    - [ ] **Controller:** Rename `AvisController.php` to `ReviewController.php`.
    - [ ] **Requests:** Rename `app/Http/Requests/Avis/` to `Review/`. Rename files to `ReviewStoreRequest` and `ReviewUpdateRequest`.
    - [ ] **Policy:** Rename `AvisPolicy.php` to `ReviewPolicy.php`.
    - [ ] **Database:** Create a migration to rename the `avis` table to `reviews`.
    - [ ] **Relationships:** Update `Trail.php` and `User.php` to use `reviews()` instead of `avis()`.
- [ ] **Rename "denivele" to "elevation_gain"**
    - [ ] **Database:** Create a migration to rename the `denivele` column to `elevation_gain` in the `trails` table.
    - [ ] **Codebase:** Update all references in Models, Actions, Resources, and Controllers.

---

## Step 2: Type-Safe Services (The DTO Pattern)
**Objective:** Replace "Mystery Bag" associative arrays with structured Data Transfer Objects.

- [ ] **Create DTO:** Create `app/DTOs/GpxStats.php` as a PHP 8.2 `readonly class`.
    ```php
    namespace App\DTOs;

    readonly class GpxStats {
        public function __construct(
            public float $distance,
            public float $elevationGain,
            public float $latitude,
            public float $longitude,
        ) {}
    }
    ```
- [ ] **Update Service:** Refactor `GpxService.php` to return a `GpxStats` object instead of an array.
- [ ] **Update Actions:** Update `BaseTrailAction.php` to consume the DTO (e.g., `$stats->distance` instead of `$stats['distance']`).

---

## Step 3: Standardizing Trail Lists (Actions)
**Objective:** Move business logic out of `TrailListController` and into dedicated Actions.

- [ ] **Create `StoreTrailListAction.php`:**
    - Encapsulate the creation of the list for the authenticated user.
- [ ] **Create `UpdateTrailListAction.php`:**
    - Encapsulate the logic for updating the list name and **syncing trail associations**.
- [ ] **Refactor Controller:** Update `TrailListController` to inject and use these Actions, keeping the methods thin.

---

## Step 4: Performance Optimization (N+1 Eradication)
**Objective:** Ensure the API stays fast as the data grows.

- [ ] **TrailList Eager Loading:** 
    - In `TrailListController@index`, update the query: 
      `$request->user()->lists()->with('trails.images')->get();`
    - In `TrailListController@show`, use `$trailList->load('trails.images')`.
- [ ] **Trail Aggregates:** (If not already done) Ensure `TrailController` uses `withAvg('reviews', 'note')` for listing and `loadAvg('reviews', 'note')` for single items.

---

## Step 5: Robust Error Handling
**Objective:** Provide better feedback when third-party libraries fail.

- [ ] **GPX Validation:** In `GpxService.php`, throw a custom `App\Exceptions\GpxParsingException` if the file is invalid or empty.
- [ ] **UI Feedback:** Ensure the API returns a meaningful error message (e.g., 422 Unprocessable Entity) when a GPX file cannot be parsed, rather than silently saving a 0km trail.

---

## Final Quality Check
- [ ] **Pint:** Run `./vendor/bin/pint` to fix styling after renames.
- [ ] **Tests:** Run `php artisan test`. All "Avis" tests should be refactored to "Review" tests.
- [ ] **DocBlocks:** Ensure every new class and method has proper `@param` and `@return` types.
