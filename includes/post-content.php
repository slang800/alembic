				<div class="entry">			
						<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>						
						<p class="post-meta">
<a href="<?php comments_link(); ?>"><?php comments_number(__( 'No Comments', 's' ), __( '1 Comment', 's' ), __( '% Comments', 's' ) ); ?></a>
 | <?php printf( __('Posted by %1$s in %2$s', 's') , get_the_author(), get_the_category_list( ', ' ) ); ?>
 

						</p>	
					<?php the_content(); ?>										
				</div>