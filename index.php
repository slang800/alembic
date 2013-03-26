<section id="content" class="grid-container">
	<div class="clearfix inner">
		<div class="grid-100">
			<?php s_breadcrumbs(); ?>
			<div id="page" class="blog">
				<div class="inner clearfix">
					<?php 
						global $query_string; //We now have access to the original WordPress Query
						$exclude = s_build_cat_exclude(); //Build the list of categories to exclude
						if($exclude) $exclude = '&cat=' . $exclude;
						$posts = query_posts($query_string . $exclude); //Tell WordPress to exclude some categories, if required.
						if(have_posts()) : while(have_posts()) : the_post();
					?>
					<div class="date">
						<p class="post-date">
							<span class="post-date-day"><?php the_time('d'); ?></span>
							<br>
							<span class="post-date-month"><?php the_time('F'); ?></span>
						</p>
					</div>
					<?php
						include(S_INCLUDES . 'post-content.php');
						endwhile;
						else: //If no posts are present
					?>
					<div class="entry">
						<p><?php _e('No posts were found.', 's'); ?></p>
					</div>
					<?php
						endif;
						include(S_INCLUDES . 'pagination.php');
					?>
				</div>
			</div>
        </div>
	</div>
</section>

<?php include("wrapper.php"); ?>

			