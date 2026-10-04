# Todolist

Lightweight PHP task management application with optional Todoist integration and a simple local task list.

Todolist combines Todoist tasks and locally stored tasks in a single interface. Todoist tasks are displayed in ascending due-date order, with undated tasks last, while local tasks are stored in MySQL and ordered by creation date.

Tasks can be edited, deleted and moved between Todoist and the local Todolist. Moving a local task to Todoist automatically adds it to Today. Both lists support the predefined **B (Business)**, **P (Personal)** and **W (Work)** labels, which are preserved when tasks are moved between the two lists.

Todolist is built on [VC Framework](https://github.com/dsoos1290/vcframework) and is compatible with PHP 5.3 and newer.

## Features

* Todoist task integration
* Local MySQL-based task list
* Todoist tasks ordered by due date, with undated tasks last
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
* Tasks are ordered by due date, with undated tasks last; today's tasks are marked as Today.
* Todoist tasks can be completed, edited or deleted.
* Tasks can be moved from Todoist to the local Todolist.
* Local tasks can be moved to Todoist and are automatically assigned to Today.
* B (Business), P (Personal) and W (Work) labels are transferred between the two lists.
## Manual FTP deployment from GitHub

The **Deploy** workflow runs only when manually started on `main`. It uses plain,
unencrypted FTP in passive mode. Credentials and uploaded content are not
protected by TLS.

1. Merge the deployment workflow into `main`.
2. In **Settings → Secrets and variables → Actions → New repository secret**,
   create `FTP_PASSWORD` with the FTP account password. Never commit it.
3. The defaults are host `208.82.114.165`, username `danielco`, port `21`, and
   target `/home/danielco/public_html/todolist/`. To override them, add repository
   variables `FTP_HOST`, `FTP_USER`, or `FTP_DEPLOY_DIR` in the same settings.
   The target must already exist. If the FTP account is jailed, use the path
   visible in your FTP client, for example `/public_html/todolist/`.
4. Open **Actions → Deploy → Run workflow**, select `main`, and leave
   **Upload application files** disabled for the first run. This checks login,
   the target directory and passive data transfer without writing files.
5. After that check passes, run it again with **Upload application files** enabled.

Uploads preserve the repository layout and include the root `index.php` and
`.htaccess`, `public_html/`, `private_html/app/`, and `private_html/framework/`.
The live `private_html/app/config/db.php`, logs, Git metadata, workflows,
documentation and `install.sql` are excluded. Existing application files are
overwritten and uploaded sizes are checked; remote files are never deleted and
SQL is never imported. Keep the existing production database configuration.

Deployment is not atomic: a failed upload can leave some application files
updated. Keep a backup before deployment and inspect the site after a successful
run. No live FTP connection has been verified from GitHub until the first check
workflow succeeds.
