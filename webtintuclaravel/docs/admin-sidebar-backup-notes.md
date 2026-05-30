# Admin sidebar backup

This branch stores the current admin sidebar template from the local XAMPP project so it can be restored if later edits break the sidebar.

## Files backed up

- `webtintuclaravel/resources/views/back/template/sidebar.blade.php`

## Local validation before backup

- `php -l resources/views/back/template/sidebar.blade.php`
- `php artisan view:clear`
- `php artisan view:cache`

## CSS note

The local project also contains `public/css/admin-dashboard.css`, but the current GitHub `main` branch does not contain that file. The local CSS edits made for the sidebar were limited to:

```css
body.vu-admin-body .logo-icon-image {
    padding: 4px;
    overflow: hidden;
}

body.vu-admin-body .logo-icon-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

body.vu-admin-body .nav-item {
    font-family: inherit;
}
```
