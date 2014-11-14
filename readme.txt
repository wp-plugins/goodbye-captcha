=== GoodBye Captcha ===
Contributors: MihChe
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XVC3TSGEJQP2U
Tags:  antispam, no captcha, captcha, spam, forms, comments, anti-spam, no-captcha, login, register, contact form, security, zero spam, no spam, comment, form, spams, spambot, spambots
Requires at least: 3.2
Tested up to: 4.0
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GoodBye Captcha is the best solution for protecting your site without annoying captcha images.

== Description ==
Have you ever been frustrated with so many forms that force you to read annoying captcha images? Captcha was design to reduce the number of spam contacts but the side effect is that it also reduces legitimate users number. Many users find the captcha a nuisance. The codes are usually hard to read, require correct spelling and if you make a mistake, you have to try and try again. Captcha error forces the user to re-enter all the information on the form every time a failed attempt happens. Captcha was shown to produce 160% more failed conversion (people who landed on the contact page, started filling out the form but abandoned it) than when no captcha was used.

GoodBye Captcha is based on algorithms that identify spam robots without having any annoying and hard to read images. No additional visible input field will be added into your form.

This plugin **does not use php Session**, so no conflict between GoodBye Captcha and any other plugins can occur. The plugin **does not require to install any additional php extensions**, you can run it 100% with your site configuration. GoodBye Captcha is compatible with any other WordPress plugin. If you prefer, you can use it with other captcha plugins for double spam protection. This plugin **does not perform requests to external APIs**.

= Features avaliable in GoodBye Captcha Free version =
*   Comments form integration
*   Register form integration
*   Login form integration
*   Forgot password form integration

= Features avaliable in GoodBye Captcha Pro version =
*   JetPack contact form integration  - JetPack contact form captcha replacement
*   JetPack comments form integration - JetPack comments form captcha replacement
*   BuddyPress registration form integration - BuddyPress registration form captcha replacement
*   BuddyPress login form integration - BuddyPress login form captcha replacement
*   Contact Form 7 integration - Contact Form 7 captcha replacement
*   Gravity Forms integration - Gravity Forms captcha replacement
*   Ninja Forms integration - Ninja Forms captcha replacement
*   Formidable Forms(Pro and Free) integration - Formidable Forms captcha replacement
*   Fast Secure Contact Form integration - Fast Secure Contact Form captcha replacement


*	Find more information about [GoodBye Captcha Pro](http://www.goodbyecaptcha.com)

= Key Features = 
*   No more captcha images
*   Eliminates automated form submissions from robots
*   Eliminates automated comment spam from robots
*   Eliminates automated robots sign-up trough registration form
*   Eliminates automated robots login attempts
*   No requests to external APIs
*   Compatible with WordPress Multisite
*   Compatible with cache plugins(WP Super Cache, W3 Total Cache and others)
*   Invisible for end users (works in background)
*   It does not affect pages loading time
*   It does not use PHP Session
*   This version is free for both Commercial and Personal use

= Technical support =
If you notice any problems by using this plugin, please notify us and we will investigate and fix the issues. Ideally your request should contain: URL of the website (if your site is public), Php version, WordPress version and all the steps in order to replicate the issue (if you are able to reproduce it somehow)

= Donate =
If you find this plugin useful, please consider making a small [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XVC3TSGEJQP2U). Thank you

== Installation ==
= Option 1 =
1. Download the zip file from WordPress plugin directory,
2. Unzip and upload all the files to the /wp-content/plugins/goodbyecaptcha/ folder from your server,
3. Activate the plugin from WordPress Dashboard

= Option 2 =
1. Download the zip file from WordPress plugin directory,
2. Login into the administration panel,
3. Go to Plugins --> Add New --> Upload,
4. Click Choose File (Browse) and select the downloaded zip file,
5. Activate GoodBye Captcha plugin

= Option 3 =
1. Login into your WordPress site,
2. Choose Plugins --> Add New,
3. Search for 'GoodBye Captcha',
4. In the results page, click Install Now. (depending on your server, you might need to provide credentials for download),
5. Activate GoodBye Captcha

After installation, a GoodBye Captcha menu item will appear in the Settings section. Click on this in order to view plugin's administration page.

== Frequently Asked Questions ==

= Is GoodBye Captcha safe? =
Yes. The algorithm behind the plugin was fully tested and there is no way a spider or robot can spam your sites.

= What forms can be secured by using Goodbye Captcha WordPress plugin? =
All standard WordPress forms(Login, Register, Comment and Forgot Password) can be secured by using GoodBye Captcha Free plugin.

= What other popular plugins which are generating forms can be integrated with this plugin =
Plugins like JetPack, BuddyPress, Contact Form 7, Formidable Forms, Gravity Forms, Ninja Forms and Fast Secure Contact Form can be integrated with GoodBye Captcha Pro plugin. You can find more information about this on [GoodBye Captcha Website](http://www.goodbyecaptcha.com).

= Are there any php extensions that need to be activated so this plugin could work? =
No. The plugin runs 100% without activating any additional php extensions.

= Is there any possibility to have a conflict between this plugin and all the other installed plugins? =
No. The plugin is written using WordPress coding stiles recommendations when it comes to naming classes, files and so on. This plugin does not use php Session, so no conflict can occur when it comes to saving objects.

= Why captcha is not user friendly? =
Studies shown that visual CAPTCHAs take around 5-10 seconds to complete and audio CAPTCHAs take much longer (around 20-30 seconds) to hear and solve.

== Screenshots ==
1. GoodBye Captcha settings are available trough Settings menu.
2. In GoodBye Captcha settings page, activate the plugin for the preferred forms.
3. GoodBye Captcha Pro - Default Page. Activate the plugin for the standard WordPress forms and activate your license in order to get the latest GoodBye Captcha PRO updates.
4. GoodBye Captcha Pro - JetPack Page. Activate the plugin for JetPack Comments Form feature and/or for Contact Form feature.
5. GoodBye Captcha Pro - BuddyPress Page. Activate the plugin for Buddy Press registration form or for Buddy Press Login form.
6. GoodBye Captcha Pro - Popular Forms Page. Activate the plugin for Contact Form 7 and/or Formidable Forms.

== Upgrade Notice ==
BuddyPress integration issue fixed
Strict Standards warning message fixed
 
== Changelog ==

= 1.0.9: November 15, 2014 =
*   Fixed Bug - token was not generated for comments form if the user was logged in
*   Added timestamp for generated token


= 1.0.8: November 10, 2014 =
*   Improved token generator speed 
*   Added pkcs7 padding

= 1.0.7: October 28, 2014 =
*   Increased PBKDF2 number of iterations for key derivation 
*   Added timing attack prevention
 
= 1.0.6: October 20, 2014 =
*   code review

= 1.0.5: October 16, 2014 =
*   implemented PBKDF2 key derivation for generated token

= 1.0.4: October 6, 2014 =
*   Added minimum time for any form submission

= 1.0.3: September 28, 2014 =
*   JavaScript browser detection improvements
*   Updated readme.txt notes and FAQs
*	Improve multisite module integration 
*   Secret key size less than 56 bytes in windows - bug fixed.

= 1.0.2: September 22, 2014 =
*   Improved/optimized random bytes generator code.
*   Fixed PHP 5.2 warnings

= 1.0.1: September 15, 2014 =
*   Fixed the integration issue with BuddyPress! (Notice: bp_setup_current_user was called incorrectly)
*   Fixed the Strict Standards: Static function should not be abstract warning message

= 1.0.0: September 14, 2014 =
*   First official release!
