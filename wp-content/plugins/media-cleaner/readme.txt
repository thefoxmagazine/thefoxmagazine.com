=== Media Cleaner ===
Contributors: TigrouMeow
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H2S7S3G4XMJ6J
Tags: management, admin, file, files, images, image, media, library, upload, clean, cleaning
Requires at least: 4.2
Tested up to: 4.7.2
Stable tag: 3.6.2

Clean your Media Library and Uploads directory. It has an internal trash and recovery features.

== Description ==

Clean your Media Library from the media which aren't used in any of your posts, gallery and so on. It features an internal trash, moving the files in there temporarily for you to make sure the files aren't actually in used; once checked, you can trash them permanently. Before using this plugin, make sure you have a proper backup of your files and database. This is the most important step on the usage of this plugin as you can't trust any file deletion tools. The Pro version of this plugin brings also scanning to the /uploads folder and will detect which files aren't registered in the Media Library, not used in your content and so on. Retina images are also detected and supported, shortcodes, HTML in sidebars and of course your posts, pages and all post types.

**INCOMPATIBILITY**. If you are not using WordPress naturally and using plugin to edit your posts, this plugin will not be able to detect how your images are used. For instance, Visual Composer is not supported as for now.

**UNIQUE PLUGIN**. Such a plugin is difficult to create and to maintain. If you understand WordPress, you probably know why. This plugin tries its best to help you. Get used to it and you will get awesome results. This is the only plugin to propose those functions and even a dashboard to cleanup your WordPress install from unused files.

**DASHBOARD**. Those file will be shown in a specific dashboard. At this point, it will be up to you to delete them. Files detected as un-used are added to a specific dashboard where you can choose to trash them. They will be then moved to a trash internal to the plugin. After more testing, you can trash them definitely.

**FREE / PRO**. The Free version of the plugin works with the media available in your Media Library. The Pro version adds file scanning to your physical /uploads directory.

**AGAIN, BE CAREFUL**. Again, this plugin deletes files so... be careful! Backup is not only important, it is **necessary**. Don't use this plugin if you don't understand how WordPress works.

It has been tested with WP Retina 2x and WPML.

Languages: English, French.

== Installation ==

1. Upload `media-file-cleaner` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go in the Settings -> Media Cleaner and check the appropriate options
3. Go in Media -> Media Cleaner

== Upgrade Notice ==

Replace all the files. Nothing else to do.

== Frequently Asked Questions ==

= Is it safe? =
No! :) How can a plugin that deletes files be 100% safe? ;) I did my best (and will improve it in every way I can) but it is impossible to cover all the cases. On a normal WordPress install it should work perfectly, however other themes and plugins can do whatever they want do and register files in their own way, not always going through the API. I ran it on a few big websites and it performed very well. Make a backup (database + uploads directory) then run it. Again, I insist: BACKUP, BACKUP, BACKUP! Don't come here to complain that it deleted your files, because, yes, it deletes files. The plugin tries its best to help you and it is the only plugin that does it well.

= What is 'Reset' doing exactly? =
It re-creates the Media Cleaner table in the database. You will need to re-run the scan after this.

== Screenshots ==

1. Media -> Media Cleaner

== Changelog ==

= 3.6.2 =
* Fix: When over 1 GO, was displaying a lower size value.
* Fix: Counting wasn't exact with a Filesystem scan.
* Info: Please read the previous changelog as it didn't appear in WP for some reason.
* Add: Check Posts also look for the Media ID in the classes (more secure).
* Info: If you want to give me a bit of motivation, write a review on https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post.

= 3.6.0 =
* Add: Now the Media can be recovered! You can remove your Media through the plugin, make sure they are not in use (by testing your website thoroughly) and later delete them definitely from the trash. I think you will find it awesome.
* Update: Nicer internal icons rather than the old images for the UI.
* Update: Faster and safer for post_content checks.
* Update: This is a big one. The plugin is more clear about what it does. You need to choose either to scan the Media or the Filesystem, and also against what exactly. There has also been a few fixes and it will work on more big installs. If it fails, you can remove a few scanning options, and I will continue to work on making it perfect to support huge installs with all the options on.

= 3.2.8 =
* Update: Show a better edit media screen.
* Update: Will show the same number of items as in the Media Library (before it was fixed to 15 items per page).
* Fix: Was displaying warning if the number of items per page in the Media page is not set.

= 3.2.0 =
* Fix: HTML adapted to WP 4.5.1.
* Fix: Doesn't break if there is an error on the server-side. Display an alert and continue.
* Update: Can select more than one file for non-Pro.
* Fix: Issue with PHP 7.

= 3.0.0 =
* Add: Option for resolving shortcode during analysis.
* Update: French translation. Big thanks to Guillaume (and also for all his testing!).
* Info: New name, fresh start. This plugin changed completely since it very first release :)

= 2.5.0 =
* Add: Delete the unused directories.
* Add: Doesn't break when there are too many files in the system.
* Add: Pro version with better support.
* Update: Improved detection of unused files.
* Fix: UTF8 filenames skipped by default but can be scanned through an option.
* Fix: Really many fixes :)
* Info: Contact me if you have been using the plugin for a long time and love it.

= 2.4.2 =
* Add: Inclusion of gallery post format images.
* Fix: Better gallery URL matching.
* Info: Thanks to syntax53 for those improvements via GitHub (https://github.com/tigroumeow/media-file-cleaner/pull/3). Please review Media Cleaner if you like it. The plugin needs reviews to live. Thank you :) (https://wordpress.org/support/view/plugin-reviews/media-file-cleaner)

= 2.4.0 =
* Fix: Cross site scripting vulnerability fixes.
* Change: Many enhancements and fixes made by Matt (http://www.twistedtek.net/). Please thanks him :)
* Info: Please perform a "Reset" in the plugin dashboard after installing this new version.

= 2.2.6 =
* Fix: Scan for multisite.
* Change: options are now all enabled by default.
* Fix: DB issue avoided trashed files from being deleted permanently.

= 2.0.2 =
* Works with WP 4.
* Gallery support.
* Fix: IGNORE function was... ignored by the scanning process.

= 1.9.0 =
* Add: thumbnails.
* Add: IGNORE function.
* Change: cosmetic changes.
* Add: now detects the custom header and custom background.
* Change: the CSS was updated to fit the new Admin theme.

= 1.7.0 =
* Change: the MEDIA files are now going to the trash but the MEDIA reference in the DB is still removed permanently.
* Stable release.
* Change: Readme.txt.

= 1.4.0 =
* Add: check the meta properties.
* Add: check the 'featured image' properties.
* Fix: keep the trash information when a new scan is started.
* Fix: remove the DB on uninstall, not on desactivate.

= 1.2.2 =
* Add: progress %.
* Fix: issues with apostrophes in filenames.
* Change: UI cleaning.

= 1.2.0 =
* Add: options (scan files / scan media).
* Fix: mkdir issues.
* Change: operations are buffered by 5 (faster).

= 0.1.0 =
* First release.
