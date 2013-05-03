<?php /* Template Name: Home */ ?>
<section>
	<div class="clearfix inner">
		<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			<div class="entry">
				<?php
					if(!get_the_title()=='')
						echo '<div class="grid-container"><h2>'.get_the_title().'</h2></div>';
					the_content();
				?>
			</div>
		<?php endwhile; ?>
		<?php else: ?>
			<div class="entry">
				<p><?php _e('No posts were found.', 's'); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php include("wrapper.php"); ?>
