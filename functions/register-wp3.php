<?php
//Custom Post Types

add_action( 'init', 's_create_post_types'); 
function s_create_post_types(){
	$labels=array(
		'name' => __( 'Portfolio' ),
		'singular_name' => __( 'Portfolio' )
	);

	$args=array(
		'labels' => $labels,
		'label' => __('Portfolio'),
		'singular_label' => __('Portfolio'),
		'public' => true,
		'show_ui' => true, 
		'_builtin' => false, 
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => false, 
		'supports' => array('title','editor','excerpt','revisions','thumbnail'),
		'taxonomies' => array('portfolio_cat', 'post_tag'),
		'menu_icon' => get_bloginfo('template_directory').'/functions/img/icon.png'
	);

	if(function_exists('register_post_type')):
		register_post_type('portfolio', $args);
	endif;
}



//Custom Post Type columns
add_filter("manage_edit-portfolio_columns", "s_portfolio_columns");
add_action("manage_posts_custom_column",  "s_portfolio_custom_columns");
function s_portfolio_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => _x("Portfolio Title", "portfolio title column", 's'),
			"author" => _x("Author", "portfolio author column", 's'),
			"portfolio_cats" => _x("Portfolio Categories", "portfolio categories column", 's'),
			"date" => _x("Date", "portfolio date column", 's')
		);

		return $columns;
}

function s_portfolio_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "author":
				the_author();
				break;
			case "portfolio_cats":
				echo get_the_term_list( $post->ID, 'portfolio_cat', '', ', ', '' ); 
				break;
		}
}





//Nav Menus
if(function_exists('register_nav_menu')):	
	register_nav_menu( 'main_menu', __( 'Main Menu', 's' ));
endif;




//Custom taxonomies
add_action('init', 's_taxonomies', 0);

function s_taxonomies(){

	$labels = array(
		'name' => _x( 'Portfolio Categories', 'taxonomy general name', 's' ),
		'singular_name' => _x( 'Portfolio Category', 'taxonomy singular name', 's' ),
		'search_items' =>  __( 'Search Portfolio', 's' ),
		'all_items' => __( 'All Portfolio Categories', 's' ),
		'parent_item' => __( 'Parent Portfolio Category', 's' ),
		'parent_item_colon' => __( 'Parent Portfolio Category:', 's' ),
		'edit_item' => __( 'Edit Portfolio Category', 's' ), 
		'update_item' => __( 'Update Portfolio Category', 's' ),
		'add_new_item' => __( 'Add New Portfolio Category', 's' ),
		'new_item_name' => __( 'New Portfolio Category Name', 's' )
	); 	
	
	register_taxonomy('portfolio_cat',array('portfolio'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'portfolio_categories' )

	));


}
