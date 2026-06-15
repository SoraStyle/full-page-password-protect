# Full Page Password Protect

**English** | [日本語](README-ja.md)

WordPress plugin that extends the built-in password protection feature to protect the entire page, not only the post content.

- **Version:** 1.0.0
- **Requires WordPress:** 5.8+
- **Requires PHP:** 7.4+
- **License:** [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

## Overview

With the default WordPress behavior, only the post content may be hidden while other parts of the page can still appear, such as the title, featured image, custom fields, and theme output.

This plugin replaces the full singular page with a simple password screen until the correct password is entered. It uses the standard WordPress password form and password handling. It does not add a separate login system, custom cookies, user accounts, or password storage.

It also helps reduce accidental exposure of protected content in archive pages, search results, taxonomy listings, and REST API responses.

## Features

- Replaces the full singular page with a password screen
- Uses the standard WordPress password form and password handling
- Helps prevent titles, images, and other page elements from appearing before the password is entered
- Sends no-cache headers on the password screen
- Adds `noindex, nofollow` to the password screen
- Reduces protected content exposure in REST API responses
- Excludes protected posts from public listings by default
- Provides an optional title-only listing mode
- Supports selected enabled post types
- Includes translatable default messages for the password screen
- Does not load third-party services, tracking scripts, or remote assets

## Installation

1. Upload the `full-page-password-protect` folder to `/wp-content/plugins/`, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Settings > Full Page Password**.
4. Confirm the target post types and listing mode.

Password protection itself is still configured on each post or page using the standard WordPress **Visibility > Password protected** setting.

## Settings

| Setting | Description |
| --- | --- |
| **Enable plugin** | Turns full page password protection on or off. |
| **Protected post types** | Select enabled post types where password-protected posts should receive full page protection. |
| **Archive display mode** | `exclude` removes protected posts from listing queries. `title_only` keeps protected posts in listings but hides excerpts, content, and featured images. |
| **Password message** | The message displayed above the password form on the password screen. |

## FAQ

**Does this plugin use a custom password system?**  
No. It uses the standard WordPress post password feature.

**Does this plugin store passwords?**  
No. Passwords are managed by WordPress in the usual way.

**Does it work with block themes?**  
Yes. The plugin works with both classic and block themes.

**Does it protect REST API output?**  
Yes. For protected posts, content-related fields are hidden from public REST responses according to the plugin settings.

**Can I use it with existing password-protected posts?**  
Yes. Set a password with the standard WordPress Visibility setting and enable the plugin.

## Privacy

This plugin does not collect, store, or transmit personal data. It does not call external services, load remote assets, or add tracking scripts.

## Development

### Project structure

```text
full-page-password-protect/
├── assets/css/frontend.css
├── includes/
│   ├── class-fppp-archive.php
│   ├── class-fppp-plugin.php
│   ├── class-fppp-protector.php
│   ├── class-fppp-rest.php
│   └── class-fppp-settings.php
├── languages/
├── templates/password-form.php
├── full-page-password-protect.php
├── readme.txt
└── uninstall.php
```

### Translation

- Text domain: `full-page-password-protect`
- Japanese translation: `languages/full-page-password-protect-ja.po`

To compile the `.mo` file after editing the `.po` file:

```bash
msgfmt -o languages/full-page-password-protect-ja.mo languages/full-page-password-protect-ja.po
```

## Links

- [Product page](https://sora-style.org/products/full-page-password-protect/)
- [WordPress.org plugin page](https://wordpress.org/plugins/full-page-password-protect/)
- [Donate](https://sora-style.org/donate/)
- [Sora Style](https://sora-style.org/)

## Changelog

### 1.0.0

- Initial release.
