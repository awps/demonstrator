=== Plugin Name ===
Contributors: _smartik_
Donate link: https://paypal.me/zerowp
Tags: theme, switcher, demo, connect, themeforest, creativemarket, codecanyon, mojothemes
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.2.1
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

More than a theme switcher!

== Description ==

More than a theme switcher!

Demonstrator allows to build a list of demo sites with or without demo styles. It was initially intended to be used only by web developers, but now it's possible to be used by everyone. Also you have the freedom to create an unlimited number on switchers, just because one bascket is not for all egs :).

## Features: 

* The possibility to manage an unlimited number of themes directly from an admin page.
* The possibility to add an unlimited number of styles to each theme.
* Setup your own logo.
* Setup your own URL that will wrap the logo in a link.
* Display the themes and styles in a grid from 1 to 4 columns.
* Setup the usernames for Envato and CreativeMarket referal program.
* Short "purchase URL". The real purchase URL will be hidden in a local URL, so nobody will be able to access the purchase page without your referal ID.
* The frame is not closed, but instead is collapsed on top. This actually is a bonus because the visitors never lose the purchase URL and the acces to other themes/styles.
* Use the homepage and ignore the full site content, or setup a custom endpoint name.

## How it works:

You must create a switcher that will keep all demo sites. You can create more than one, but at least one is required. By default on first plugin installation a switcher is created, so you can focus on more important things. Go to "Demonstrator" page from admin side to add or edit a switcher.

After the switcher is created, you can access it from a subpage under "Demonstrator" menu item.

There complete all fields and save.

Now you can access the switcher using "http://yoursiteadress.com/{switcher_id}". Where {switcher_id} is the ID used when the switcher was created. 

## Live example:

Here are some links that will show the plugin in action. The switcher ID in this case is `tf`.

* **Switcher base url:**    http://themes.market/tf/
* **Single site URL:**      http://themes.market/tf/gustoswp
* **Shorten purchase URL:** http://themes.market/?buy=tf:gustoswp

*More demos coming soon.*


## Completed TODOs:

* Make themes and styles sortable( in admin panel ).
* Display the price and description.
* Make a theme unlisted. This is usefull when you have to show the demo, but you don;t have the purchase URL yet. Example: You submitted for review and the theme should be visible only to reviewer, but not to other users. This will be possible only by using a direct link.
* Do not allow to collapse the top bar and hide the dropdown(themes list) if no theme has been selected.
* Hide "Purchase" button if a purchase URL is not available.
* Private themes. Just like unlisted, but instead do not allow access to demo using direct link for user without administrative rights.
* Do not hide the dropdown if no theme is selected.
* Lazy-load images. We must wait for demo site from iframe to complete loading, not for images from top window.
* Include a link to WP admin in top bar.
* Pretty links.
* Multiple switchers.


## TODO:

* Import, Export and Backup settings(with images/files). This requires a lot of work.
* Add an option to specify the frame size(width&height) from admin panel. Then this will be available on frontend as dropdown.
* Add an upload field to each style. This may be handy if you want to include the demo data.
* Implement custom colors for each category. Currently this is hardcoded for `WordPress` and `HTML` categories only.
* Make possible to change the text of all elements directly from admin panel.
* Add the possibility to ignore the 'purchase URL' in favor to a 'download URL'. Just in case if you want to provide free files.
* Display an icon that will allow to preview the the theme outside the iframe. An alternative to `collapse`.
* Lazy-load items. Right now are loaded all themes and styles regardless if they are needed or not. It's needed to load to load each theme and style just when needed(probably using ajax). Ideas are welcome.
* Add the possibility to customize the design of the switcher itself.
* Add the possibility to activate google analytics.
* Anonymous URL. Allow to open a link in switcher frame even if it not registered under a theme style. This may be usefull for other use cases.
* Add an iframe loader, so it does not show up an incomplete site.
* Style separators. Sometimes you may have too many demos that may need to be separated somehow. This one should allow to add sections of text that will serve as an intro to a styles set.

## Support & Donate: 

Hi.<br>
I invested a considerable amount of time in this product. And I still have a lot of work to do on it. See the above 'TODO' list.
Consider making a donation if you find this product useful. Don't ignore this message. Your donation will make a difference.
I would like to improve it as much as I can, but your support is needed.

Donate link: https://paypal.me/zerowp

## Issues tracker:
Please report bugs on: https://github.com/ZeroWP/demonstrator/issues

== Changelog ==

= 1.2.1 =
* Faster escape the iframe if it's within another iframe. Previously it has to wait for jquery and CSS to load first.
* Hide dropdown on tab click. Previously it has been possible to close it only on top bar click.
* Do not show the arrow used to collapse the bar when no theme has been selected.
* Load bar logo before any other images.
* Shortcut link in admin panel to WordPress.ORG reviews page. Just a reminder. ;)

= 1.2 =
* Major code refactoring.

= 1.1 = 
* Do not allow to collapse the top bar and hide the dropdown(themes list) if no theme has been selected.
* Hide "Purchase" button if a purchase URL is not available.
* Private themes. Just like unlisted, but instead do not allow access to demo using direct link for user without administrative rights.
* Do not hide the dropdown if no theme is selected.
* Lazy-load images. We must wait for demo site from iframe to complete loading, not for images from top window.
* Include a link to WP admin in top bar.
* Pretty links.
* Multiple switchers.

= 1.0 =
* Initial release.