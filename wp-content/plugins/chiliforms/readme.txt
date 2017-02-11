=== ChiliForms ===
Contributors: KonstruktStudio
Donate link: https://www.chiliforms.com/
Tags: form, form builder, contact form, email, AJAX, contact, feedback, form, multilingual
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use drag-n-drop contact form builder plugin for your blog or website.

== Description ==

A better WordPress contact form builder. 
ChiliForms is here to make your life a bit easier. With ChiliForms creating simple contact form sometimes takes less than a minute of your time! Just create new form, drop required fields, paste it to your page and you’re all set.
You can see <a href="https://www.chiliforms.com/demos" title="ChiliForms contact form demos">demos</a> of what it currently can do.

Main features:

* Clean and fast drag n drop user interface build with ReactJS
* Live preview in form editor
* Mark any number of fields as required
* All the necessary fields (Single line, Multiline, Dropdown, Radio, Checkboxes, Email, Url) are available
* add reCAPTCHA to block bots
* Custom field width
* You can deactivate the form for some time, user see a message
* AJAX form submit (user receives animated message, no page reload)
* Client-side fields validation for better user experience
* Server side validation will work as well, for better security
* Admin email notification on new submisions with form contents
* Custom colors for forms
* You can mark important submission entries as starred
* You can use filters, to see only starred entries or only unread items
* Styled theme and HTML-only version
* Fixed width or percent of parent container
* Dedicated preview page on theme page
* Use shortcode to insert form on page

ChiliForms was tested and optimized for compatibility with following themes:

Free:

* Twenty Sixteen
* Twenty Fifteen
* Twenty Twelve
* Twenty Thirteen
* Sydney
* Spacious
* ColorMag
* Customizr
* Hueman
* Fictive
* Poseidon
* MH Magazine Lite
* ResponsiveBoat
* Oblique
* Sela
* Amadeus
* Hitchcock
* Lovecraft

Premium:

* Avada
* Salient

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/chiliforms` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to ChiliForms -> Forms to create your first form.
4. Use preview to see how it looks in your theme.
5. Save form and use shortcode to embed form on page.
6. View submissions in Chiliforms -> Entries

== Frequently Asked Questions ==

= Can I use it to create a Contact Form? =

Sure, it has all the necessary fields for this and more

= Can contact form style be customized? =

Yes, you can change form theme colors, and if extra styling is required, forms have static ids and you can add custom CSS classes.

= Can I set custom validation for the fields? =

Sure. Currently you can set text field to accept Only numbers/Only characters/Only numbers and characters. Also you can use Email and URL field, which have corresponding validations.

= Can I use different values for Dropdowns/Radio/Checkboxes? =

Yes, it is possible.

= What info about the entries I receive? =

Besides the entry selected values, you also receive submission time, referrer url and IP address.

== Screenshots ==

1. Main form builder interface
2. Drag n drop fields to form
3. Form with customized colors
4. Validation on client side
5. Easy entries management

== Changelog ==

= 0.5.1 =
* Fixed bug: invalid date in Safari and Firefox Nightly
* Entries admin page Layout fixed on Safari and Firefox
* Forms and entries ordering fixed

= 0.5 =
* New feature: now you can send submission contents in email
* Fixed bug with field slug displaying instead of field name for new fields
* Fixed bug with checkboxes not adding custom css class
* Entries page minor selection bugs fixed
* Admin styles changed to not block wordpress default notifications
* Fixed php warning when all options deleted in dropdown/radio/checkboxes

= 0.4 =
* reCAPTCHA added
* Entry display fixed for radio submitted with no value
* Entry display fixed for radio submitted and renamed later
* Checkbox and radio edit multiple fields bug fixed
* Dropdown now displays both key and label if it has key in entries list
* Added possibility to display entries after options has been changed or removed
* Fixed Hide empty fields for checkboxes
* Multiple entries page layout fixes and improvements
* Admin layouts fixed on smaller screens (down to 1024 px width)
* Entries link on form list fixed
* Submit animation fixed
* Warnings in some dashboard pages fixed
* Multiple small bug fixes
* Global settings added (for reCAPTCHA)

= 0.3 =
* Fixed bug with special characters not saved properly on fields
* Fixed css for better compatibility with themes (see attached list)
* Editor optimized for speed
* Editor styles improved

= 0.2b =
* Added entries view section
* Multiple bug fixes

= 0.1 =
* Initial version