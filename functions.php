<?php


add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'jquery' ); // which version do we need? I think this one embeds WP version
	wp_enqueue_script( 'vue-router', get_template_directory_uri() . '/assets/js/vue-router.js', [ 'vuejs' ] );
	wp_enqueue_script( 'vuejs', get_template_directory_uri() . '/assets/js/vue.js' );
} );

// make sure this single.php would work with all public post types, not just posts and pages - por lo que yo entiendo, esto hace visibles los custom posts typs en el REST. Capaz no es necesario en las versiones nuevas
add_action( 'init', function () {
	global $wp_post_types;
	foreach ( get_post_types() as $post_type ) {
		if ( $wp_post_types[ $post_type ]->public && ! $wp_post_types[ $post_type ]->show_in_rest ) {
			$wp_post_types[ $post_type ]->show_in_rest = true;
			if ( empty( $wp_post_types[ $post_type ]->rest_base ) ) {
				$wp_post_types[ $post_type ]->rest_base = $wp_post_types[ $post_type ]->name;
			}
		}
	}
}, 40000 );