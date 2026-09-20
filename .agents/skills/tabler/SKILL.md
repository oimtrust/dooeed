---
name: tabler-development
description: "Always invoke when the user's message includes 'tabler' in any form. Also invoke for: building layouts (page, page-wrapper, page-center, containers), styling UI components (cards, tables, navbars, forms, inputs, badges, alerts, avatars, modals, offcanvas, dropdowns), auth pages (sign-in, sign-up, forgot-password), dashboards with sidebars/topbars, dark mode theming, and Tabler + Vite/npm work. The core use case: writing or fixing Tabler component classes in HTML templates (Blade). Skip for backend PHP logic, database queries, API routes, and JavaScript with no HTML/CSS component."
license: MIT
metadata:
  author: dooeed
---

# Tabler UI Development

Tabler is a Bootstrap-based dashboard UI kit. This project uses Tabler as its
**only** CSS framework (Tailwind was removed). Do NOT introduce Tailwind
utility classes or any other CSS framework.

Sources:

- Installation: <https://docs.tabler.io/ui/getting-started/installation>
- Component/layout demos: <https://preview.tabler.io> (e.g. `/sign-in.html`)
- npm package: `@tabler/core` (pinned version in `package.json`)

## Installation (already wired via npm + Vite)

```bash
npm install @tabler/core
```

```css
/* resources/css/app.css */
@import '@tabler/core/dist/css/tabler.min.css';
```

```js
/* resources/js/app.js */
import '@tabler/core/dist/js/tabler.min.js';
```

Optional plugin stylesheets live under `@tabler/core/dist/css/`
(`tabler-flags`, `tabler-payments`, `tabler-socials`, `tabler-vendors`,
`tabler-marketing`). Import only the ones a page actually needs.
For dark/light theme switching, also import
`@tabler/core/dist/js/tabler-theme.min.js`.

Rules:

- Never load Tabler from CDN when the npm package is installed. One source.
- Never add Tailwind (`tailwindcss`, `@tailwindcss/vite`, `@import 'tailwindcss'`,
  `@source`, `@theme`) back into the project.
- Page-specific CSS belongs in `resources/css/` and must be registered in the
  `laravel-vite-plugin` `input` array in `vite.config.js`.

## Layout Patterns

Auth/centered pages (mirrors `preview.tabler.io/sign-in.html`):

```html
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="/" class="navbar-brand navbar-brand-autodark">Dooeed</a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h1 class="h2 text-center mb-4">Login to your account</h1>
        <!-- form -->
      </div>
    </div>
    <div class="text-center text-secondary mt-3">
      Don't have account yet? <a href="/register">Sign up</a>
    </div>
  </div>
</main>
```

App pages:

```html
<div class="page">
  <div class="page-wrapper">
    <div class="page-header d-print-none"><!-- title + actions --></div>
    <div class="page-body">
      <div class="container-xl"><!-- cards, tables --></div>
    </div>
  </div>
</div>
```

- Narrow content: `container-tight`. Standard app content: `container-xl`.
- Card anatomy: `.card` > `.card-header` (`.card-title` + `.card-actions`) >
  `.card-body` > `.card-footer`. Dividers between body blocks: `.hr-text`.

## Components

Prefer Tabler components over hand-rolled markup:

| Need           | Tabler class(es)                                              |
| -------------- | ------------------------------------------------------------- |
| Form input     | `.form-control`, `.form-label`, `.form-hint`, `.form-select`  |
| Validation     | `.is-invalid` + `.invalid-feedback`, `.is-valid`              |
| Checkbox/radio | `.form-check` > `.form-check-input` + `.form-check-label`     |
| Buttons        | `.btn .btn-primary`, `.w-100`, `.btn-loading` (spinner state) |
| Alerts         | `.alert .alert-danger/.alert-success`, `.btn-close` dismiss   |
| Password field | `.input-group.input-group-flat` + eye toggle button           |
| Helper link    | `.form-label-description` inside `.form-label`                |
| Table          | `.table .table-vcenter .card-table` inside `.card`            |
| Badge/avatar   | `.badge .bg-green`, `.avatar`                                 |
| Modal/offcanvas| Bootstrap `data-bs-toggle="modal"` (Tabler JS bundles it)    |

- Loading state: add `.btn-loading` + `disabled` to the submit button, never
  swap button text manually.
- API 422 errors map to fields: input gets `.is-invalid`, message goes in a
  sibling `.invalid-feedback` div. Clear both on resubmit.
- Global form messages use dismissible `.alert` blocks above the form.

## Icons

Tabler has no icon font. Use inline SVGs from <https://tabler.io/icons>
with `class="icon"` (inside buttons/inputs) — same pattern as the demo pages.
Do NOT add other icon libraries.

## Theming

- Dark mode via `tabler-theme.min.js` + `data-bs-theme` handling; accent color
  and font are CSS variables (`--tblr-primary`, …). Override variables in
  `resources/css/`, never edit `node_modules`.
- Check `preview.tabler.io` before inventing a layout — reuse the demo markup,
  trimmed of demo-only extras (theme-settings offcanvas, social buttons,
  marketing illustrations).

## Anti-Patterns

- Tailwind utilities (`flex`, `grid`, `px-4`, `text-gray-500`, …) in Blade.
- Custom CSS that duplicates a Tabler component.
- Putting business logic or API payload shapes into Blade/JS — the API contract
  owns those; FE only renders `UserResource`-shaped data.
- Fat page scripts: page JS only wires DOM events to `domains/<name>/api.js`;
  shared DOM helpers live in `domains/<name>/components/`, generic HTTP in
  `resources/js/lib/`.
