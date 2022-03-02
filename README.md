# WPVIP Glossary

* Contributors: lschuyler, automattic
* Tags: glossary
* Version: 0.3.0
* Tested up to: 5.9.1
* Requires at least: 5.8
* Requres PHP: 7.1
* License: GPLv2 or later

Adds a glossary custom post type with shortcode.

## Description

Creates a custom post type to store glossary posts.

Creates a glossary shortcode to display the glossary items, with several display options (excerpts, thumbnails, A to Z organization).

## Installation

1. Install the plugin.
2. Add glossary items through the dashboard.
3. If you want to display excerpts, use the excerpt field in the editor sidebar to add a brief excerpt for the glossary
   item.
4. Add the `[glossary]` shortcode to a page to display the glossary items. The following attributes individual attributes can be used in the shortcocde:
* excerpts - Yes or No. Displays post excerpts. Optional. Defaults to 'no'.
* thumbnails - Yes or No. Displays thumbnail of post\'s featured image. Defaults to 'no'.
* alphabet_headings - Yes or No. Organized alphabetically by title with an alphabet letter heading. Optional. Defaults to 'no'.
* link - Yes or No. Add a hyperlink to the term to allow a click through to a full post view.

Example: `[glossary excerpts="no" thumbnails="no" alphabet_headings="yes" link="yes"]`

## FAQ

* If you want to completely disable/prevent individual post views, beyond setting the `link="no"`, you can filter the custom post type from your theme's functions.php file (or another file of your choice):

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
