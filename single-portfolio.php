<section id="content"  class="grid-container">
    <div class="clearfix">
        <div>
			<div class="clearfix">
				<div class="grid-20 sidebar">
					<div class="gal-slide">
						<?php next_posts_link('Older Entries �', 0); ?>
						<?php previous_post_link( '%link', __( '<img src="' . S_THEME_DIR . '/assets/img/icon-left-arrow.png" alt="">', 's' ) ); ?>
						<?php next_post_link( '%link', __( '<img src="' . S_THEME_DIR . '/assets/img/icon-right-arrow.png" alt="">', 's' ) ); ?>
					</div>
					<h1 class="portfolio-title"><?php the_title(); ?></h1>
					<div class="side-info">
						<?php 
							$image_url = s_post_image(); //Use the function to fetch the portfolio image
							if($image_url){	$image_url = s_build_image($image_url, 150, 150);}
							echo '<p><img class="image align-left" alt="" src="'.$image_url.'" /></p>'; 
						?>
						<?php 	
							$place = get_post_meta($post->ID,'_place',true);
							$date = get_post_meta($post->ID,'_date',true); 
							$price = get_post_meta($post->ID,'_price',true); 
						?>
						<p><?php echo $place; ?></p>
						<p><?php echo $price; ?></p>
						<p><?php echo $date; ?></p>
					</div>
				<?php if(function_exists('dynamic_sidebar') && dynamic_sidebar('Portfolio Sidebar')) : endif; ?>
				</div>
				<div class="grid-80">
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
						<div class="portfolio-content">
							<?php the_content(); ?>
						</div>
					<?php endwhile; endif; ?>
				</div>
			</div>
		</div>
	</div> 
	<div class="grid-100">
		<?php 
			$promobox = get_post_meta($post->ID,'_promo_area',true);
			echo $promobox; 
		?>
	</div>
	<div class="clear"></div>
</section>

<?php include("wrapper.php"); ?>
