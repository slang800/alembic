<section>
	<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
		<?php include (S_INCLUDES . 'post-excerpt.php'); ?>
	<?php endwhile; ?>
	<?php else: //If no posts are present ?>
		<div class="entry">
			<p><?php _e('No posts were found.', 's'); ?></p>
		</div>
	<?php endif; ?>
	<?php include (S_INCLUDES . 'pagination.php'); ?>
</section>

<?php include("wrapper.php"); ?>
			