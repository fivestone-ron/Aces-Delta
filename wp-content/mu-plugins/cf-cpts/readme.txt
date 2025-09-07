=== CF Custom Post Types ===
Author URI: https://crowdfavorite.com
Tags: custom post type, custom taxonomy
Requires at least: not tested
Tested up to: 5.5.1
Stable tag: 3
Requires PHP: 7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Registers programmatically the custom post types, custom post meta boxes and custom taxonomies related.

== Installation ==
* This is recommended to be used as a must-use plugin, add this in the mu-plugins folder.

== Frequently Asked Questions ==

= How to create a new CPT model class =
- Create a new class in the `/cf-cpts/classes/models/cpts/` directory; the file name convention is `class-{customcptslug}.php`, for example: `/cf-cpts/classes/models/cpts/class-awesomeproduct.php`.
- The name of your class should be `CustomCPTSlug`, your new class will extend the `ModelBase` class, for example `AwesomeProduct`.
- Define the model slug, using the `MODEL` constant, for example `awesome_product`.

= Register the new CPT =
- That should be done by using the WP native `register_post_type` inside the `register` method of your CPT model class.

= Register the new CPT postmeta and meta boxes =
TBD

= How to create a new CPT model class =
- Create a new class in the `/cf-cpts/classes/models/taxonomies/` directory; the file name convention is `class-{customtaxonomyslug}.php`, for example: `/cf-cpts/models/taxonomies/class-productcategory.php`.
- The name of your class should be `CustomTaxonomySlug`, your new class will extend the `BaseModel` class, for example `ProductCategory`.
- Define the model slug, using the `MODEL` constant, for example `product_category`.

= Register the new custom taxonomy =
- That should be done by using the WP native `register_taxonomy` inside the `register` method of your taxonomy model class.

= How to assign a custom taxonomy to a CPT =
- Go to the `Settings -> CF CPTs Settings` page and enable any taxonomy for whatever CPTs you want.
- All custom taxonomies are disabled by default and need to be manually enabled from the `CF CPTs Settings` before they are registered.


== Changelog ==
= 3.0 =
* Done refactoring to align code with PSR12 standard and latest best practices and to simplify the registration of models.

= 2.1 =
* Taxonomies can now be associated with regular posts and pages.
* Added examples of custom meta registration for the 'post' and 'page' post types.

= 2.0 =
* Refactored the plugin and split the CPTs models from the taxonomies models.
* A taxonomy can now be assigned to multiple CPTs from the CF CPTs Settings page.
* Taxonomies are disabled by default until assigned to the desired CPTs.
* Aligned the code to the current standards.

= 1.0 =
* Initial version


== Upgrade Notice ==
* v2.0 is not backwards compatible with 1.0.


== License ==
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.


== Version history ==
1.0 - Initial version.
