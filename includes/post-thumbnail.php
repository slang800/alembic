<?php
	$image_url = s_post_image();

	if(!$image_url)
		return;

	$image_url = s_build_image($image_url, 110);
?>

		<a href="<?php the_permalink(); ?>"><img class="image alignleft" alt="<?php the_title(); ?>" src="<?php echo $image_url; ?>" /></a>