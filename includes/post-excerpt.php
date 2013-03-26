				<div class="entry">
						<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="post-meta"><?php include(S_INCLUDES . 'post-meta.php'); ?></p>
					<?php include(S_INCLUDES . 'post-thumbnail.php'); ?>
					<?php the_excerpt(); ?>

				</div>