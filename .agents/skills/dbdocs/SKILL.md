---
name: database-documentation
description: Synchronize the Dooeed schema documentation in dbdiagram.dbml with Laravel migrations. Use when asked to update the database diagram or when migration changes affect tables, columns, indexes, and foreign keys. Not for running migrations or modifying application data.
---

# Dooeed Database Documentation

Target: `dbdiagram.dbml` in the Dooeed project root; its location in this workspace is `/dooeed/dbdiagram.dbml`. For other checkouts, use the active project root.

## Source of Truth and Scope

- Read `AGENTS.md`, the current DBML, and the relevant migrations in `database/migrations/` before editing.
- Use the final state produced by all migration `up()` methods in filename order. Account for `Schema::table`, type changes, renames, and drops; do not simply copy the migrations that create tables.
- For a full synchronization request, compare all migrations. For changes to a specific feature, update the affected tables and relationships and report any other discrepancies found.
- DBML documents the structure defined by the code. If comparison with an existing database is needed, use Boost `database-schema` in read-only mode when available and report differences; do not overwrite the migration-defined design based on a local database that may be out of date.
- Do not modify migrations, models, dependencies, or data, or run migrations solely to fix the diagram. Do not upload the schema to an external service unless requested by the user.

## Mappings to Preserve

- Match primary and foreign key types to the migrations. Dooeed domain tables use UUIDs; do not replace them with auto-incrementing integers. `audit_logs.id` uses `$table->id()`, unlike the domain UUIDs.
- Preserve string lengths, decimal precision and scale, nullability, defaults, primary keys, unique and composite indexes, and explicit index names. Application or model defaults are not automatically database defaults.
- Use the logical type `uuid` for `$table->uuid()` / `foreignUuid()`; use auto-incrementing `bigint` for `$table->id()`. Follow the existing DBML representation for other types; when documenting engine-specific physical types, identify the database engine.
- `timestamps()` creates nullable `created_at` and `updated_at` columns; `softDeletes()` adds a nullable `deleted_at`. Do not add `updated_at` to `audit_logs`, which only has `created_at` with a default of the current time.
- Role/status columns defined with `string()` remain varchar. Values validated by the application may be described in notes, but must not be converted into database enums or CHECK constraints.
- Document foreign keys only when the migration defines a constraint. A column named `*_id` or an Eloquent relationship alone is not evidence of a constraint; the same applies to Sanctum polymorphic relationships.
- Record `cascadeOnDelete`, `restrictOnDelete`, and `nullOnDelete` in `Ref` definitions. Audit records reference `users` through `admin_id` and `target_user_id` with restricted deletion; soft deletion does not remove relationships or audit history.
- Preserve domain notes, colors, and groupings that remain accurate. If a note contradicts a migration, correct it based on the migration and explain the discrepancy.

## Editing and Verification

1. Identify differences in tables, columns, indexes, and relationships within the task scope, then edit the existing DBML file; do not create a duplicate diagram.
2. Use the [official DBML syntax](https://dbml.dbdiagram.io/docs/). Foreign key nullability is determined by the column's nullability; retain relationship notation supported by the project's parser. String defaults use single quotes; expression defaults use backticks.
3. Ensure every `Ref` points to an existing table and column, key types match, and there are no duplicate table, column, or relationship definitions. Check nullability and indexes against the source migrations.
4. Use an existing DBML parser or validator when available. Do not automatically install new dependencies. If no parser is available, perform structural checks and state that parser validation was not performed.
5. Review the diff and whitespace. Documentation-only changes do not require PHP tests, a frontend build, or a database connection.

Report the files changed, the main tables and relationships synchronized, the source migrations, verification results, and any discrepancies intentionally left unresolved. Do not claim that an existing database has changed merely because the DBML was updated.
