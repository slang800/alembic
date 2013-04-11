<?php
/*
  Plugin Name: Cloud Zoom
  Description: Integrates the Cloud Zoom jQuery plugin into WordPress.
  Version: 1.0
  Author: slang800
  Author URI: https://github.com/slang800
*/

function registerFiles() {
    wp_register_script('cloudzoom_js', plugins_url('/cloudzoom/cloudzoom.js', __FILE__), array('jquery'), '2.5.1');
    wp_register_style('cloudzoom_css', plugins_url('/cloudzoom/cloudzoom.css', __FILE__), false, '1.0.0', 'all');
}

function enqueueFiles() {
    wp_enqueue_script('cloudzoom_js');
    wp_enqueue_style('cloudzoom_css');
}
// Register Cloud Zoom JavaScript and Cloud Zoom CSS styles on initialization.
add_action('init', 'registerFiles');

// Use the registered JavaScript and CSS.
add_action('wp_enqueue_scripts', 'enqueueFiles');

// Include quickstart function into head, and
// adjust CSS to work better with default Word Press.
function quickStartAndCss() {
    $pathToBlank = plugins_url('/cloudzoom/blank.png', __FILE__);
    $pathToAjaxLoader = plugins_url('/cloudzoom/ajax-loader.gif', __FILE__);
    if (wp_script_is('cloudzoom_js', 'done')) {
        ?>
        <style>
            .cloudzoom img, img.cloudzoom {
                padding:0px !important;
                border:none !important;
            }
            .cloudzoom-blank {
                background-image:url("<?= $pathToBlank ?>");
            }
            .cloudzoom-ajax-loader {
                background-image:url("<?= $pathToAjaxLoader ?>");
            }
        </style>
        <script type="text/javascript">
            CloudZoom.quickStart();
        </script>
                    
        <?php
    }
}

add_action('wp_head', 'quickStartAndCss');
?>