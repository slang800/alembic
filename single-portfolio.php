<?php get_header(); ?>
    <section id="content"  class="grid-container">
    	<div class="clearfix">
        <div>
        	<div class="grid-100"><h1 class="welcome"> <?php the_title(); ?></h1></div>
          <div class="clearfix">
          	<div class="grid-20">
				<div class="gal-slide">
					<?php next_posts_link('Older Entries »', 0); ?>
					<?php previous_post_link( '%link', __( '<img src="'. S_THEME_DIR.'/assets/img/icon-left-arrow.png" alt="">', 's' ) ); ?>
					<a href="#"><img src="<?php echo S_THEME_DIR; ?>/assets/img/icon-sort.png" alt=""></a>
					<?php next_post_link( '%link', __( '<img src="'. S_THEME_DIR.'/assets/img/icon-right-arrow.png" alt="">', 's' ) ); ?>
				</div>
              <?php	if(function_exists('dynamic_sidebar') && dynamic_sidebar('Portfolio Sidebar')) : endif; ?>
            </div>
          	<div class=" grid-80">
              <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			  <?php the_content(); ?>		
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
  	<?php get_footer(); ?>