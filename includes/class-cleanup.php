<?php

namespace Posty;

/**
 * Cleanup some unnecessary WordPress functionalities.
 */
class Cleanup {
	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'clean_head' ] );
		add_action( 'init', [ __CLASS__, 'disable_emoji' ] );
		add_action( 'wp_footer', [ __CLASS__, 'remove_wp_embed' ] );
	}

	public static function remove_wp_embed(): void {
		wp_dequeue_script( 'wp-embed' );
		wp_deregister_script( 'wp-embed' );
	}

	public static function clean_head(): void {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'wp_generator' );
	}

	public static function disable_emoji(): void {
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		add_filter( 'emoji_svg_url', '__return_false' );
		add_filter(
			'tiny_mce_plugins',
			function( $plugins ): array {
				return is_array( $plugins ) ? array_diff( $plugins, [ 'wpemoji' ] ) : [];
			}
		);
	}
}
