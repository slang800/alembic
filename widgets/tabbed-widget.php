<?php
class S_Tabbed extends WP_Widget {

	function s_Tabbed(){
		$widget_ops = array( 'classname' => 's_tabbed', 'description' => 'Show a tabbed widget with popular and recent posts, and comments.' );
		$this->WP_Widget( 's_tabbed', 'S Tabbed', $widget_ops );
	}
	
	
	function widget($args, $instance) { 
		extract($args);
						
		$title = apply_filters('widget_title', $instance['title']);		
		if ( empty($title) ) $title = false;
		
		$title_popular = $instance['title_popular'] ;	
		$title_recent = $instance['title_recent'] ;	
		$title_comments = $instance['title_comments'] ;	
		$number = absint( $instance['number'] );	
				
		
		echo $before_widget;
?>		
			<div id="tabs">
<?php
		if($title):
			echo $before_title;
?>
				<h2><?php echo $title; ?></h2>
<?php			
			echo $after_title;	
		endif;
?>
				<ul>
					<li><a href="#tabs-1"><?php echo $title_popular; ?></a></li>
					<li><a href="#tabs-2"><?php echo $title_recent; ?></a></li>
					<li><a href="#tabs-3"><?php echo $title_comments; ?></a></li>
				</ul>		
			
				<div id="tabs-1">
					<div class="list_items">
						<ul>
						
<?php
		$posts_query = null;
		$args = array('posts_per_page' => $number, 'orderby' => 'comment_count');
		$posts_query = new WP_Query($args);
		
		if($posts_query->have_posts()): while($posts_query->have_posts()): $posts_query->the_post();
?>
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							
<?php   endwhile; endif; ?>

						</ul>
					</div>
				</div>
				
				<div id="tabs-2">
					<div class="list_items">
						<ul>
						
<?php		
		$args = array();
		$posts_query = null;
		$args = array('posts_per_page' => $number);
		$posts_query = new WP_Query($args);
		
		if($posts_query->have_posts()): while($posts_query->have_posts()): $posts_query->the_post();
?>			
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							
<?php   endwhile; endif; ?>


						</ul>
					</div>
				</div>		
				
				<div id="tabs-3">
					<div class="list_items">
						<ul>

<?php	
		$comments = get_comments('status=approve&type=comment&number='.$number);
		foreach ($comments as $comment):
?>
							<li><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>"><?php echo wp_html_excerpt( $comment->comment_content, 30 ); ?>&hellip;</a></li>

<?php   endforeach; ?>

						</ul>
					</div>
				</div>

			</div>

<?php	
		
		
		echo $after_widget;
			
	}
	
	
	
	
	

	function form($instance) {	
	
	
		$instance = wp_parse_args( (array) $instance, array('title_popular' => 'Popular', 'title_recent' => 'Recent', 'title_comments' => 'Comments', 'number' => 5 ) );
	
        $title= esc_attr($instance['title']);		
		$title_popular=$instance['title_popular'];
		$title_recent=$instance['title_recent'];
		$title_comments=$instance['title_comments'];	
		$number = absint( $instance['number'] );
		
?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               Title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('title_popular'); ?>">
               <em>Popular</em> tab title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title_popular'); ?>" name="<?php echo $this->get_field_name('title_popular'); ?>" type="text" value="<?php echo $title_popular; ?>" />
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('title_recent'); ?>">
              <em>Recent</em> tab title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title_recent'); ?>" name="<?php echo $this->get_field_name('title_recent'); ?>" type="text" value="<?php echo $title_recent; ?>" />
        </p>
		
				
		
		<p>
            <label for="<?php echo $this->get_field_id('title_comments'); ?>">
              <em>Comments</em> tab title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title_comments'); ?>" name="<?php echo $this->get_field_name('title_comments'); ?>" type="text" value="<?php echo $title_comments; ?>" />
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
              Number of posts per tab:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>

<?php
	}

	
	
	function update($new_instance, $old_instance) {
        $instance=$old_instance;		
		
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['title_popular']  = $new_instance['title_popular'];
		$instance['title_recent']  = $new_instance['title_recent'];
		$instance['title_comments']  = $new_instance['title_comments'];
		$instance['number'] = absint( $new_instance['number'] );	
        return $instance;

    }
	
}
?>