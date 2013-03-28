<?php

class s_meta_box{

	var $options; //Options to display for the meta box
	var $box_title;  //Title, displayed at the top of the meta box.
	var $location;  //Where to display the meta box - post, page, custom post type etc
	var $box_name;  //Unique name for the meta box


	function s_meta_box($info, $options){

		$this->info=$info;
		$this->box_title=$info['title']; 
		$this->location=$info['location'];
		$this->box_name=$info['box_name'];
		$this->options=$options;


		add_action('admin_init', array(&$this, 's_add_metabox'));
		add_action('save_post', array(&$this, 's_save_metabox'));

	}



	function s_add_metabox(){

		if ( function_exists('add_meta_box') and is_array($this->location) ) :
			foreach($this->location as $loc):

				add_meta_box(
				$this->box_name, //ID
				$this->box_title, //Title
				array(&$this, 's_display_metabox'), //Callback function to print HTML
				$loc, //Place to display
				'high' //Context
				);

			endforeach;

		endif;

	}



	function s_display_metabox(){

		global $post;
?>

	<div class="form-wrap">
<?php
	wp_nonce_field( plugin_basename( __FILE__ ), $this->box_name .'_wpnonce', false, true ); //Security field for verification


	foreach ($this->options as $value):
		$data = get_post_meta($post->ID, $value['name'], true);
		if(!$data) $data=$value['default'];
?>
		<div class="form-field form-required">
		<label for="<?php echo $value['name']; ?>"><strong><?php echo $value['title']; ?></strong></label>
<?php

		switch($value['type'] ){

			case "text": $this->display_meta_text($value, $data); break;

			case "textarea": $this->display_meta_textarea($value, $data); break;

			case "select": $this->display_meta_select($value, $data); break;

		}
?>

		</div>
<?php
		endforeach;
?>

	</div><!-- //form_wrap -->

<?php
	}




	function s_save_metabox(){

		global $post;

		if ( !wp_verify_nonce( $_POST[ $this->box_name . '_wpnonce' ], plugin_basename(__FILE__) ) )
			return $post->ID;

		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;

		foreach($this->options as $meta_box ):

			$data = $_POST[ $meta_box['name'] ];

			if($data=="")
				delete_post_meta($post->ID, $meta_box['name'], $data);
			else
				update_post_meta($post->ID, $meta_box['name'], $data);

		endforeach;

	}




	function display_meta_text($meta_box, $data){
?>
		<input type="text" id="<?php echo $meta_box['name']; ?>" name="<?php echo $meta_box['name']; ?>" value="<?php echo esc_html($data); ?>" />

		<p>
			<?php echo $meta_box[ 'desc' ]; ?>
		</p>
<?php
	}



	function display_meta_textarea($meta_box, $data){
?>
		<textarea id="<?php echo $meta_box['name']; ?>" name="<?php echo $meta_box['name']; ?>"><?php echo stripslashes($data); ?></textarea>

		<p>
			<?php echo $meta_box[ 'desc' ]; ?>
		</p>
<?php
	}








	function display_meta_select($meta_box, $data){
?>
		<select name="<?php echo $meta_box['name']; ?>" id="<?php echo $meta_box['name']; ?>" >  
<?php
		foreach ($meta_box['options'] as $option):
			echo'<option';  
				  
			if ( $data == $option) {  
					   echo ' selected="selected"';  
				} 
			echo '>'.$option.'</option>';  

		endforeach;  
?>
		</select>

	<p>
		<?php echo $meta_box[ 'desc' ]; ?>
	</p>
<?php

	}




}//end class
?>