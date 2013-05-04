<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; endif; ?>
<div class="grid-100">
	<?php 
		$promobox = get_post_meta($post->ID,'_promo_area',true);
		echo $promobox; 
	?>
</div>
<div class="clear"></div>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/assets/css/gallery.css">

<?php include("wrapper.php"); ?>
