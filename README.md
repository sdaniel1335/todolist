# Todolist

Lightweight PHP task manager with optional Todoist integration, built on [VC Framework](https://github.com/dsoos1290/vcframework) for PHP 5.3 and newer.

Todolist brings Todoist tasks and a local MySQL task list into one interface. The local list works without a Todoist API key.

## Features

* Local, per-user MySQL task list with task creation, editing and deletion.
* Optional Todoist integration to display, create, edit, complete and delete tasks.
* Todoist tasks ordered by due date, with undated tasks last and Today tasks highlighted.
* Move tasks between Todoist and the local list; local tasks moved to Todoist are assigned to Today.
* Managed task labels: **B** (Business), **P** (Personal) and **W** (Work), preserved during moves.
* Preservation of unrelated Todoist labels when updating managed labels.
* Local tasks ordered by creation date; compact, mobile-friendly table interface.
* Per-user Todoist API key configuration, login, password changes, CSRF protection and flash messages.
* MySQLi, Apache URL rewriting, subdirectory support and no Composer dependency.

## Requirements

* PHP 5.3+ with MySQLi.
* PHP cURL extension when using Todoist integration.
* MySQL or MariaDB.
* Apache with `mod_rewrite`.

## Installation

1. Download the [latest release](https://github.com/sdaniel1335/todolist/releases/latest) or clone the repository.
2. Rename `private_html/app/config/db-sample.php` to `private_html/app/config/db.php` and configure the database.
3. Import `install.sql` into the configured database.
4. Open the application and sign in with `admin` / `admin`.
5. **Immediately change the default password**.
6. Optionally open **Todoist API** in the application and save your personal Todoist API token.

**Security:** Change the default credentials before exposing the application to the internet. Keep `private_html` inaccessible to direct HTTP requests and use production configuration for deployment.

## Todoist integration

Todoist integration is optional. Without a token, local tasks remain available.

When connected, Todoist tasks appear above local tasks. You can manage Todoist tasks, highlight Today tasks and move tasks between the two lists. Moving a local task to Todoist sets its due date to Today. The application uses the Todoist REST API; an active network connection and a valid token are required.

## Configuration

* Application settings: `private_html/app/config/app.php`.
* Interface language: set `APP_LANG` to `hu` (default) or `en` in the application settings.
* Translation dictionaries: `private_html/app/config/lang_hu.php` and `private_html/app/config/lang_en.php`, using scalar constants such as `LANG_HU_WEEK` and `LANG_EN_WEEK`. The helpers in `private_html/app/c/lang.php` select the language from `APP_LANG`: use `lang('WEEK')` for plain text or `lang_html('WEEK')` for HTML output. Unsupported languages fall back to English.
* Database settings: `private_html/app/config/db.php`.
* Todoist API token: configure through the authenticated application interface.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the release history.
