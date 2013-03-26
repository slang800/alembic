<?php get_header(); ?>
<section id="content" class="grid-container blog">
	<div class="clearfix inner">
		<div class="grid-100">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				<div class="date"><p class="post-date"><span class="post-date-day"><?php the_time('d'); ?> </span><br><span class="post-date-month"><?php the_time('F'); ?></span></p></div>
				<div class="entry">
					<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>						
						<p class="post-meta">
<a href="<?php comments_link(); ?>"><?php comments_number(__( 'No Comments', 's' ), __( '1 Comment', 's' ), __( '% Comments', 's' ) ); ?></a>
 | <?php printf( __('Posted by %1$s in %2$s', 's') , get_the_author(), get_the_category_list( ', ' ) ); ?>
 

						</p>	
					<?php the_content(); ?>		
					<?php comments_template(); ?>	
				</div>
					
				<?php endwhile; endif; ?>
        </div>
	</div>
</section>	
<?php get_footer(); ?>