=== Full Page Password Protect ===
Contributors: ayumitabuchi
Donate link: https://sora-style.org/donate/
Tags: password, privacy, protected-content, rest-api, access-control
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Protects the full page with the WordPress standard password form, not only the post content.

== Description ==

Full Page Password Protect extends the built-in WordPress password protection feature.

With the default WordPress setting, only the post content may be hidden while other parts of the page can still appear, such as the title, featured image, custom fields, and theme output. This plugin replaces the full page with a simple password screen until the correct password is entered.

The plugin uses the standard WordPress password form and password handling. It does not add a separate login system, custom cookies, user accounts, or password storage.

It also helps reduce accidental exposure of protected content in archive pages, search results, taxonomy listings, and REST API responses.

= Key features =

* Replaces the full singular page with a password screen.
* Uses the standard WordPress password form and password handling.
* Helps prevent titles, images, and other page elements from appearing before the password is entered.
* Sends no-cache headers on the password screen.
* Adds noindex, nofollow to the password screen.
* Reduces protected content exposure in REST API responses.
* Excludes protected posts from public listings by default.
* Provides an optional title-only listing mode.
* Supports selected public post types.
* Includes translatable default messages for the password screen.
* Does not load third-party services, tracking scripts, or remote assets.

== Installation ==

1. Upload the `full-page-password-protect` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to Settings > Full Page Password.
4. Confirm the target post types and listing mode.

Password protection itself is still configured on each post or page using the standard WordPress Visibility > Password protected setting.

== Settings ==

= Enable plugin =

Turns full page password protection on or off.

= Protected post types =

Select public post types where password-protected posts should receive full page protection.

= Archive display mode =

`exclude` removes protected posts from listing queries.

`title_only` keeps protected posts in listings but hides excerpts, content, and featured images.

= Password message =

The message displayed above the password form on the password screen.

== Screenshots ==

1. Settings screen. Configure target post types, archive display mode, password message, and other options under Settings > Full Page Password.
2. Post or page editor. Choose WordPress standard Visibility > Password protected and set a password for each post or page.
3. Password entry screen. Before the correct password is entered, the full page is replaced with this screen showing only the message and password form.

== Frequently Asked Questions ==

= Does this plugin use a custom password system? =

No. It uses the standard WordPress post password feature.

= Does this plugin store passwords? =

No. Passwords are managed by WordPress in the usual way.

= Does it work with block themes? =

Yes. The plugin works with both classic and block themes.

= Does it protect REST API output? =

Yes. For protected posts, content-related fields are hidden from public REST responses according to the plugin settings.

= Can I use it with existing password-protected posts? =

Yes. Set a password with the standard WordPress Visibility setting and enable the plugin.

== Privacy ==

This plugin does not collect, store, or transmit personal data.

It does not call external services, load remote assets, or add tracking scripts.

== Changelog ==

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.0 =

Initial release.
