<?php
/*
  Plugin Name: Cloud Zoom
  Description: Integrates the Cloud Zoom jQuery plugin into WordPress.
  Version: 1.0
  Author: slang800
  Author URI: https://github.com/slang800
*/

function registerFiles() {
    wp_register_script('cloudzoom_js',S_THEME_DIR.'/plugins/cloudzoom/cloudzoom.js', array('jquery'), '2.5.1');
    wp_register_style('cloudzoom_css', S_THEME_DIR.'/plugins/cloudzoom/cloudzoom.css', false, '1.0.0', 'all');
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
    $pathToBlank = S_THEME_DIR.'/plugins/cloudzoom/blank.png';
    $pathToAjaxLoader = S_THEME_DIR.'/plugins/cloudzoom/ajax-loader.gif';
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