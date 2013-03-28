<?php

class s_slider_page{

	//data members:
	var $file_name;
	var $page_title;
	var $slider_name;
	var $image_name;
	var $links_name;
	var $desc_name;
	var $title_name;

	//constructor:
	function s_slider_page($info){
	$this->info=$info;
	$this->file_name=$info['pagename']; //filename for the options page
	$this->page_title=$info['title']; //title, displayed near top of page
	$this->slider_name=$info['slider_name'];
	$this->image_name=$info['image_name'];
	$this->link_name=$info['link_name'];
	$this->desc_name=$info['desc_name'];
	$this->title_name=$info['title_name'];

	add_action('admin_menu', array(&$this, 's_add_menu'));

	}

	//Add menu item
	function s_add_menu(){

			add_submenu_page(S_MAINMENU_NAME, $this->page_title, $this->page_title, 'administrator', $this->file_name, array(&$this, 's_generate_page')); 

	}

	//Generate functions page
	function s_generate_page(){
		$this->save_options();

?>


	<h2><?php echo $this->page_title; ?></h2>
	<input type="button" class="button add-new-top" value="Add new item" />


<form method="post" id="s_options_form" class="s_options_form">
	<input type="hidden" name="action" id="action" value="s_save_slider" />
	<table class="widefat s_slider s_options">
		<thead>
			<tr>
				<th>Settings</th>
				<th>Description</th>
				<th>Controls</th>
			</tr>
		</thead>

		<tbody>

<?php

	$image_set=get_option($this->image_name);
	$link_set=get_option($this->link_name);
	$desc_set=get_option($this->desc_name);
	$title_set=get_option($this->title_name);

	$slider_empty=false;

	if($image_set==false or count($image_set)==0)
		$slider_empty=true;

	if($slider_empty): //if there are no options associated with the slider, display one input box

?>

			<tr>
				<td>
					<label>Image URL:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[image][]" />
					<div class="clearfix"></div>

					<label>Link:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[link][]" />
					<div class="clearfix"></div>

					<label>Title:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[title][]" />
					<div class="clearfix"></div>

				</td>

				<td>
					<textarea name="<?php echo $this->slider_name; ?>[desc][]" /></textarea>
				</td>

				<td>
					<input type="button" class="button add-above" value="Add above" /><br />
					<input type="button" class="button add-below" value="Add below" /><br />
					<input type="button" class="button-primary delete-item" value="Delete item" /><br />
				</td>
			</tr>
<?php
	else: //Otherwise=>if there is atleast one slider image, display the set of images and links

		$count=0;
		foreach($image_set as $item_image):
			$item_link=$link_set[$count];
			$item_description=$desc_set[$count];
			$item_title=$title_set[$count];
			$count++;

?>
			<tr>
				<td
					<label>Image URL:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[image][]" value="<?php echo esc_html(stripslashes($item_image)); ?>" />
					<div class="clearfix"></div>

					<label>Link:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[link][]" value="<?php echo esc_html(stripslashes($item_link)); ?>"/>
					<div class="clearfix"></div>

					<label>Title:</label>
					<input type="text" name="<?php echo $this->slider_name; ?>[title][]" value="<?php echo esc_html(stripslashes($item_title)); ?>" />
					<div class="clearfix"></div>

				</td>

				<td>
					<textarea name="<?php echo $this->slider_name; ?>[desc][]" /><?php echo stripslashes($item_description); ?></textarea>
				</td>

				<td>
					<input type="button" class="button add-above" value="Add above" /><br />
					<input type="button" class="button add-below" value="Add below" /><br />
					<input type="button" class="button-primary delete-item" value="Delete item" /><br />
				</td>
			</tr>

<?php
		endforeach;

	endif;
?>
		</tbody>
	</table>

	<input type="submit" class="button" id="final_submit" name="final_submit" value="Save changes" />
</form>
<?php

	}





	function save_options(){

		if (isset($_POST['action']) && $_POST['action'] == 's_save_slider' ) {
							$posted_slider=$_POST[$this->slider_name];
							$posted_images=$posted_slider['image'];
							$posted_links=$posted_slider['link'];
							$posted_desc=$posted_slider['desc'];
							$posted_titles=$posted_slider['title'];

							update_option($this->image_name, $posted_images);
							update_option($this->link_name, $posted_links);
							update_option($this->desc_name, $posted_desc);;
							update_option($this->title_name, $posted_titles);

					echo '<div id="message" class="updated fade"><p><strong>'.S_THEME.' settings saved.</strong></p></div>';

		}

	}




}//end class
?>