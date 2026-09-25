---
name: localization-preferences
description: Implement or modify Dooeed language and currency preferences, including Indonesian/English UI behavior, active controls, searchable currency selection, and money formatting. Use for every new user-facing page or monetary display.
---

# Localization preferences

Use this skill whenever adding or changing a user-facing page, Blade typography, dynamic UI text, or monetary amount.

## Language

- Support only `id` and `en`, persisted in `users.preferred_locale`.
- Load the saved preference before rendering page behavior. The active language control must display the persisted value; never leave it blank or undefined.
- Translate all visible typography on the affected page, including Admin Panel pages: headings, descriptions, form labels, placeholders, buttons, empty states, menus, alerts, and confirmation text.
- Apply translation again after API-driven or JavaScript-rendered content is inserted into the DOM, including admin tables, detail panels, pagination, modals, and alerts.
- Set `<html lang>` to the active locale. Keep Indonesian as the default and provide English equivalents for every new user-facing string.
- When reading profile preferences through Axios, preserve the API envelope: profile values are in `response.data.data`.

## Currency

- Persist ISO 4217 codes in `users.preferred_currency`.
- Build the option set from `config('money.currencies')`; do not maintain a limited manual list.
- Use one searchable currency field. The saved currency must appear in that same field when the page opens; do not add a separate search input.
- Format every user-visible monetary value with the saved locale and currency. Use `akaunting/laravel-money` on PHP and `Intl.NumberFormat` in JavaScript.
- Currency selection changes presentation only. Do not silently convert stored amounts without an explicitly requested exchange-rate policy.

## Dynamic translation

- Use `stichoza/google-translate-php` only for explicitly requested dynamic content. Cache results and preserve original text on failure. Do not make external translation requests during normal page rendering.

## Verification

- Add focused Pest coverage for persisted preferences and API response shape when backend behavior changes.
- Run the relevant tests and `npm run build` after Blade, JavaScript, or CSS changes.
