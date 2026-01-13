# Galactic Menagerie API

A Laravel 11 JSON API for managing alien wildlife enclosures and animals. This API enforces business rules to ensure species are placed in compatible environments and enclosures are never over capacity.

---

## Table of Contents

* [Installation](#installation)
* [Running the Application](#running-the-application)
* [Architecture & Design Decisions](#architecture--design-decisions)
* [API Endpoints](#api-endpoints)
* [Business Rules](#business-rules)
* [Testing](#testing)

---

## Installation

### Prerequisites

* PHP 8.2+
* Composer
* SQLite (or any other supported database)
* Node.js & NPM (for Vue 3 frontend, optional)

### Setup

1. Clone the repository:

```bash
git clone <repository_url>
cd galactic-menagerie-api
```

2. Install PHP dependencies:

```bash
composer install
```

3. Set up environment variables:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure SQLite (or another DB) in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

5. Run migrations:

```bash
php artisan migrate
```

6. (Optional) Seed data:

```bash
php artisan db:seed
```

7. Start the application:

```bash
php artisan serve
```

The API will now be available at `http://127.0.0.1:8000`.

---

## Architecture & Design Decisions

This project uses a **Service-Repository-DTO** pattern to ensure clean separation of concerns and maintainable, testable code.

### Key Decisions

* **Service Layer:**
  All business logic, including transfer rules and validation, is handled in `AnimalService` and `EnclosureService`.

    * Example: The `transfer` method ensures both the target environment and enclosure capacity are valid before moving an animal.

* **Repository Pattern:**
  Encapsulates database operations.

    * `AnimalRepository` and `EnclosureRepository` handle all persistence, including enum normalization and DB transactions.

* **DTOs (Data Transfer Objects):**
  Requests are converted into typed DTOs (`CreateAnimalDTO`, `TransferAnimalDTO`) for type safety and clarity in services.

* **Enums:**
  `EnvironmentType` ensures only valid environments are used and eliminates string errors.

* **Custom Validation Rules:**
  `ValidateNameRule` enforces proper naming conventions, ensuring names are human-readable and within length constraints.

* **Centralized Logging:**
  `DomainLogger` captures domain-level events and business rule violations for auditability.

* **Reusable API Response:**
  `ApiResponse` standardizes JSON responses across all endpoints.

* **Error Handling:**
  Domain-specific exceptions (`EnclosureCapacityExceededException`, `InvalidEnvironmentException`) provide meaningful feedback with proper HTTP codes.

---

## API Endpoints

| Method | Endpoint                 | Description                             |
| ------ | ------------------------ | --------------------------------------- |
| POST   | `/enclosures`            | Create an enclosure                     |
| GET    | `/enclosures`            | List enclosures                         |
| GET    | `/enclosures/{id}`       | Retrieve an enclosure                   |
| PUT    | `/enclosures/{id}`       | Update an enclosure                     |
| DELETE | `/enclosures/{id}`       | Delete an enclosure                     |
| POST   | `/animals`               | Create an animal                        |
| GET    | `/animals`               | List animals                            |
| GET    | `/animals/{id}`          | Retrieve an animal                      |
| PUT    | `/animals/{id}`          | Update an animal                        |
| DELETE | `/animals/{id}`          | Delete an animal                        |
| POST   | `/animals/{id}/transfer` | Transfer an animal to another enclosure |

---

## Business Rules

1. **Survival Rule:** An animal cannot be placed in an enclosure if the enclosure type does not match its preferred environment.
2. **Capacity Rule:** An animal cannot be added to an enclosure if it is already at maximum capacity.
3. **Transfer Rule:** Moving an animal validates both the survival and capacity rules.

---

## Testing

Feature tests are included to cover:

* Animal creation
* Enclosure creation
* Transfer success
* Transfers violating survival or capacity rules

Run tests using:

```bash
php artisan test
```

---

## Notes

* Enums are used for environment types to enforce domain integrity.
* All business logic resides in services to keep controllers thin.
* Repositories handle DB normalization (e.g., enum → string).
* Logging and structured responses ensure maintainable and observable API behavior.

This design prioritizes **separation of concerns, maintainability, and testability** while ensuring all domain rules are strictly enforced.
