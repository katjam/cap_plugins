<?php

/**
 * Plugin Name: Capriol custom type film post 
 * Description: Provides film post type for Capriol Films. 
 * Author: Katja Mordaunt
 */

add_action( 'init', 'cap_create_film_post' );
add_action( 'admin_init', 'cap_custom_film_type_admin' );
add_action( 'save_post', 'save_cap_film_prod_custom_fields' );

// Add terms once.
register_activation_hook( __FILE__, 'cap_add_terms_to_film_type' );
add_action ('init', 'cap_film_type_taxonomies' );

function cap_create_film_post() { 
	register_post_type(
		'capriol-film',
		array(
			'labels' => array(
				'name' => __( 'Capriol Films', 'capriol-film' ),
				'singular_name' => __( 'Capriol Film', 'capriol-film' ),
				'add_new' => __( 'Add New', 'capriol-film' ),
				'add_new_item' => __( 'Add New film', 'capriol-film' ),
				'edit_item' => __( 'Edit Capriol Film', 'capriol-film' ),
				'new_item' => __( 'New Capriol Film', 'capriol-film' ),
				'view_item' => __( 'View Capriol Film', 'capriol-film' ),
				'search_items' => __( 'Search Capriol Film productions', 'capriol-film' ),
				'not_found' => __( 'No Capriol Film productions found', 'capriol-film' ),
				'not_found_in_trash' => __( 'No Capriol Film productions in Trash', 'capriol-film' ),
			),
			'hierarchical' => false,
			'description' => 'Capriol Films - production',
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies' => array( '' ),
			'public' => true,
			//'show_ui' => true,
			//'show_in_menu' => true,
			'menu_position' => 15,
			//'show_in_nav_menus' => true,
			//'publicly_queryable' => true,
			//'exclude_from_search' => false,
			'has_archive' => true,
			//'query_var' => true,
			//'can_export' => true,
			'rewrite' => true,
		)
	);
}

function cap_film_type_taxonomies() {
	// Register custom taxonomy for capriol-films
	// hierarchical (like categories)
	$labels = array(
		'name'                => _x( 'Production types', 'taxonomy general name' ),
		'singular_name'       => _x( 'Production type', 'taxonomy singular name' ),
		'search_items'        => __( 'Search Production types' ),
		'all_items'           => __( 'All Production types' ),
		'parent_item'         => __( 'Parent Production type' ),
		'parent_item_colon'   => __( 'Parent Production type:' ),
		'edit_item'           => __( 'Edit Production type' ), 
		'update_item'         => __( 'Update Production type' ),
		'add_new_item'        => __( 'Add New Production type' ),
		'new_item_name'       => __( 'New Production type' ),
		'menu_name'           => __( 'Production types' ),
	); 	

	$tax_args = array(
		'hierarchical'        => true,
		'labels'              => $labels,
		'show_ui'             => true,
		'show_admin_column'   => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'production-types' )
	);	

	register_taxonomy( 'production-types', 'capriol-film', $tax_args );
	register_taxonomy_for_object_type( 'production-types', 'capriol-film' );
 }

/**
 * Populate production type categories with default terms.
 */
function cap_add_terms_to_film_type() {
	cap_film_type_taxonomies();
	$tax_name = 'production-types';
	$categories    = array(
		'Cinema',
		'Television',
		'Development',
	);
	foreach ( $categories as $category ) {
		wp_insert_term(
			$category,
			$tax_name,
			array(
				'description' => '',
				'slug' => sanitize_title( $category ),
				'parent' => '',
			)
		);
	}

}

function cap_custom_film_type_admin() {
	add_meta_box(
		'cap_film_prod_image',
		'Film Production Image',
		'display_cap_film_image_meta_box',
		'capriol-film', 'normal', 'high'
	);
}

function display_cap_film_image_meta_box( $film ) {
	// Retrieve current field values.
	$image = get_post_meta( $film->ID, 'cap_film_prod_image', true );

  // Create nonce field for verification
  wp_nonce_field( basename( __FILE__ ), 'cap_film_prod_nonce');

  $output  = '<textarea name="cap_film_prod_image" cols="65" value="'. $definition .'">' . $definition . '</textarea>';

	echo $output;
}


function save_cap_film_prod_custom_fields( $film_id ) {
  // Verify nonce.
  if ( !isset( $_POST['cap_film_prod_nonce'] )
        || !wp_verify_nonce( $_POST['cap_film_prod_nonce'], basename( __FILE__ ) ) 
      )
        return $post_id;

  // Check post type for film production 
	$film = get_post( $film_id );
	if ( $film->post_type == 'capriol-film' ) {
    foreach ( $_POST as $key => $value ) {
      // Store data in post meta table if present in post data
      if ( isset( $value ) && $value != '' ) {
				update_post_meta( $word_id, $key, $value );
			}	
		}
	}
}

?>
