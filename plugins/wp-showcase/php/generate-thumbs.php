<?php
// Gallery
if($options['gallery_layout'] != 'slider') {
	$layout = 'layout-default';
	if ($options['gallery_layout']) {
		$layout = 'layout-'. str_replace('_', '-', $options['gallery_layout']);
	}
	
	do_action('wp_showcase_before_gallery');
	$output .= '<ul class="wp-showcase-gallery '. $layout .'">';
	foreach($attachments as $attachment ){
		$image = $attachment['thumbnail'];
		$image_full = $attachment['full'];
		$meta = $attachment['meta'];
		$thumb_src = $image;
		
		if (isset($options['dim_x']) && isset($options['dim_y']) && $options['dim_x'] && $options['dim_y'] ) {
			$resized_image = $this->resize_image($attachment['id'], '', $options['dim_x'], $options['dim_y'], true );
			if (is_wp_error($resized_image) ) {
				$output .= '<p>Error: '. $resized_image->get_error_message() .'</p>';
			} else {
				$thumb_src = $resized_image['url'];
			}
		}
		
		$a_class = '';
		$a_rel = 'wp-showcase-'. $id;
		
		$lb_options = get_option('showcase_settings' );
		if (!isset($lb_options['lightbox-config']) ) {
			$lb_options['lightbox-config'] = 'default';
		}
		if ($lb_options['lightbox-config'] != 'default') {
			$a_class = $lb_options['custom-class'];
			$a_rel = $lb_options['custom-rel'];
		}
		
		$output .= '<li><a href="'. $image_full .'" rel="'.$a_rel.'" class="'.$a_class.'" title="';
		if (isset($meta['wp_showcase']['caption']) && $meta['wp_showcase']['caption'] ) {
			$output .= $meta['wp_showcase']['caption'] .'<br />';
		}
		if (isset($meta['wp_showcase']['link']) && $meta['wp_showcase']['link'] ) {
			$output .= $meta['wp_showcase']['link'];
		}
		if ($layout == 'layout-default') {
			$output .= '"><img src="'. $thumb_src .'"';
		} else {
			$output .= '"><img src="'. $image_full .'"';
		}
		if (isset($meta['wp_showcase']['alt']) && $meta['wp_showcase']['alt'] ) {
			$output .= ' alt="'. $meta['wp_showcase']['alt'] .'"';
		}
		$output .= ' /></a>';
		if (isset($options['show_thumb_caption']) && $options['show_thumb_caption'] == 'on' && isset($meta['wp_showcase']['caption']) && $meta['wp_showcase']['caption'] != '') {
			$output .= '<p class="thumb-caption">'.$meta['wp_showcase']['caption'].'</p>';
		}
		
		if ($layout == 'layout-full-data') {
			$image_shutter_speed = isset($meta['image_meta']['shutter_speed']) ? $meta['image_meta']['shutter_speed'] : 0;
			if ($image_shutter_speed > 0) {
				if ((1 / $image_shutter_speed) > 1) {
					if ((number_format((1 / $image_shutter_speed), 1)) == 1.3
						or number_format((1 / $image_shutter_speed), 1) == 1.5
						or number_format((1 / $image_shutter_speed), 1) == 1.6
						or number_format((1 / $image_shutter_speed), 1) == 2.5) {
						$pshutter = '1/' . number_format((1 / $image_shutter_speed), 1, '.', '') .' '.__('second');
					} else {
						$pshutter = '1/' . number_format((1 / $image_shutter_speed), 0, '.', '') .' '.__('second');
					}
				} else {
					$pshutter = $image_shutter_speed .' '.__('seconds');
				}
			}
			
			do_action('wp_showcase_before_exif');
			$output .= '<div class="exif">';
			if (isset($meta['image_meta']['created_timestamp']) && $meta['image_meta']['created_timestamp']) {
				$output .= '<p class="date-taken"><span>'. __('Date Taken:', 'showcase') .'</span> '. date('d M Y', $meta['image_meta']['created_timestamp']) .'</p>';
			}
			if (isset($meta['image_meta']['camera']) && $meta['image_meta']['camera']) {
				$output .= '<p class="camera"><span>'. __('Camera:', 'showcase') .'</span> '. $meta['image_meta']['camera'] .'</p>';
			}
			if (isset($meta['image_meta']['focal_length']) && $meta['image_meta']['focal_length']) {
				$output .= '<p class="focal-length"><span>'. __('Focal Length:', 'showcase') .'</span> '. $meta['image_meta']['focal_length'] .'mm</p>';
			}
			if (isset($meta['image_meta']['aperture']) && $meta['image_meta']['aperture']) {
				$output .= '<p class="aperture"><span>'. __('Aperture:', 'showcase') .'</span> f/'. $meta['image_meta']['aperture'] .'</p>';
			}
			if (isset($meta['image_meta']['iso']) && $meta['image_meta']['iso']) {
				$output .= '<p class="iso"><span>'. __('ISO:', 'showcase') .'</span> ' . $meta['image_meta']['iso'] .'</p>';
			}
			if (isset($meta['image_meta']['shutter_speed']) && $meta['image_meta']['shutter_speed']) {
				$output .= '<p class="shutter-speed"><span>'. __('Shutter Speed:', 'showcase') .'</span> '. $pshutter .'</p>';
			}
			$output .= '</div>';
			do_action('wp_showcase_after_exif');
		}
		$output .= '</li>';
	}
	$output .= '</ul>';
	do_action('wp_showcase_after_gallery');
}
?>