<?php
/**
 * WP VIP Glossary
 *
 * @package     WPVIPGlossary
 * @author      Lisa Schuyler <lisa@automattic.com>
 * @copyright   2022 Lisa Schuyler
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:     WP VIP Glossary
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

declare( strict_types=1 );

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
	 * @param mixed $atts Shortcode attributes. Associated array if any attributes are used, empty string if not.
	 * @param string|null $content Shortcode content. Default null.
	 * @param string $tag Shortcode tag (name). Default empty.
	 *
	 * @return string Shortcode output.
	 *
	 * @var string $letter Used for optional Alphabet letter headings.
	 * @var array $glossary_atts User specified attributes merged with default values for shortcode.
	 * @var array $args Arguments for WP_QUERY.
	 */

	public function create_glossary_shortcode( $atts, string $content = null, string $tag = '' ): string {

		$letter = '';

		// if no attributes specified in shortcode by user, an empty string is passed for the $atts value. Switch it to an array.
		if ( $atts === '' ) {
			$atts = [];
		}

		// change attribute keys to lowercase
		$atts = array_change_key_case( $atts, CASE_LOWER );

		// override default attributes with user specified attributes
		$glossary_atts = shortcode_atts(
			array(
				'excerpts'          => 'no',
				'thumbnails'        => 'no',
				'items_per_page'    => '1000',
				'alphabet_headings' => 'yes',
				'link'              => 'yes'
			), $atts, $tag
		);

		// enforce a max items_per_page to prevent performance issues
		$glossary_atts['items_per_page'] = min( $glossary_atts['items_per_page'], 1000 );

		$args = array(
			'post_type'      => 'glossary',
			'posts_per_page' => $glossary_atts['items_per_page'],
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			if ( $glossary_atts['alphabet_headings'] === "yes" ) {
				$letter = '';
			}

			$content .= "<div class='glossary'>";
			$content .= "<dl>";

			while ( $query->have_posts() ) {

				$query->the_post();

				if ( $glossary_atts['alphabet_headings'] === "yes" ) {
					if ( $letter !== strtoupper( get_the_title()[0] ) ) {
						$letter  = strtoupper( get_the_title()[0] );
						$content .= '</dl>' . PHP_EOL . '<h3 class="glossary__alphabet_headings">' . esc_html( $letter ) . '</h3>' . PHP_EOL . '<dl>';
					}
				}

				$content .= "<div class='glossary__item'>";

				if ( $glossary_atts['link'] === 'yes' ) {
					$content .= '<dt><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></dt>';
				} else {
					$content .= '<dt>' . esc_html( get_the_title() ) . '</dt>';
				}

				if ( $glossary_atts['excerpts'] === 'yes' ) {
					$content .= "<dd>";
					if ( $glossary_atts['thumbnails'] === 'yes' ) {
						the_post_thumbnail( 'thumbnail', array( 'class' => 'glossary__img alignright' ) );
					}
					$content .= wp_kses_post( get_the_excerpt() ) . "</dd>";
				}

				$content .= "</div>" . PHP_EOL;

			}

			wp_reset_postdata();
			$content .= "</dl>";
			$content .= "</div>";

		}

		return $content;
	}
}

$wpvip_glossary = new WPVIP_Glossary();
