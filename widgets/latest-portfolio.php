<?php
class S_Latest_Portfolio extends WP_Widget {

	function S_Latest_Portfolio(){
		$widget_ops = array( 'classname' => 'latest_portfolio', 'description' => 'Show the latest portfolio post you have written, with an image and excerpt.' );
		$this->WP_Widget( 's_portfolio', 'S Latest Portfolio', $widget_ops );
	}
	
	
	function widget($args, $instance) { 
		extract($args);
						
		$title = apply_filters('widget_title', $instance['title']);		
		if ( empty($title) ) $title = false;
		
		
		echo $before_widget;
		if($title):
			echo $before_title;
				echo $title;
			echo $after_title;	
		endif;
		
		
		$args = array(
				'post_type' => 'portfolio',
				'posts_per_page' => '1'
				);
		
		$portfolio_posts_query = new WP_Query($args);
		
		if($portfolio_posts_query->have_posts()): while($portfolio_posts_query->have_posts()): $portfolio_posts_query->the_post();
			global $post;
			
		
?>

			<div class="project_details">
				<div class="avatar">

			  		<span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
			  		<img src="<?php echo s_build_image(s_post_image(), 270, 170); ?>" alt="<?php the_title(); ?>" />
			  	</div>
			  	
					<p><?php s_excerpt(); ?></p>
			  		
			</div>

					
						
<?php		
		endwhile; endif;
	
		
		
		echo $after_widget;
			
	}
	
	
	
	
	

	function form($instance) {	
	
	
	
        $title= esc_attr($instance['title']);		
		
?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               Title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		
		<p>
			<code><em>This widget shows your latest portfolio post.</em></code>
		</p>
		

<?php
	}

	
	
	function update($new_instance, $old_instance) {
        $instance=$old_instance;		
		
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;

    }
	
}
?>