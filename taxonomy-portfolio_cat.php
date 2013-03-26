<div id="content" class="container">
	<?php s_breadcrumbs(); ?>
		<div class="clear"></div>
		<div id="page">
			<div class="inner">
				<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
					<?php include (S_INCLUDES . 'post-excerpt.php'); ?>
				<?php endwhile; ?>
				<?php else: //If no posts are present ?>
					<div class="entry">
						<p><?php _e('No posts were found.', 's'); ?></p>
					</div>
				<?php endif; ?>
				<?php include (S_INCLUDES . 'pagination.php'); ?>
			</div>
		</div>
	<?php get_sidebar(); ?>
</div>
<div class="clear"></div>
<div id="sub_content">
	<div class="inner container">
		<?php
			//Include file for the columns
			include(S_INCLUDES . 'widget-columns.php');
		?>
	</div>
</div>

<?php include("wrapper.php"); ?>
			