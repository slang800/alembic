<?php

//TITLES
function s_titles() {

	

	$separator=stripslashes(get_option('s_separator'));
	
	if(!$separator)
		$separator="|";
	
	if(is_front_page())
		bloginfo('name');	
		
	else if (is_single() or is_page() or is_home()){
		bloginfo('name'); 
		wp_title($separator,true,'');
	}
	
	else if (is_404()){
		bloginfo('name');	
		echo " $separator ";
		_e('404 error - page not found', 's');
	}
	
	else{
		bloginfo('name'); 
		wp_title($separator,true,'');
	}
	
	
}

//GET THE PORTFOLIO IMAGE
function s_post_image(){
	global $post;
	$image = '';
	
	//Get the image from the post meta box
	$image = get_post_meta($post->ID, 's_post_image', true);	
	if($image) return $image;
	
	//If the above doesn't exist, get the post thumbnail
	$image_id = get_post_thumbnail_id($post->ID);
	$image = wp_get_attachment_image_src($image_id, 's_thumb');
	$image = $image[0];
	if($image) return $image;
	
	
	//If there is still no image, get the first image from the post
	return s_get_first_image();

}


//GET FIRST IMAGE FROM POST CONTENT
function s_get_first_image(){
	global $post, $posts;
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		
	$first_img="";
		
	if(isset($matches[1][0]))
		$first_img = $matches[1][0];
			
	return $first_img;
}
	
	


//BUILD IMAGE RESIZE
function s_build_image($img='', $w=false, $h=false, $zc=1 ){

	if($h)
		$h = "&amp;h=$h";
	else
		$h = '';
		
	if($w)
		$w = "&amp;w=$w";
	else
		$w = '';
		
	$image_url = S_THEME_DIR . "/php/timthumb.php?src=" . $img . $h . $w;
	
	return $image_url;


}


//Get all images from post

function my_attachment_gallery($postid=0, $size='thumbnail', $attributes='') {



	if ($postid<1) $postid = get_the_ID();



	if ($images = get_children(array(



		'post_parent' => $postid,



		'post_type' => 'portfolio',



		'order' => 'DESC',



		'numberposts' => 0,



		'post_mime_type' => 'image',)))



		foreach($images as $image) {



			$attachment=wp_get_attachment_image_src($image->ID, $size);

			$full_attachment=wp_get_attachment_image_src($image->ID, 'full');



			?><div class="feature"><a href="<?php echo $full_attachment[0]; ?>"><img src="<?php echo $attachment[0]; ?>" <?php echo $attributes; ?> /></a></div><?php



		}



}

//DISPLAY MAIN MENU
function s_menu(){

	//If this is WordPress 3.0 and above AND if the menu location registered in functions/register-wp3.php has a menu assigned to it
	if(function_exists('wp_nav_menu') && has_nav_menu('main_menu')):
	
		/*
		 Display the Nav menu with:
		  - the slug main_menu
		  - no container element
		  - a menu class of sf-menu
		  - a depth of 2 (main level and first child)
		  - the custom walker defined below, s_menu_walker
		*/
		
		wp_nav_menu( 
			array( 
				'theme_location' => 'main_menu', 
				'container' => '', 
				'menu_class' => 'sf-menu', 
				'depth' => 2, 
				'walker' => new s_menu_walker()) 
		);
		
	
		
	
	//If either this is WP version<3.0 or if a menu isn't assigned, use wp_list_pages()
	else:
		echo '<ul class="sf-menu">';
			wp_list_pages('depth=1&title_li=');
		echo '</ul>';
	
	endif;		
	
	
}

//CUSTOM EXCERPT LENGTH
function s_excerpt($len=20, $trim="&hellip;"){
	$limit = $len+1;
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	$num_words = count($excerpt);
	if($num_words >= $len){
		$last_item=array_pop($excerpt);
	}
	else{
	$trim="";
	}
	$excerpt = implode(" ",$excerpt)."$trim";
	echo $excerpt;

}



//S MENU WITH DESCRIPTION
class s_menu_walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth, $args){	
	
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

		if($depth != 0){
			$description = "";
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= $description . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
	}
}




//S BREADCRUMBS
function s_breadcrumbs(){

	//No breadcrumbs if disabled
	if(get_option('s_show_breadcrumbs', 'true') == 'false')
		return;

	//No breadcrumbs on homepage
	if(is_front_page())
		return;
		

	$breadcrumb_sep=' / '; // Separator
			
	global $post;
?>	
	<div class="bread_crumbs">

		<a href="<?php bloginfo('url'); ?>"><?php _e('Home', 's'); ?></a>

<?php echo $breadcrumb_sep; ?>

<?php

$blog_page_id=get_option('page_for_posts');

	//Single post
	if(is_single()){	
		//Portfolio posts
		if(get_query_var('post_type') == 'portfolio')
			_e('Portfolio', 's');
		//Blog posts
		else{
			echo '<a href="' . get_permalink($blog_page_id) . '">';
			echo get_the_title($blog_page_id);		
			echo '</a>';
		}
		echo $breadcrumb_sep;
		the_title();
	
	}

	if ( is_home()) {
		echo get_the_title($blog_page_id);	
	}	
	

	if ( is_page() && $post->post_parent==0 ) {
			the_title();
	}
	elseif( is_page() && $post->post_parent!=0 ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb)
				echo $crumb . $breadcrumb_sep;
			the_title();
	}
	elseif (is_category() ) {
			_e('Archive for category', 's');
			echo ' &#39;';
			single_cat_title();
			echo '&#39;';
 
	}
	elseif ( is_tax() ) {
			global $wp_query;	
			$term = $wp_query->get_queried_object();	
			$taxonomy = get_taxonomy ( get_query_var('taxonomy') );
			$term = $term->name;
			_e('Archive for', 's');
			echo ' ' . strtolower($taxonomy->labels->singular_name) . ' &#39;' . $term . '&#39;';
 
	}
	elseif ( is_day() ) {
    	echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> / ';
    	echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> / ';
    	echo get_the_time('d');
	} 
	elseif ( is_month() ) {
    	echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> / ';
    	echo get_the_time('F'); 
	} 
	elseif ( is_year() ) {
    	echo get_the_time('Y'); 
	} 	
	elseif ( is_search() ) {
			_e('Search results for', 's');
			echo ' &#39;' . get_search_query() . '&#39;'; 
	}
	elseif ( is_tag() ) {
			_e('Posts tagged', 's'); 
			echo ' &#39;';
			single_tag_title();
			echo '&#39;';
	}
	
	if ( get_query_var('paged') ) {
		printf( __( ' (Page %s) ', 's' ), get_query_var('paged') );
	}
?>
</div>
<?php	
}




//BUILD A LIST OF CATEGORIES TO EXCLUDE
function s_build_cat_exclude(){

	$categories = get_categories('hide_empty=0&orderby=id');
	$exclude="";
	
	foreach($categories as $cat):
		$cat_field = 's_cat_' . $cat->cat_ID;
		if( get_option($cat_field) and get_option($cat_field)=='false')
			$exclude .= "-" . $cat->cat_ID . ",";		
	endforeach;		
	
	if($exclude)
		$exclude = substr($exclude, 0, -1); //Remove the last comma
		
		
	return $exclude;
}



?>