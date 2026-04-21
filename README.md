# Caawiye Care

Caawiye Care is a Laravel 12 application for healthcare and business operations management. The codebase includes role and permission management, API endpoints, admin workflows, reporting/export features, and modular domain areas built on Laravel, Livewire, Alpine.js, and Vite.

## Stack

- PHP 8.3+
- Laravel 12
- Livewire 3
- Alpine.js
- Vite
- MySQL
- Pest
- PHPStan
- Laravel Pint

## Quick Start

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

## Useful Commands

```bash
composer dev
composer pest
composer phpstan
composer pint
npm run build
```

## Documentation

Project documentation is organized under [`docs/README.md`](docs/README.md). Additional internal development notes live in the [`Follow-Ai`](Follow-Ai) directory.

## Commit Message Standard

This repository already uses `commitlint` with conventional commits. Preferred commit format:

```text
type: short summary
```

Allowed types:

- `feat`
- `fix`
- `docs`
- `style`
- `refactor`
- `perf`
- `test`
- `chore`
- `ci`
- `build`
- `revert`

Guidelines:

- Keep the subject in lowercase.
- Make the summary specific and action-oriented.
- Use `docs` for README or documentation updates.
- Use `chore` for cleanup that does not change product behavior.

Examples:

```text
docs: add project setup and commit guidelines to readme
chore: remove unused legacy build and suspicious wordpress files
fix: resolve permission issue in admin workflow
```

## Notes

- `api.json` is used by Scramble API documentation output and should remain in the project root.
- The project uses Vite for frontend builds; legacy Mix configuration is not required.
