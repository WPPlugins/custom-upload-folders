=== Custom Upload Folders ===
Contributors: brasofilo
Donate link: http://plugins.brasofilo.com
Tags: upload, custom folder, media, ftp
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Organize your uploaded files in custom folders.
Available options: by Year-Month-Day, File Type, Post ID or Author Display Name. 

== Description ==

Adds a new option in the Media Settings page to select a different folder organization for your uploads.
Simply visit your `/wp-admin/options-media.php` page and select the desired folder structure.

**Important**: Not tested in Multisite.

= Acknowledgements =
_Based on the following WordPress Answers_

* [Organize uploads by year, month and day](http://wordpress.stackexchange.com/q/70946/12615)
* [How Can I Organize the Uploads Folder by Slug (or ID, or FileType, or Author)?](http://wordpress.stackexchange.com/q/25894/12615)

= Localizations =
* Português
* Español

== Installation ==
1. Upload `custom-upload-folders.zip` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the *Plugins* menu in WordPress.
3. Go to *Settings -> Media* and set the desired folder structure.

= Uninstall =
The plugin option will be deleted via unsinstall.php.


== Frequently Asked Questions ==
= Doubts, bugs, suggestions =
Don't hesitate in posting a new topic here in [WordPress support forum](http://wordpress.org/support/plugin/custom-upload-folders).


== Screenshots ==
1. Settings page

== Changelog ==
**Version 1.2**

* Fixed typo in plugin URL. Props to [GermanKiwi](http://wordpress.org/support/topic/incorrect-plugin-url-on-the-plugins-page).
* Updated translations.

**Version 1.1**

* Bug fix: better handling of the option `Add Year-Month to the subfolder -> /custom-folder/year-month/`

**Version 1.0**

* Plugin launched

== Upgrade Notice ==

= Version 1.1 =
Better handling of some folder variations. `/slug/y-m/`, `/post-id/y-m/`, `/y-m-d/y-m/` are not necessary.

= Version 1.0 =
Plugin launched