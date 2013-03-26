<?php
$info=array(
	'box_name' => 's-meta-box',
	'title' => 'Archive Options',
	'location' => array('post', 'portfolio')
);


$options=array(
	
array(
	"type" => "text",
	"title" => "Post image",
	"name" => "s_post_image",				
	"desc" => "Enter the URL to an image for this post. It will be resized to fit where required. You may also use the <code>Post Thumbnail</code> feature instead.",
	"default" => "" ),	
	
);

$metabox_post = new s_meta_box($info, $options);





add_action('admin_init','promo_meta_box_init');

function promo_meta_box_init() {
	add_meta_box('promo-meta',__('Page Options','promo-plugin'), 'promo_meta_box','portfolio','side','default');
	add_action('save_post','promo_save_meta_box');
}
function promo_meta_box($post,$box) {
	$promo_area = get_post_meta($post->ID,'_promo_area',true);
	$place = get_post_meta($post->ID,'_place',true);
	$price = get_post_meta($post->ID,'_price',true);
	$date = get_post_meta($post->ID,'_date',true);
	
	echo '<p>' .__('Promo box:<br>','promo-plugin'). ' <textarea style="width: 100%; min-height: 100px;"
	name="promo_area">'.$promo_area.'</textarea></p>';
	echo '<p>' .__('Place:','promo-plugin'). ' <input type="text" name="place" value="'.esc_attr($place).'"></p>';
	echo '<p>' .__('Date:','promo-plugin'). ' <input type="text" name="date" value="'.$date.'"></p>';
	echo '<p>' .__('Price:','promo-plugin'). ' <input type="text" name="price" value="'.$price.'"></p>';
	}

function promo_save_meta_box($post_id) {
	if($post->post_type == 'revision') { return; } 
	update_post_meta($post_id,'_promo_area', $_POST['promo_area']);
	update_post_meta($post_id,'_place', esc_attr($_POST['place']));
	update_post_meta($post_id,'_date', $_POST['date']);
	update_post_meta($post_id,'_price', $_POST['price']);
	} 


?>