=== SharpLogin ===
Contributors: sharplugins
Tags: LoginURL, LoginScreen, Login, change login url, hide login url, bruteforceattack
Donate link: https://www.patreon.com/husnainahmad
Requires at least: 5.0
Tested up to: 5.8.1
Requires PHP: 7.3
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With SharpLogin plugin you can get to play with Login screen.

== Description ==
SharpLogin gives you facility to change login url, change background picture of the login screen, also you can put your logo in login screen. In the new release, you can make sure that you have limited login attemps and if the limit exceed, you can give a specific time to lockout the user from system.

= Compatibility =

Requires WordPress 5.0 or higher. All login related things such as the registration form, lost password form, login widget and expired sessions just keep working.

It’s also compatible with any plugin that hooks in the login form, including:

* BuddyPress,
* bbPress,
* Limit Login Attempts,
* and User Switching.
Obviously it doesn’t work with plugins or themes that *hardcoded* wp-login.php.

Works with multisite, with subdomains and subfolders. Activating it for a network allows you to set a networkwide default. Individual sites can still rename their login page to something else.


== Installation ==
1. Go to Plugins > Add New
2. Search for SharpLogin
3. Look for the plugin, download and activate it using wordpress plugins section
4. Navigate to *SharpLogin* Menu and update settings

== Frequently Asked Questions ==
= What should I do if I forget my custom login URL? =
In that case, you have to go to database and look for *sharplogin_page* in *wp_options* and delete it.

= How can I change the background picture of the login screen? =
In the settings, you can change the background picture of the login screen.

= How can I change the logo of the login screen? =
In the settings, you can change the logo of the login screen.


== Screenshots ==

1. Activate the plugin and click on *Settings*
2. Settings page
3. Advanced Settings page for Login attempts
4. Login attempts message
5. Lockout time and message


== Changelog ==

= 0.1 =
* Initial release.
* It has following features
    1. Hide Login URL
    2. Customize login URL
    3. Change background Picture of the login screen
    4. Update Login screen logo


= 1.1.0 =
* Updated backend for better understanding of the customer.
* Added login attempts menu which have following options:
    1. Enable or disable option
    2. Max Attempts for login
    3. Max lockout time
    4. Time unit which is Minutes, Hours and Days

= 1.2.0 =
* updated the general settings section
* Added media library to upload login logo
