=== GoodBye Captcha ===
Contributors: MihChe
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XVC3TSGEJQP2U
Tags:  anti-spam, antispam, captcha, spam, website field, allowed tags, no captcha, forms, comments, no-captcha, login, register, contact form, security, no spam, comment, form, spams, spambot, spambots
Requires at least: 3.2
Tested up to: 4.2.2
Stable tag: 1.1.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An extremely powerful anti-spam plugin that blocks Spam-bots without annoying captcha images.

== Description ==
**Goodbye Captcha anti-spam and security plugin is based on algorithms that identify spam bots without any annoying and hard to read captcha images.**

Goodbye Captcha completely eliminates spam-bot signups, spam comments, even brute force attacks, the second you install it on your Wordpress website.  It is completely invisible to the end-user - no need to ever fill out a Captcha or other "human-detection" field ever again - and it just works!

Unlike other anti-spam plugins, which detect spam comments and signups after the fact and move them to your spam folder, which you then have to delete - using up not only your website's resources, but your time as well, Goodbye Captcha prevents the bots from leaving spam in the first place. The result is that your site is not only spam free, it's faster and more secure.

In addition, Goodbye Captcha is completely self-contained and does not need to connect to any outside service.  Your logins remain yours, 100%.

Goodbye Captcha eliminates spam-bots on comments, signup pages as well as login and password reset pages. At the click of a button, you can decide which forms to protect.

**It also currently works with the following plugins:**

* **MailChimp for WordPress** (https://wordpress.org/plugins/mailchimp-for-wp)
	GoodBye Captcha offers protection for all forms the user will create with MailChimp

* **Ultimate Member** (https://wordpress.org/plugins/ultimate-member)             
	GoodBye Captcha offers protection for Login, Registration and Reset Password forms

* **WP User Control** (https://wordpress.org/plugins/wp-user-control)
	GoodBye Captcha offers protection for Login, Registration and Lost Password forms

* **Login With Ajax** (https://wordpress.org/plugins/login-with-ajax)
    GoodBye Captcha offers protection for Login, Registration and Lost Password forms

* **JetPack by WordPress** (https://wordpress.org/plugins/jetpack)
	GoodBye Captcha offers protection for JetPack Contact Form

= Summary of Goodbye Captcha features =
* Login form integration
* Register form integration
* Comments form integration
* Forgot password form integration
* Logging with the ability to enable/disable it
* Limit the number of allowed attempts
* Automatically Block IP Address if number of allowed attempts is reached
* Automatically purge logs older than a certain number of days
* Manually whitelist trusted IP Address
* Manually block/unblock IP Addresses (IPV4 and IPV6)
* Properly detects client IP Address when using CloudFlare, Incapsula, Cloudfront, RackSpace
* Provides statistics, reports, maps and charts with all blocked spam attempts
* No requests to external APIs
* Can be switched to "Test Mode" - for testing
* Compatible with WordPress Multisite
* Compatible with cache plugins (WP Super Cache, W3 Total Cache, ZenCache and others)
* Invisible for end users (works in the background)
* Does not affect page loading times



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
All standard WordPress forms(Login, Register, Comment and Forgot Password) can be secured by using GoodBye Captcha plugin.

= Are there any php extensions that need to be activated so this plugin could work? =
No. The plugin runs 100% without activating any additional php extensions.

= Is there any possibility to have a conflict between this plugin and all the other installed plugins? =
No. The plugin is written using WordPress coding stiles recommendations when it comes to naming classes, files and so on. This plugin does not use php Session, so no conflict can occur when it comes to saving objects.

= Does GoodBye Captcha help me to block ip of the spammers? =
Yes. Starting with GoodBye Captcha version 1.1.0, this feature is available.

= Does GoodBye Captcha recognize IPV6 addresses and is it possible to block them? =
Yes. Starting with GoodBye Captcha version 1.1.0, IPV6 is recognized and the administrator can block it with a single click.

= Why captcha is not user friendly? =
Studies shown that visual CAPTCHAs take around 5-10 seconds to complete and audio CAPTCHAs take much longer (around 20-30 seconds) to hear and solve.


== Screenshots ==
1. GoodBye Captcha - available through Top Level menu.
2. GoodBye Captcha - All common settings.
3. GoodBye Captcha - WordPress Forms. Activate the plugin for the standard WordPress forms.
4. Total Attempts per Day Chart, Monthly Spam Attempts Percentage Comparison, Latest Blocked Attempts Table.
5. Geographical Locations of The Attempts, Top of Attempts per Country, Statistics of Attempts per Modules.
6. Total Attempts per Module/Day Charts, Modules Latest Attempts.

== Upgrade Notice ==


= 1.1.16: May 23, 2015 =

**Fixed**
- Chrome caching Ajax requests
- Incompatibility with CloudFlare - Rocket Loader

**Additions**
- Integration with JetPack Contact Form


= 1.1.15: May 04, 2015 =

**Fixed**
- Issue with Ultimate Member - user can't edit profile
- Login issue when ZenCache is activated


= 1.1.14: April 15, 2015 =

**Additions**
- Integration with Login With Ajax plugin
- Ability to switch the plugin to "Test Mode"
- Moved GoodBye Captcha to Top Level menu

**Fixed**
- Issue with custom login

= 1.1.12: March 28, 2015 =

**Additions**
- Integration with Ultimate Member plugin
- Properly detects client IP Address when using RackSpace

**Fixed**
- Issue when settings are reset to default values

= 1.1.11: March 15, 2015 =

**Additions**
- Ability to whitelist your current IP address
- Properly detects client IP Address when using CloudFlare, Incapsula or Cloudfront

**Fixed**
- Issue with Sucuri Firewall


= 1.1.10: February 25, 2015 =

**Additions**
- Ability to set the maximum form submissions per minute
- Ability to Automatically Block IP Address

**Fixed**
- Issue with non popular caching plugins


= 1.1.9: February 14, 2015 =
*   Fixed the issue causing the logs to disappear

= 1.1.8: February 07, 2015 =
*   Logs are automatically purged daily
*   Ability to turn off logging
*   Settings for max/min form submission time

= 1.1.7: January 18, 2015 =
*   Fixed the issue when open_basedir is enabled on shared hosts
*   Fixed the issue when just register option is enabled for wordpress
*   Integration with UjiCountdown

= 1.1.6: January 10, 2015 =
*   Integration with MailChimp for WordPress plugin - PRO and FREE
*   Fixed the issue when FORCE_SSL_ADMIN is set to true

= 1.1.5: December 28, 2014 =
*   Improved security token generator

= 1.1.4: December 20, 2014 =
*   Fixed the issue when multiple versions of jQuery are present

= 1.1.2: December 6, 2014 =
*   Fixed several minor bugs
*   New Feature - Integration with MailChimp for WordPress plugin

= 1.1.1: November 29, 2014 =
*   Fixed Bug - Token generated twice for IE11 browser
*   Improved the IP blocking functionality
*   New Feature - Remove comments form Website URL field
*   New Feature - Remove comments form Allowed Tags field and the "Your email address will not be published" text

= 1.1.0: November 17, 2014 =
*   Block Ip address feature (IPV4 and IPV6)
*   Reports and statistics with blocked spam attempts
*   Total spam attempts per days and modules charts
*   Monthly spam attempts percentage comparison
*   Latest spam attempts table per day and module
*   Geographic map chart of the blocked spam attempts
*   Top of attempts organized by countries
*   Spam attempts statistics per modules

= 1.0.9: November 15, 2014 =
*   Fixed Bug - token was not generated for comments form if the user was logged in
*   Added timestamp for generated token

= 1.0.8: November 10, 2014 =
*   Improved token generator speed 
*   Added pkcs7 padding

= 1.0.7: October 28, 2014 =
*   Increased PBKDF2 number of iterations for key derivation 
*   Added timing attack prevention
 
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
