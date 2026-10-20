# Changelog

All notable changes to this project will be documented in this file.

## [0.1.0] - 2026-10-20

### Added

- Initial Todolist application built on VC Framework with PHP 5.3+ compatibility.
- Per-user local MySQL task management with creation, editing and deletion.
- Optional Todoist REST API integration and per-user API token configuration.
- Todoist task listing, creation, completion, editing and deletion.
- Today task highlighting and due-date sorting with undated tasks last.
- Bidirectional task movement between Todoist and the local list, with automatic Today assignment for tasks sent to Todoist.
- B (Business), P (Personal) and W (Work) labels, preserved across task moves.
- Preservation of unrelated Todoist labels during managed-label updates.
- Compact, mobile-friendly task interface with action confirmations.
- User authentication, password changes, CSRF protection and flash messages.
- MySQLi database support, Apache URL rewriting and subdirectory installation support.
- No Composer dependency.