=== Neshan Maps ===
Contributors: neshanmaps,mashhadcode
Tags: map, maps, neshan maps, persian map, google maps, static maps, نقشه, نقشه گوگل, نقشه ایران, نقشه نشان
Requires at least: 4.0
Tested up to: 4.9.7
Requires PHP: 5.2.4
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Amazing map tools for Wordpress. Create and use beautiful maps based on Neshan™ Maps Platform an alternative to Google Maps Platform.

== Description ==
**Neshan™ Maps Platform** provides several map-based services to developers. This plugin is a tool to make it easier for site owners and developers to create stylized maps.

You need an *API Key* which you can get one for free from [https://developer.neshan.org](https://developer.neshan.org).

Please keep in mind that this plugin should load some external resources (Neshan Web SDK files) from static.neshan.org. These files are:
* Main Neshan Web SDK javascript file (v4.6.5.js)
* Main Neshan Web SDK stylesheet file (v4.6.5.css)

Also there is a javascript file which is load externally for old environments like Internet Explorer and Android 4.x browsers. Because this file generated based on user\'s browser compatibilities, there is no chance to prevent offloading it: https://cdn.polyfill.io/v2/polyfill.min.js

== Useful Links ==
[How to use Neshan Maps Wordpress Plugin](https://neshan.blog/wordpress-neshan-maps-plugin-msdvteihyzkw)

== Persian Description ==
افزونه تولید و مدیریت نقشه با استفاده از زیرساخت توسعه‌ی نقشه‌ی نشان این امکان را به مدیران سایت‌ها و توسعه‌دهندگان می‌دهد تا بدون هیچ دانش فنی اقدام به ایجاد و استفاده از نقشه‌های نشان نمایند. نقشه نشان جایگزین مناسبی برای سرویس نقشه گوگل در ایران می‌باشد.
از طریق این افزونه پس از ساخت نقشه می‌توانید از شورتکدهای تولید شده در هر صفحه‌ای که مایلید استفاده نمایید.

از نسخه 1.1.0 به بعد امکان ایجاد نقشه‌های داینامیک (از طریق کدنویسی) با استفاده از شورت‌کد زیر فراهم شده است:
`[neshan-map-dynamic]`

برای کسب اطلاعات بیشتر در مورد چگونگی استفاده از نقشه‌ی نشان می‌توانید به صفحه **راهنمای استفاده از نقشه‌ی نشان** در پنل مدیریت وردپرس خود مراجعه نمایید.

== Screenshots ==
1. My maps list
2. Add new map (no API Key)
3. Add new Map (with API Key)

== Changelog ==
= 1.1.4 =
* Now developers can set an ID for dynamic maps, then they can access map instance via neshan_ID (global variable)
= 1.1.3 =
* Bug fixed (map always injected before page contents)
= 1.1.2 =
* Minimum Wordpress version changed to 4.0
* Help page updated
= 1.1.1 =
* My Maps page introduction and usage help page added
= 1.1.0 =
* Dynamic map creation added (via neshan_map_dynamic shortcode)
* Bug fixed in rendering maps
= 1.0.2 =
* Added a default marker to indicate center of the map
* Bug fixed
= 1.0.1 =
* Plugin description and tags are updated
* Persian description section added


