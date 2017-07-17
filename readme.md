## Description

More than a theme switcher!

This is a theme switcher designed for **theme developers**. When installed, the current site will become blank, the current theme will be ignored and the site will become a placeholder for an iframe where will be loaded different site addresses. 
It will create a bar on top that will make possible to switch between demos.

**If you are not a theme developer, you should not install it. It is useless for regular users.**

**Note:** *Go to permalinks and setup an endpoint name for this switcher. That way you can can get the website back to normal and use the swither only when the endpoint is accessed.*

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


## Completed TODOs:
- [x] Make themes and styles sortable( in admin panel ).
- [x] Display the price and description.
- [x] Make a theme unlisted. This is usefull when you have to show the demo, but you don;t have the purchase URL yet. Example: You submitted for review and the theme should be visible only to reviewer, but not to other users. This will be possible only by using a direct link.


## TODO:

- [ ] Import, Export and Backup settings(with images/files). This requires a lot of work.
- [ ] Add an upload field to each style. This may be handy if you want to include the demo data.
- [ ] Implement custom colors for each category. Currently this is hardcoded for `WordPress` and `HTML` categories only.
- [ ] Make possible to change the text of all elements directly from admin panel.
- [ ] Add the possibility to ignore the 'purchase URL' and favor to a 'download URL'. Just in case if you want to provide free files.
- [ ] Display an icon that will allow to preview the the theme outside the iframe. An alternative to `collapse`.
- [ ] Lazy-load items. Right now all themes and styles regardless if they are needed or not. It's needed to load to load each theme and style just when needed(probably using ajax). Ideas are welcome.
- [ ] Add the possibility to customize the design of the switcher itself.
- [ ] Do not hide the dropdown if no theme is selected.
- [ ] Add the possibility to activate google analytics.
- [ ] Multiple switchers. Right now, it's not possible to create multiple switchers, but it will be a great idea to make this possible. For example: Someone may need this, in case they sell themes on multiple marketplaces.
- [ ] Anonymous URL. Allow to open a link in switcher frame even if it not registered under a theme style. This may be usefull for other use cases.
- [ ] Private themes. Just like unlisted, but instead do not allow access to demo using direct link for user without administrative rights.

## Support & Donate: 

Hi.<br>
I invested a considerable amount of time in this product. And I still have a lot of work to do on it. See the above 'TODO' list.
Consider making a donation if you find this product useful. Don't ignore this message. Your donation will make a difference.
I would like to improve it as much as I can, but your support is needed.

Donate link: https://paypal.me/zerowp

## Issues tracker:
Please report bugs on: https://github.com/ZeroWP/demonstrator/issues