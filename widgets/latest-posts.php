<?php
class S_Latest_Posts extends WP_Widget {

	function S_Latest_Posts(){
		$widget_ops = array( 'classname' => 'latest_posts', 'description' => 'Show recent/popular/random posts from your blog, with excerpts.' );
		$this->WP_Widget( 's_latest', 'S Posts', $widget_ops );
	}


	function widget($args, $instance) { 
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$number = absint( $instance['number'] );

		$sort_by=$instance['sort_by'];

		$categories=(array) $instance['categories'];

		echo $before_widget;
		if($title):
			echo $before_title;
				echo $title;
			echo $after_title;
		endif;


		$args=array();

		//Number
		$args['posts_per_page'] = $number;

		//Categories
		$args['category__in']=$categories;


		//Order by
		if($sort_by == "popular"):
			$args['orderby']= "comment_count";

		elseif($sort_by == "random"):
			$args['orderby']= "rand";

		else:
			$args['orderby']= "date";

		endif;






		$get_posts_query = new WP_Query($args);

		if($get_posts_query->have_posts()): while($get_posts_query->have_posts()): $get_posts_query->the_post();
			global $post;


?>
					<div>
							<span><a href="<?php the_permalink(); ?>"><?php the_title(); ?> | <?php the_time('F j, Y'); ?> </a></span>
							<p><?php s_excerpt(); ?></p>
							<span><?php _e('at', 's');?> <?php the_time('g:i A'); ?> <a href="<?php comments_link(); ?>"><?php comments_number(__( '0 Comments', 's' ), __( '1 Comment', 's' ), __( '% Comments', 's' ) ); ?></a></span>
					</div>

<?php
		endwhile; endif;



		echo $after_widget;

	}






	function form($instance) {


		$instance = wp_parse_args( (array) $instance, array('number' => 2 ) );

        $title= esc_attr($instance['title']);
		$number = absint( $instance['number'] );
		$sort_by=$instance['sort_by'];
		$categories=(array) $instance['categories'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               Title:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>


		<p> 
			<label>
               Select categories:
            </label>
			<br />
		<?php
		$all_categories=get_categories('hide_empty=0&orderby=name');
		foreach ($all_categories as $cat ):
			$cat_id=intval($cat->cat_ID);
			$cat_name=$cat->cat_name;
			$selected='';
			if(in_array($cat_id, $categories))
				$selected=' checked="checked"';


		?>
			<input value="<?php echo $cat_id; ?>" class="checkbox" type="checkbox"<?php echo $selected; ?> id="<?php echo $this->get_field_id('categories'); echo $cat_id; ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" /> <label for="<?php echo $this->get_field_id('categories'); echo $cat_id; ?>"><?php echo $cat_name; ?></label> <br />
		<?php
		endforeach;
		?>

		</p>






		<p> 
			<label for="<?php echo $this->get_field_id('sort_by'); ?>">
               Sort them by:
            </label>
			<select name="<?php echo $this->get_field_name('sort_by'); ?>" id="<?php echo $this->get_field_id('sort_by'); ?>" class="widefat">
					<option value="popular"<?php selected( $instance['sort_by'], 'popular' ); ?>>Most Popular</option>
					<option value="recent"<?php selected( $instance['sort_by'], 'recent' ); ?>>Most Recent</option>
					<option value="random"<?php selected( $instance['sort_by'], 'random' ); ?>>Random</option>
			</select>
		</p>



		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               Number of posts to get:
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>

<?php
	}



	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
		$instance['categories']  = (array)$new_instance['categories'];
		$instance['sort_by'] =$new_instance['sort_by'];
		$instance['number'] = absint( $new_instance['number'] );
        return $instance;

    }

}
?>