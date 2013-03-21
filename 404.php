<?php get_header(); ?>
<section id="content" class="grid-container">
	<div class="clearfix inner">
		<div class="grid-25">
          <?php	if(function_exists('dynamic_sidebar') && dynamic_sidebar('Page Sidebar')) : 	endif; ?>
        </div>
		<div class="grid-75">
			<h2><?php _e('404 Error - Page Not Found', 's'); ?></h2>	
			<?php echo stripslashes(get_option('s_404')); ?>
        </div>
	</div>
</section>	
<?php get_footer(); ?>
