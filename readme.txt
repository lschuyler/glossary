=== Glossary ===
Contributors: lschuyler, automattic
Tags: glossary
Version: 0.3
Tested up to: 5.8.2
Requires at least: 5.2
Requires PHP: 5.6
License: GPLv2 or later

Used to display the posts of the glossary custom post type.

== Description ==
Creates a custom post type to store glossary posts.

Creates a glossary shortcode to display the glossary items, with several display options (excerpts, thumbnails, A to Z organization).

== Installation ==
1. Install the plugin.
2. Add glossary items through the dashboard.
3. If you want to display excerpts, use the excerpt field in the editor sidebar to add a brief excerpt for the glossary
   item.
4. Add the `[glossary]` shortcode to a page to display the glossary items.
5. There are two suggested plugin display modes available for display. Adding `mode="plain"` displays a list of terms and their definitions, with no link to a single post view, and no thumbnails. Adding `mode="glossary"` displays a list organized with alphabetical headers, thumbnails if available, with the term title linked to a single post view. Both modes show all glossary items on the same page (no pagination).
6. If neither mode is specified, the following attributes individual attributes can be used in the shortcocde:

* excerpts - Yes or No. Displays post excerpts. Optional. Defaults to 'no'.
* thumbnails - Yes or No. Displays thumbnail of post\'s featured image. Defaults to 'no'.
* alphabet_headings - Yes or No. Organized alphabetically by title with an alphabet letter heading. Optional. Defaults to 'no'.

Example: [glossary excerpts="no" thumbnails="no" alphabet_headings="yes"]

== FAQ ==

* If you want to completely disable individual post views, you can filter the custom post type from your theme's functions.php file (or another file of your choice):

```
function make_private($args, $post_type)
{
   if ( $post_type == 'glossary' ) {
      $args['publicly_queryable'] = false;
   }

    return $args;
}

add_filter('register_post_type_args', 'make_private', 10, 2);
```