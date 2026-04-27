# Frontend Patterns & Conventions

## Stack

| Layer | Technology | Delivery |
|---|---|---|
| CSS framework | Tailwind CSS | CDN (dev) / Vite (prod) |
| Interactivity | Alpine.js | CDN |
| Templating | Laravel Blade | Server-rendered |
| Icons | Font Awesome | CDN |

No React, Vue, or Livewire. All interactivity is handled by Alpine.js inline in Blade templates.

---

## Layouts

Two root layouts are used — one per application surface:

### `layouts/app.blade.php`
Tenant application layout. Features an **indigo-900 sidebar**, top navigation bar, and a main content area. Used by all tenant-facing routes (`/dashboard`, `/customers`, `/invoices`, etc.).

### `layouts/platform.blade.php`
Platform admin layout. Used exclusively by `/platform/*` routes accessed by super-admins (users with `tenant_id IS NULL`).

Both layouts share the same extension points:

```blade
@yield('title')        {{-- HTML <title> tag content --}}
@yield('page-title')   {{-- Heading shown in the top bar --}}
@yield('content')      {{-- Main page body --}}
```

Child views extend a layout and fill the sections:

```blade
@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
    {{-- page content --}}
@endsection
```

---

## Design System

### Colours
- **Sidebar:** `bg-indigo-900` with `text-white`; active items use `bg-indigo-700`
- **Cards:** `bg-white rounded-xl shadow-sm` for content panels
- **Stats cards:** white background with a `border-l-4` coloured accent (indigo, green, red, yellow)

### Status Badges
Badges must use these classes consistently across the app:

| Status | Classes |
|---|---|
| Active / Paid | `bg-green-100 text-green-800` |
| Suspended / Unpaid | `bg-red-100 text-red-800` |
| Partial / Trial | `bg-yellow-100 text-yellow-800` |

### Custom CSS
Only two custom CSS rules are permitted outside Tailwind utilities:

```css
[x-cloak] { display: none !important; }   /* prevents Alpine FOUC */

.sidebar-item.active { /* active nav highlight */ }
```

All other styling must use Tailwind utility classes directly.

---

## Alpine.js Patterns

### Responsive Sidebar
```html
<div x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
    <!-- sidebar is open by default on desktop, closed on mobile -->
</div>
```

### Dropdown Menus
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Menu</button>
    <div x-show="open" @click.away="open = false">
        <!-- dropdown items -->
    </div>
</div>
```

### Preventing FOUC
Add `x-cloak` to any element that Alpine controls to prevent a flash of un-styled content on page load:

```html
<div x-cloak x-show="open">...</div>
```

### Auto-Dismissing Alerts
Flash messages auto-remove after 5 seconds:

```html
<div x-data x-init="setTimeout(() => $el.remove(), 5000)">
    {{ session('success') }}
</div>
```

---

## Forms

- Always include `@csrf` inside every `<form>` tag
- Use `method="POST"` with `@method('PUT')` or `@method('DELETE')` for non-POST verbs (HTML forms only support GET/POST)
- Validation errors are shown via the global block in the layout — no per-field inline error handling is needed in individual views unless contextual placement is required

```blade
@if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

---

## Currency

Always use the `taka()` helper. Never format amounts manually.

```blade
{{-- Correct --}}
{{ taka($invoice->amount) }}

{{-- Wrong — do not do this --}}
৳{{ number_format($invoice->amount, 2) }}
BDT {{ $invoice->amount }}
```

The `taka()` helper ensures consistent formatting, the correct symbol, and makes future locale or formatting changes a single-file fix.
