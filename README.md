# Todolist

Lightweight PHP task management application with optional Todoist integration and a simple local task list.

Todolist combines Todoist tasks and locally stored tasks in a single interface. Todoist tasks are displayed with Today tasks first, while local tasks are stored in MySQL and ordered by creation date.

Tasks can be edited, deleted and moved between Todoist and the local Todolist. Moving a local task to Todoist automatically adds it to Today. Both lists support the predefined **B (Business)**, **P (Personal)** and **W (Work)** labels, which are preserved when tasks are moved between the two lists.

Todolist is built on [VC Framework](https://github.com/dsoos1290/vcframework) and is compatible with PHP 5.3 and newer.

## Features

* Todoist task integration
* Local MySQL-based task list
* Todoist Today tasks displayed first
* Today task highlighting
* Complete Todoist tasks
* Edit and delete Todoist tasks
* Edit and delete local tasks
* Move tasks from Todoist to Todolist
* Move tasks from Todolist to Todoist
* Local tasks moved to Todoist are automatically added to Today
* Predefined B (Business), P (Personal) and W (Work) task labels
* Label preservation when moving tasks between lists
* Preservation of unrelated Todoist labels when editing tasks
* Local tasks ordered by creation date
* Compact table-based interface
* Mobile-friendly layout
* Confirmation before task actions
* User authentication
* Password change support
* Per-user Todoist API key storage
* CSRF protection
* Flash messages
* MySQLi database access
* Apache URL rewriting
* Subdirectory installation support
* No Composer required
* PHP 5.3+ compatibility

## Requirements

* PHP 5.3 or newer
* MySQL or MariaDB
* Apache with `mod_rewrite`
* PHP cURL extension for Todoist integration

## Usage

1. Download the latest version: https://github.com/sdaniel1335/todolist/releases/latest
2. Rename `private_html/app/config/db-sample.php` to `db.php`.
3. Open `db.php` and configure your database connection.
4. Import `install.sql` into your database.
5. Open the application in your browser.
6. Log in with the default account:

    * Username: `admin`
    * Password: `admin`
7. Change the default password after logging in.
8. Optionally open **Todoist API** and enter your Todoist API key to enable Todoist integration.

## Todoist Integration

Todoist integration is optional. Without a Todoist API key, the local Todolist can still be used independently.

When Todoist integration is enabled:

* Active Todoist tasks are displayed above the local Todolist.
* Today tasks are displayed first and marked as Today.
* Todoist tasks can be completed, edited or deleted.
* Tasks can be moved from Todoist to the local Todolist.
* Local tasks can be moved to Todoist and are automatically assigned to Today.
* B (Business), P (Personal) and W (Work) labels are transferred between the two lists.