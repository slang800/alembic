		<ul id="slider" class="jcarousel-skin-s">

<?php
//If the user has chosen to populate the slider with recent posts

if(get_option('s_slider_source', 'Latest Posts') == "Latest Posts"):

	$num_items = 5;
	if(get_option('s_slider_items'))
		$num_items = intval(get_option('s_slider_items'));

	$args = array();

	$args['post_type'] = 'portfolio'; //Get posts only from the Portfolio post type
	$args['posts_per_page'] = $num_items; //Get a user-defined number of items

	$slider_query = new WP_Query($args);

	if($slider_query->have_posts()) : while($slider_query->have_posts()) : $slider_query->the_post();
		$image_url = s_post_image();
		$image_url = s_build_image($image_url, 270, 170);

?>

			  <li>
			  	<div class="avatar">
			  		<span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
			  		<img src="<?php echo $image_url; ?>" alt="<?php the_title(); ?>" />
			  	</div>

			  
			  	<p><?php s_excerpt(25); ?></p>
			  
			  
			  	<div class="links">
			  		<a class="more" href="<?php the_permalink(); ?>"><?php _e('Read More', 's'); ?></a>
			  		<div class="tags"><?php echo get_the_term_list( $post->ID, 'portfolio_cat', '', ', ', '' ); ?></div>

			  	</div>
			  </li>
			  
<?php
	endwhile; endif;
?>


<?php
else:
	$slider_images=get_option('s_slider_images');
	$slider_links=get_option('s_slider_links');
	$slider_descs=get_option('s_slider_desc');
	$slider_titles=get_option('s_slider_title');

	$total=count($slider_images);

	for($i=0; $i<$total; $i++):
		$this_image = s_build_image($slider_images[$i], 270, 170);
		$this_link = stripslashes($slider_links[$i]);
		$this_desc = stripslashes($slider_descs[$i]);
		$this_title = stripslashes($slider_titles[$i]);
?>

			  <li>
			  	<div class="avatar">
			  		<span><a href="<?php echo $this_link; ?>"><?php echo $this_title; ?></a></span>
			  		<img src="<?php echo $this_image; ?>" alt="<?php echo $this_title; ?>" />
			  	</div>

			  
			  	<p><?php echo $this_desc; ?></p>
			  
			  
			  	<div class="links">
			  		<a class="more" href="<?php echo $this_link; ?>"><?php _e('Read More', 's'); ?></a>
			  	</div>
			  </li>
			  
<?php
	endfor;


endif;
?>

		</ul>