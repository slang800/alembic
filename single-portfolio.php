
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


<style>
.wrap {
	display: relative;
}

.wp-showcase {
	/*display: inline-block;*/
	padding: 15px;
	margin: auto;
	width: 85%;
}

.wp-showcase-gallery img{
	width: 60px;
}
.wp-showcase li {
	border: none;
}
.wp-showcase-gallery li, .wp-showcase li{
	border: 5px solid transparent;
}

#portfolio-sidebar {
	min-width: 150px;
}

#portfolio-sidebar .info li {
	display: list-item;
}
#portfolio-sidebar ul, #portfolio-sidebar li {
	margin: 0;
}

.flexslider {
	width: 60%;
}

.flexslider li {
	margin: 0;
}

#portfolio-sidebar, .flexslider{
	vertical-align: top;
	display: inline-block;
	background: white;
	margin: 15px;
	padding: 5px;
}

.wp-showcase-gallery{
	background: none;
}
</style>

<?php include("wrapper.php"); ?>
