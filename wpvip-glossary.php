<?php
/**
 * WPVIP Glossary
 *
 * @package     WPVIPGlossary
 * @author      Lisa Schuyler <lisa@automattic.com>
 * @copyright   2022 Lisa Schuyler
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:     WPVIP Glossary
 * Plugin URI:
 * Description:     Adds a glossary custom post type with shortcode.
 * Version:         0.3.0
 * Author:          Lisa Schuyler
 * Author URI:      https://lschuyler.dev
 * Text Domain:     wpvip-glossary
 * License:         GPL v2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires PHP:    7.1
 * Requires WP:     5.2
 */

/**
 * WPVIP_Glossary class.
 *
 * @since 0.1.0
 *
 * @package WPVIPGlossary
 * @author  Lisa Schuyler *
 */
class WPVIP_Glossary {

	/**
	 * CSS_VERSION constant, to be updated any time the CSS is updated to break the cache on the CSS.
	 *
	 * @since 0.1.0
	 * @var string $CSS_VERSION Stores a constant to allow cache breaking of the CSS files.
	 */
	private const CSS_VERSION = '20211222';

	/**
	 * Instantiate the WPVIP_Glossary class.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_glossary_post_type' ) );
		add_shortcode( 'glossary', array( $this, 'create_glossary_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
	}

	/**
	 * Register and enqueue a custom stylesheet for this plugin.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_css() {
		wp_enqueue_style( 'custom_glossary_css', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), self::CSS_VERSION );
	}

	/**
	 * Create and register a new custom post type called 'glossary'.
	 *
	 * @since 0.1.0
	 * @var array $labels Stores the custom post type labels for internationalization.
	 * @var array $args Stores the arguments for the custom post type.
	 */
	public function create_glossary_post_type() {

		$labels = array(
			'name'               => _x( 'Glossary', 'post type general name', 'wpvip-glossary' ),
			'singular_name'      => _x( 'Glossary Item', 'post type singular name', 'wpvip-glossary' ),
			'add_new'            => _x( 'Add New', 'glossary', 'wpvip-glossary' ),
			'add_new_item'       => __( 'Add New Glossary Item', 'wpvip-glossary' ),
			'edit_item'          => __( 'Edit Glossary Item', 'wpvip-glossary' ),
			'new_item'           => __( 'New Glossary Item', 'wpvip-glossary' ),
			'view_item'          => __( 'View Glossary Item', 'wpvip-glossary' ),
			'search_items'       => __( 'Search Glossary', 'wpvip-glossary' ),
			'not_found'          => __( 'No Glossary Item(s) found', 'wpvip-glossary' ),
			'not_found_in_trash' => __( 'No Glossary Items found in Trash', 'wpvip-glossary' ),
			'parent_item_colon'  => __( 'Parent Glossary Item:', 'wpvip-glossary' ),
			'menu_name'          => __( 'Glossary', 'wpvip-glossary' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => ( 'Glossary' ),
			'menu_icon'          => 'dashicons-book-alt',
			'public'             => true,
			'show_ui'            => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'hierarchical'       => false,
			'show_in_rest'       => true,
			'supports'           => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'featured_image',
				'revisions'
			),
		);

		register_post_type( 'glossary', $args );

	}

	/**
	 * Create the [glossary] shortcode.
	 *
	 * Displays the glossary custom post type posts.
	 *
	 * @param array $user_atts Shortcode attributes. Default empty.
	 *
	 * @var array $default_atts Stores the default shortcode parameters, available to be overridden by specification in shortcode.
	 * @var array $atts User defined attributes from the shortcode tag.
	 * @var array $args Arguments for the WordPress query.
	 * @var string $query WordPress Query.
	 */

	public function create_glossary_shortcode( $user_atts = [] ) {

		// change attribute keys to lowercase
		$user_atts = array_change_key_case( (array) $user_atts, CASE_LOWER );

		// override default attributes with user specified attributes
		$default_atts = array(
			'excerpts'          => 'no',
			'thumbnails'        => 'no',
			'items_per_page'    => '1000',
			'alphabet_headings' => 'yes',
			'link'              => 'yes'
		);

		$atts = shortcode_atts( $default_atts, $user_atts, 'glossary' );

		// enforce a max items_per_page to prevent performance issues
		$atts['items_per_page'] = min( $atts['items_per_page'], 1000 );

		$args = array(
			'post_type'      => 'glossary',
			'posts_per_page' => $atts['items_per_page'],
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			if ( $atts['alphabet_headings'] === "yes" ) {
				$letter = '';
			}

			echo "<div class='glossary'>";
			echo "<dl>";

			while ( $query->have_posts() ) {

				$query->the_post();

				if ( $atts['alphabet_headings'] === "yes" ) {
					if ( $letter !== strtoupper( get_the_title()[0] ) ) {
						$letter = strtoupper( get_the_title()[0] );
						echo '</dl>' . PHP_EOL . '<h3 class="glossary__alphabet_headings">' . esc_html( $letter ) . '</h3>' . PHP_EOL . '<dl>';
					}
				}

				echo "<div class='glossary__item'>";


				if ( $atts['link'] === 'yes' ) {
					echo '<dt><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></dt>';
				} else {
					echo '<dt>' . esc_html( get_the_title() ) . '</dt>';
				}

				if ( $atts['excerpts'] === 'yes' ) {
					echo "<dd>";
					if ( $atts['thumbnails'] === 'yes' ) {
						the_post_thumbnail( 'thumbnail', array( 'class' => 'glossary__img alignright' ) );
					}
					echo wp_kses_post( get_the_excerpt() ) . "</dd>";
				}

				echo "</div>" . PHP_EOL;

			}

			wp_reset_postdata();
			echo "</dl>";
			echo "</div>";

		}
	}
}

$wpvip_glossary = new WPVIP_Glossary();
