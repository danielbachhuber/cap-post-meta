<?php
/**
 * Plugin Name: Co-Authors Plus Post Meta
 * Version: 0.1-alpha
 * Description: Store Co-Authors Plus data in post meta
 * Author: Daniel Bachhuber
 * Author URI: https://handbuilt.co
 * Plugin URI: https://handbuilt.co
 * Text Domain: cap-post-meta
 * Domain Path: /languages
 * @package Co-Authors Plus Post Meta
 */

/**
 * Get the co-authors for a post, from the post's meta
 *
 * @param integer $post_id
 * @return array
 */
function cpm_get_coauthors( $post_id ) {
	$logins = get_post_meta( $post_id, 'cpm_coauthors', true );
	$users = array();
	foreach( $logins as $login ) {
		$user = get_user_by( 'login', $login );
		if ( $user ) {
			$users[] = $user;
		}
	}
	return $users;
}

/**
 * Display the co-authors for a post, separated by commas
 *
 * @param integer $post_id
 */
function cpm_the_coauthors( $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return;
	}
	$users = cpm_get_coauthors( $post_id );
	if ( ! $users ) {
		return;
	}
	echo wp_sprintf( 'By %l',
			array_map( function( $user ) { return '<a href="' . get_author_posts_url( $user->ID, $user->user_nicename ) . '">' . esc_html( $user->display_name ) . '</a>'; }, $users )
		);
}

/**
 * Save co-authors for a post to post meta
 */
function cpm_save_coauthors( $post_id ) {
	if ( ! function_exists( 'get_coauthors' ) ) {
		return;
	}
	$coauthors = get_coauthors( $post_id );
	$meta_authors = array();
	foreach( $coauthors as $coauthor ) {
		if ( ! empty( $coauthor->caps ) ) {
			$meta_authors[] = $coauthor->user_login;
		}
	}
	update_post_meta( $post_id, 'cpm_coauthors', $meta_authors );
}
add_action( 'save_post', 'cpm_save_coauthors', 11 ); // After CAP has saved its authors
