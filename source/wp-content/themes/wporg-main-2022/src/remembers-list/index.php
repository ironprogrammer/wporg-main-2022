<?php
/**
 * Block Name: Remembers Contributor List
 * Description: Displays a list of memorialized contributors..
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Main_2022\Remembers_List_Block;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/remembers-list',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {

	if ( ! function_exists( '\WordPressdotorg\MemorialProfiles\get_profiles' ) ) {
		return __( 'Memorial Profiles mu-plugin is missing.', 'wporg' );
	}

	$profiles = \WordPressdotorg\MemorialProfiles\get_profiles();

	$columns     = $attributes['columns'];
	$group_count = ceil( count( $profiles ) / $columns );

	$groups = array();
	for ( $i = 0; $i < $group_count; $i++ ) {
		$groups[] = array_slice( $profiles, $i * $columns, $columns );
	}

	$block_content = '';
	foreach ( $groups as $group ) {
		// Set isStackedOnMobile to false so that the columns are not stacked on mobile. We override this in CSS to stack them.
		$block_content .= '<!-- wp:columns {"isStackedOnMobile":false} --><div class="wp-block-columns is-not-stacked-on-mobile">';

		foreach ( $group as $profile ) {
			$block_content .= '<!-- wp:column --><div class="wp-block-column">';
			$block_content .= '<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|40","right":"var:preset|spacing|default","bottom":"var:preset|spacing|40","left":"var:preset|spacing|default"}}},"fontSize":"extra-large"} -->';
			$block_content .= '<h2 class="wp-block-heading has-text-align-center has-extra-large-font-size" style="margin-top:var(--wp--preset--spacing--40);margin-right:var(--wp--preset--spacing--default);margin-bottom:var(--wp--preset--spacing--40);margin-left:var(--wp--preset--spacing--default)">';
			$block_content .= '<em>';
			$block_content .= '<a href="' . esc_url( 'https://profiles.wordpress.org/' . $profile->user_nicename ) . '">' . esc_html( $profile->display_name ) . '</a>';
			$block_content .= '</em>';
			$block_content .= '</h2>';
			$block_content .= '<!-- /wp:heading -->';
			$block_content .= '</div><!-- /wp:column -->';
		}

		$block_content .= '</div><!-- /wp:columns -->';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		do_blocks( $block_content )
	);
}
