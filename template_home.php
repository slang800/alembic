<?php /* Template Name: Home */ ?>
<section>
		<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				<?php
					if(!get_the_title()=='')
						echo '<h2>'.get_the_title().'</h2>';
					the_content();
				?>
		<?php endwhile; ?>
		<?php else: ?>
			<div class="entry">
				<p><?php _e('No posts were found.', 's'); ?></p>
			</div>
		<?php endif; ?>
</section>

<?php include("wrapper.php"); ?>
