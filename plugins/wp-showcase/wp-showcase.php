<?php
/*
Plugin Name: Showcase
Plugin URI: http://showcase.dev7studios.com
Description: The ultimate WordPress gallery plugin
Version: 1.4
Author: Dev7studios
Author URI: http://dev7studios.com
*/
if (!session_id() ) {
    session_start();
}

$wordpress_showcase = new WordpressShowcase();
class WordpressShowcase {
    public $plugin_folder = 'wp-showcase';
    
    function __construct()
    {
        add_action('init', array(&$this, 'init'));
        add_filter('post_updated_messages', array(&$this, 'updated_messages'));
        add_action('manage_edit-showcase_gallery_columns', array(&$this, 'edit_columns'));
        add_action('manage_showcase_gallery_posts_custom_column', array(&$this, 'custom_columns'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_print_styles', array(&$this, 'admin_print_styles'));
        add_action('admin_print_scripts', array(&$this, 'admin_print_scripts'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles_scripts'));
        add_action('save_post', array(&$this, 'save_post'));
        add_action('wp_ajax_showcase_load_images', array(&$this, 'load_images'));
        add_action('wp_ajax_showcase_upload', array(&$this, 'upload_image'));
        add_action('wp_ajax_showcase_load_meta', array(&$this, 'load_image_meta'));
        add_action('wp_ajax_showcase_edit', array(&$this, 'edit_image'));
        add_action('wp_ajax_showcase_remove', array(&$this, 'remove_image'));
        add_action('wp_ajax_showcase_order_save', array(&$this, 'save_order'));
        add_shortcode('showcase', array(&$this, 'shortcode'));
        
        //Image Sources
        add_action('wp_ajax_showcase_add_image_source', array(&$this, 'add_image_source'));
        
        load_plugin_textdomain('showcase', false, dirname(plugin_basename(__FILE__ ) ) .'/lang/' );
        
        require_once('wp-updates-plugin.php');
        new WPUpdatesPluginUpdater('http://wp-updates.com/api/1/plugin', 117, plugin_basename(__FILE__) );
    }
    
    function get_labels()
    {
        $labels = array('name' => _x('Showcase Gallery', 'post type general name' ),
        'singular_name' => _x('Showcase Gallery', 'post type singular name' ),
        'add_new' => __('Add New', 'showcase' ),
        'add_new_item' => __('Add New Showcase Gallery', 'showcase' ),
        'edit_item' => __('Edit Showcase Gallery', 'showcase' ),
        'new_item' => __('New Showcase Gallery', 'showcase' ),
        'view_item' => __('View Showcase Gallery', 'showcase' ),
        'search_items' => __('Search Showcase Galleries', 'showcase' ),
        'not_found' =>  __('No Showcase Galleries found', 'showcase' ),
        'not_found_in_trash' => __('No Showcase Galleries found in Trash', 'showcase' ),
        'menu_name' => 'Showcase',
        );
        return apply_filters('wp_showcase_labels', $labels );
    }
    
    function init()
    {
        $labels = $this->get_labels();
        register_post_type('showcase_gallery',
        array('labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'menu_position' => 100,
        'supports' => array('title'),
        'menu_icon' => plugins_url($this->plugin_folder . '/images/favicon.png')
        )
        );
        
        if (current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true' ) {
            add_filter('mce_external_plugins', array(&$this, 'mce_add_plugin'));
            add_filter('mce_buttons_2', array(&$this, 'mce_register_button'));
        }
        
    }
    
    function updated_messages($messages )
    {
        global $post, $post_ID;
        
        $showcase_messages = array(0 => '', // Unused. Messages start at index 1.
        1 => __('Showcase Gallery updated.', 'showcase'),
        2 => __('Custom field updated.', 'showcase'),
        3 => __('Custom field deleted.', 'showcase'),
        4 => __('Showcase Gallery updated.', 'showcase'),
        /* translators: %s: date and time of the revision */
        5 => isset($_GET['revision']) ? sprintf(__('Showcase Gallery restored to revision from %s', 'showcase'), wp_post_revision_title((int) $_GET['revision'], false ) ) :
        false,
        6 => __('Showcase Gallery published.', 'showcase'),
        7 => __('Showcase Gallery saved.', 'showcase'),
        8 => __('Showcase Gallery submitted.', 'showcase'),
        9 => sprintf(__('Showcase Gallery scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Gallery</a>', 'showcase'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'showcase' ), strtotime($post->post_date ) ), esc_url(get_permalink($post_ID) ) ),
        10 => __('Showcase Gallery draft updated.', 'showcase')
        );
        
        $messages['showcase_gallery'] = apply_filters('wp_showcase_messages', $showcase_messages );
        return $messages;
    }
    
    function edit_columns($columns )
    {
        $columns = array('cb' => '<input type="checkbox" />',
        'title' => __('Title', 'showcase' ),
        'shortcode' => __('Shortcode', 'showcase' ),
        'author' => __('Author', 'showcase' ),
        'images' => __('Images', 'showcase' ),
        'date' => __('Date', 'showcase' )
        );
        return $columns;
    }
    
    function custom_columns($column )
    {
        global $post;
        if ($post->post_type != 'showcase_gallery') {
            return;
        }
        switch ($column ) {
        case 'images':
            $limit = 5;
            if (isset($_GET['mode']) && $_GET['mode'] == 'excerpt') {
                $limit = 20;
            }
            $attachments = $this->get_gallery_images($post->ID, $limit );
            if ($attachments ) {
                echo '<ul class="showcase-thumbs">';
                foreach($attachments as $attachment ){
                    $image = $attachment['thumbnail'];
                    echo '<li><img src="'. $image .'" alt="" style="width:32px;height:32px;" /></li>';
                }
                echo '</ul>';
            }
            break;
        case 'shortcode':
            echo '<span class="showcase-code">[showcase id="'. $post->ID .'"]</span>';
            if ($post->post_name != '') {
                echo '<br /><span class="showcase-code">[showcase slug="'. $post->post_name .'"]</span>';
            }
            break;
        }
    }
    
    function enqueue_styles_scripts()
    {
        if (!is_admin() ) {
            $options = get_option('showcase_settings' );
            
            //Colorbox specific
            if (!isset($options['lightbox-config']) ) {
                $options['lightbox-config'] = 'default';
            }
            
            if ($options['lightbox-config'] == 'default') {
                if (!isset($options['theme']) ) {
                    $options['theme'] = 'dark';
                }
                
                // Styles
                wp_enqueue_style('colorbox', plugins_url($this->plugin_folder . '/scripts/colorbox/colorbox.css'), array(), '1.3' );
                if ($options['theme'] ) {
                    wp_enqueue_style('colorbox-theme', plugins_url($this->plugin_folder . '/scripts/colorbox/themes/'. $options['theme'] .'.css'), array(), '1.0' );
                }
                // Scripts
                wp_register_script('colorbox', plugins_url($this->plugin_folder . '/scripts/colorbox/jquery.colorbox-min.js'), array('jquery'), '1.3' );
                wp_enqueue_script('colorbox' );
            }
            
            // Styles
            wp_enqueue_style('flexslider', plugins_url($this->plugin_folder . '/scripts/flexslider/flexslider.css'), array(), '1.8' );
            wp_enqueue_style('wp-showcase', plugins_url($this->plugin_folder . '/styles/wp-showcase.css'), array(), '1.0' );
            
            // Scripts
            wp_register_script('flexslider', plugins_url($this->plugin_folder . '/scripts/flexslider/jquery.flexslider-min.js'), array('jquery'), '1.8' );
            wp_enqueue_script('flexslider' );
            wp_register_script('wp-showcase', plugins_url($this->plugin_folder . '/scripts/wp-showcase.js'), array('colorbox','flexslider','jquery'), '1.0' );
            wp_enqueue_script('wp-showcase' );
            wp_enqueue_script('jquery' );
        }
    }
    
    function admin_menu()
    {
        add_submenu_page('edit.php?post_type=showcase_gallery',
        'Settings',
        'Settings',
        'manage_options',
        'showcase-settings',
        array(&$this, 'settings_page')
        );
    }
    
    function settings_page_header()
    {
        $header = 'Showcase Settings';
        return apply_filters('wp_showcase_settings_page_header', $header);
    }
    
    function settings_page()
    {
        ?>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h2><?php echo $this->settings_page_header() ?></h2>
        <form action="options.php" method="post">
        <?php settings_fields('showcase-settings');
        ?>
        <?php do_settings_sections('showcase-settings');
        ?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'showcase' );
        ?>"></p>
        </form>
        </div>
        <?php
    }
    
    function setting_lightbox_config()
    {
        $options = get_option('showcase_settings' );
        if (!isset($options['lightbox-config']) ) {
            $options['lightbox-config'] = 'default';
        }
        
        echo '<select name="showcase_settings[lightbox-config]">
<option value="default"'. (($options['lightbox-config'] == 'default') ? ' selected="selected"' : '') .'>Default</option>
<option value="custom"'. (($options['lightbox-config'] == 'custom') ? ' selected="selected"' : '') .'>Custom</option>
</select>';
    }
    
    function setting_theme()
    {
        $options = get_option('showcase_settings' );
        if (!isset($options['theme']) ) {
            $options['theme'] = 'dark';
        }
        
        echo '<select class="lb-default" name="showcase_settings[theme]">
<option value="dark"'. (($options['theme'] == 'dark') ? ' selected="selected"' : '') .'>Dark</option>
<option value="light"'. (($options['theme'] == 'light') ? ' selected="selected"' : '') .'>Light</option>
<option value=""'. (($options['theme'] == '') ? ' selected="selected"' : '') .'>Custom (no CSS applied)</option>
</select>';
    }
    
    function setting_custom_rel()
    {
        $options = get_option('showcase_settings' );
        if (!isset($options['custom-rel']) ) {
            $options['custom-rel'] = '';
        }
        
        echo '<input class="lb-custom" type="text" value="'.$options['custom-rel'].'" name="showcase_settings[custom-rel]">';
        
    }
    
    function setting_custom_class()
    {
        $options = get_option('showcase_settings' );
        if (!isset($options['custom-class']) ) {
            $options['custom-class'] = '';
        }
        
        echo '<input class="lb-custom" type="text" value="'.$options['custom-class'].'" name="showcase_settings[custom-class]">';
        
    }
    
    function setting_image_sources()
    {
        $sources = $this->get_image_sources();
        $html = '';
        if ($sources) {
            $html .= '<input type="hidden" name="showcase_settings[sources]" value="sources">';
            foreach($sources as $key => $source) {
                if ($key != 'gallery' && $key != 'upload') {
                    $html .= '<input type="checkbox" id="'. $key . '" name="showcase_settings['. $key .']" checked="checked" /> ';
                    $html .= $source .'<br/>';
                }
            }
        }
        if ($html == '') {
            $html = 'No connected image sources';
        }
        echo $html;
    }
    
    function save_image_sources($input)
    {
        if ($input && is_array($input) && isset($input['sources'])) {
            $options = get_option('showcase_settings' );
            $saved_sources = $options['image_sources'];
            $sources = array('upload' => 'File Upload',
            'gallery' => 'WordPress Gallery'
            );
            foreach($input as $key => $value) {
                if (array_key_exists($key, $saved_sources)) {
                    $sources[strtolower($key)] = ucfirst($key);
                    unset($input[$key]);
                }
            }
            $input['image_sources'] = $sources;
            foreach($saved_sources as $key => $value) {
                if (!array_key_exists($key, $sources)) {
                    unset($input[$key .'-settings']);
                }
            }
            unset($input['sources']);
        }
        return $input;
    }
    
    function setting_custom_roles()
    {
        $options = get_option('showcase_settings' );
        global $wp_roles;
        $role_names = $wp_roles->get_names();
        if (!isset($options['custom-roles']) ) {
            $defaults = array();
            foreach($role_names as $key => $value ) {
                if ($key != 'administrator') {
                    $defaults[] = $key;
                }
            }
            $options['custom-roles'] = $defaults;
        }
        $roles = array();
        foreach($role_names as $key => $value ) {
            if ($key != 'administrator') {
                $roles[ $key ] = get_role($key );
                $roles[ $key ]->display_name = $value;
                if (in_array($roles[ $key ]->name, $options['custom-roles'] )) {
                    $roles[ $key ]->selected = true;
                }
            }
        }
        echo '<input type="hidden" name="showcase_settings[roles]" value="roles">';
        foreach($roles as $role ):
        echo '<input type="checkbox" id="'. $role->name . '" name="showcase_settings['. $role->name .']" '. (isset($role->selected) && ($role->selected)  ? ' checked="checked"' : '') .'/> ';
        echo $role->display_name .'<br/>';
        endforeach;
    }
    
    function save_custom_roles($input)
    {
        if ($input && is_array($input) && isset($input['roles'])) {
            $custom_roles = array();
            global $wp_roles;
            $role_names = $wp_roles->get_names();
            $roles = array();
            foreach($role_names as $key => $value ) {
                if ($key != 'administrator') {
                    $roles[ $key ] = get_role($key );
                    $roles[ $key ]->display_name = $value;
                }
            }
            foreach($input as $key => $value) {
                if (array_key_exists($key, $roles)) {
                    $custom_roles[] = $key;
                    unset($input[$key]);
                }
            }
            unset($input['roles']);
            $custom_roles[] = 'administrator';
            $input['custom-roles'] = $custom_roles;
        }
        return $input;
    }
    
    function settings_validate($input )
    {
        $options = get_option('showcase_settings' );
        if ($options && is_array($options)) {
            foreach($options as $key => $option) {
                if (!array_key_exists($key, $input)) {
                    $input[$key] = $option;
                }
            }
        }
        
        //custom saving for checkboxes
        $input = $this->save_custom_roles($input);
        $input = $this->save_image_sources($input);
        
        return $input;
    }
    
    function remove_showcase_menu()
    {
        $options = get_option('showcase_settings' );
        if (isset($options['custom-roles'])) {
            $custom_roles = $options['custom-roles'];
            if ($custom_roles) {
                $allowed_roles = apply_filters('showcase_allowed_roles', $custom_roles );
                global $current_user;
                $user_roles = $current_user->roles;
                $show = false;
                foreach($allowed_roles as $role) {
                    if (in_array($role , $user_roles)) {
                        $show = true;
                    }
                }
                if (!$show) {
                    remove_menu_page('edit.php?post_type=showcase_gallery' );
                }
            }
        }
    }
    
    function settings_intro()
    {
        echo apply_filters('wp_showcase_settings_intro', '');
    }
    
    function admin_init()
    {
        add_meta_box('showcase_upload_box', __('Upload Images', 'showcase' ), array(&$this, 'meta_box_upload'), 'showcase_gallery', 'normal' );
        add_meta_box('showcase_settings_box', __('Settings', 'showcase' ), array(&$this, 'meta_box_settings'), 'showcase_gallery', 'normal' );
        add_meta_box('showcase_shortcode_box', __('Using this Gallery', 'showcase' ), array(&$this, 'meta_box_shortcode'), 'showcase_gallery', 'side' );
        add_meta_box('showcase_usefullinks_box', __('Useful Links', 'showcase' ), array(&$this, 'meta_box_usefullinks'), 'showcase_gallery', 'side' );
        
        register_setting('showcase-settings', 'showcase_settings', array(&$this, 'settings_validate') );
        add_settings_section('showcase-settings', '', array(&$this, 'settings_intro'), 'showcase-settings' );
        
        add_settings_field('lightbox-config', __('Lightbox Config', 'zilla' ), array(&$this, 'setting_lightbox_config'), 'showcase-settings', 'showcase-settings' );
        add_settings_field('theme', __('Lightbox Theme', 'zilla' ), array(&$this, 'setting_theme'), 'showcase-settings', 'showcase-settings' );
        add_settings_field('custom-rel', __('Custom Rel Attribute', 'zilla' ), array(&$this, 'setting_custom_rel'), 'showcase-settings', 'showcase-settings' );
        add_settings_field('custom-class', __('Custom Class Attribute', 'zilla' ), array(&$this, 'setting_custom_class'), 'showcase-settings', 'showcase-settings' );
        add_settings_field('custom-roles', __('Showcase Management Roles', 'zilla' ), array(&$this, 'setting_custom_roles'), 'showcase-settings', 'showcase-settings' );
        add_settings_field('image-sources', __('Connected Image Sources', 'zilla' ), array(&$this, 'setting_image_sources'), 'showcase-settings', 'showcase-settings' );
        
        $this->remove_showcase_menu();
        $this->image_sources_header();
    }
    
    function admin_print_styles()
    {
        global $post;
        
        if (isset($post->post_type) && $post->post_type == 'showcase_gallery' || (isset($_GET['page']) && $_GET['page'] == 'showcase-settings')) {
            wp_enqueue_style('wp-showcase-admin', plugins_url($this->plugin_folder . '/styles/wp-showcase-admin.css'));
        }
    }
    
    function admin_print_scripts()
    {
        global $post;
        
        if ((isset($post->post_type) && $post->post_type == 'showcase_gallery') || (isset($_GET['page']) && $_GET['page'] == 'showcase-settings')) {
            wp_register_script('showcase_plupload', plugins_url($this->plugin_folder . '/scripts/plupload/plupload.full.js'), array('jquery') );
            wp_enqueue_script('showcase_plupload' );
            wp_register_script('jquery-simplemodal', plugins_url($this->plugin_folder . '/scripts/jquery.simplemodal.1.4.1.min.js'), array('jquery') );
            wp_enqueue_script('jquery-simplemodal' );
            wp_register_script('wp-showcase-admin', plugins_url($this->plugin_folder . '/scripts/showcase-admin.js'), array('showcase_plupload','jquery','jquery-ui-sortable') );
            wp_enqueue_script('wp-showcase-admin' );
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-sortable');
        }
        
        // Galleries list for TinyMCE dropdown
        $galleries = get_posts(array('post_type' => 'showcase_gallery', 'posts_per_page' => -1) );
        $list = array();
        foreach($galleries as $gallery ){
            $list[] = array('id' => $gallery->ID,
            'name' => $gallery->post_title
            );
        }
        wp_localize_script('jquery', 'wp_showcase', array('post_id' => (isset($post->ID)) ? $post->ID : '',
        'plugin_folder' => plugins_url($this->plugin_folder . '/'),
        'nonce' => wp_create_nonce('wp_showcase'),
        'galleries' => json_encode($list)
        ));
    }
    
    function image_sources_header()
    {
        
        if (isset($_GET['post']) && isset($_GET['type'])) {
            global $post;
            $post = get_post($_GET['post']);
            if ($post->post_type != 'showcase_gallery') {
                return;
            }
            
            $options = get_option('showcase_settings' );
            $source = $_GET['type'];
            $request_code = $_GET['oauth_verifier'];
            
            if (isset($_SESSION[$source .'_oauth_token']) && isset($_SESSION[$source .'_oauth_token_secret'])) {
                
                $auth_token = $_SESSION[$source .'_oauth_token'];
                $auth_token_secret = $_SESSION[$source .'_oauth_token_secret'];
                
                (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://";
                $callback =  $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
                $callback = substr($callback, 0, strpos($callback, $source) + strlen($source));
                
                $source_dir = dirname(__FILE__) .'/includes/sources/';
                include_once($source_dir . $source .'.php');
                
                $var = 'showcase_source_'. $source;
                $obj = new $var($auth_token, $auth_token_secret);
                $token = $obj->getAccessToken($request_code, $callback);
                
                $showcase_settings = get_post_meta($_GET['post'], 'showcase_settings', true );
                $showcase_settings['source'] = $source;
                update_post_meta($_GET['post'], 'showcase_settings', $showcase_settings);
                
                $options[$source .'-settings'] = array('access-token' => $token);
                $image_sources = $options['image_sources'];
                $image_sources[$source] = ucfirst($source);
                $options['image_sources'] = $image_sources;
                update_option('showcase_settings', $options);
                
                if (isset($_SESSION[$source .'_oauth_token'])) {
                    unset($_SESSION[$source .'_oauth_token']);
                }
                if (isset($_SESSION[$source .'_oauth_token_secret'])) {
                    unset($_SESSION[$source .'_oauth_token_secret']);
                }
            }
            
        }
        
    }
    
    function add_image_source()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        
        $options = get_option('showcase_settings' );
        $options['image_sources'] = array_merge($options['image_sources'], array($_POST['source'] => ucfirst($_POST['source'])));
        update_option('showcase_settings', $options);
        
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    
    function get_image_sources()
    {
        $options = get_option('showcase_settings' );
        if (!isset($options['image_sources']) ) {
            $defaults = array('upload' => 'File Upload',
            'gallery' => 'WordPress Gallery'
            );
            $options['image_sources'] = $defaults;
            update_option('showcase_settings', $options);
            
        }
        return $options['image_sources'];
    }
    
    function get_all_sources()
    {
        $options = get_option('showcase_settings' );
        $image_sources = $options['image_sources'];
        (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://";
        $callback =  $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (strpos($callback,'post=') === false) {
            global $post;
            $callback .= '&post='. $post->ID;
        }
        $source_dir = glob(dirname(__FILE__) .'/includes/sources/*');
        $sources = array();
        if ($source_dir) {
            foreach($source_dir as $dir) {
                if (!array_key_exists(basename($dir, ".php"), $image_sources) ) {
                    include_once($dir);
                    $source = basename($dir, ".php");
                    $var = 'showcase_source_'. $source;
                    $obj = new $var();
                    $sources[$source] = $obj->get_authorise_url($callback, $source);
                }
            }
        }
        return $sources;
    }
    
    function get_image_source_details($image_source, $selected_source)
    {
        if ($image_source != 'gallery' && $image_source != 'upload') {
            
            global $post;
            $options = get_post_meta($post->ID, 'showcase_settings', true );
            
            $source_dir = dirname(__FILE__) .'/includes/sources/';
            include_once($source_dir . $image_source .'.php');
            $var = 'showcase_source_'. $image_source;
            $obj = new $var();
            $settings = $obj->show_details();
            
            $display = ($image_source != $selected_source) ? 'style="display: none;"' : '';
            $header = '<tr valign="top" id="showcase_type_'. $image_source .'" class="image_source" '. $display .'>';
            $header .= '<th scope="row"> '. ucfirst($image_source) .' settings</th>';
            $header .= '<td>';
            $body   =   '<label>';
            $body   .=  '   <select name="showcase_settings['. $image_source .'_type]" class="image_source_type" >';
            
            foreach($settings as $method => $value) {
                $selected = ($this->default_val($options, $image_source .'_type', '')) == $method ?  'selected="selected"' : '';
                $body   .=  '<option '. $selected .' value="'. $method .'">'. $value['name'].'</option>';
            }
            
            $body   .=  '   </select>';
            $body   .=  '   <span class="description">Choose the type of images from '. ucfirst($image_source) .' </span>';
            $body   .=  '<br/><br/>';
            $body   .=  '</label>';
            
            foreach($settings as $method => $value) {
                if (isset($value['param_type'])) {
                    $body   .=  '<div id="'. $image_source .'_param_'. $method .'" class="image_source_param">';
                    if ($value['param_type'] == 'text') {
                        $body   .=  '   <input id="'. $image_source .'_'. $method .'" type="text" value="'. $this->default_val($options, $image_source .'_' . $method, '') .'" name="showcase_settings['. $image_source .'_'. $method .']">';
                    }
                    
                    if ($value['param_type'] == 'select') {
                        $body   .=  '   <input id="'. $image_source .'_'. $method .'" type="text" value="'. $this->default_val($options, $image_source .'_' . $method, '') .'" name="showcase_settings['. $image_source .'_'. $method .']">';
                    }
                    $body   .=  (isset($value['param_desc'])) ? '   <span class="description">'. $value['param_desc'] .'</span>' : '';
                    $body   .=  '</div>';
                }
            }
            $footer = '</td></tr>';
            $html =  $header.$body.$footer;
            return $html;
        }
    }
    
    function image_source_default()
    {
        return apply_filters('wp_showcase_image_source_default', 'upload' );
    }
    
    function meta_box_settings()
    {
        global $post;
        $options = get_post_meta($post->ID, 'showcase_settings', true );
        wp_nonce_field(plugin_basename(__FILE__ ), 'showcase_noncename' );
        ?>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Image Source', 'showcase');
        ?></th>
        <td><select name="showcase_settings[source]">
        <?php
        $image_source = $this->default_val($options, 'source', $this->image_source_default());
        $images_sources = $this->get_image_sources();
        if ($images_sources) {
            foreach($images_sources as $source => $value) {
                echo '<option value="'. $source .'"';
                if ($image_source == $source) {
                    echo ' selected="selected"';
                }
                echo '>'. __($value, 'showcase') .'</option>';
            }
        } else {
            echo '<option value="none">'.__('No Sources', 'showcase').'</option>';
        }
        ?>
        </select> <a id="add_image_source" href="#" class="button">Add Image Source</a><br />
        <span class="description"><?php _e('Choose an image source', 'showcase');
        ?></span></td>
        </tr>
        <div id="new-image-sources">
        <p><strong>Add Image Source</strong></p>
        <?php
        if ($post->post_status == 'auto-draft' ) {
            echo 'You can add new sources once the gallery has been published';
        } else {
            $sources = $this->get_all_sources();
            if ($sources) {
                foreach($sources as $key => $value) echo '<a href="'. $value .'" class="button image-source" id="'. $key .'">'. ucfirst($key) .'</a> ';
            } else {
                echo 'No more sources available';
            }
        }
        ?>
        </div>
        <tr valign="top" id="showcase_type_gallery">
        <th scope="row">- <?php _e('Post', 'showcase');
        ?></th>
        <td><select name="showcase_settings[source_gallery]">
        <?php
        $posts = get_posts(array('numberposts' => -1) );
        foreach($posts as $post_item){
            echo '<option value="'. $post_item->ID .'"';
            if ($this->default_val($options, 'source_gallery') == $post_item->ID) {
                echo ' selected="selected"';
            }
            echo '>'. $post_item->post_title .'</option>';
        }
        ?>
        </select><br />
        <span class="description"><?php _e('Select the post gallery you want to use', 'showcase');
        ?></span></td>
        </tr>
        <?php
        $seleced_source = $this->default_val($options, 'source', $this->image_source_default());
        $images_sources = $this->get_image_sources();
        if ($images_sources) {
            foreach($images_sources as $source => $value) {
                if ($source != 'gallery' && $source != 'upload') {
                    echo $this->get_image_source_details($source, $seleced_source);
                }
            }
        }
        ?>
        <tr valign="top" class="showcase_non_manual">
        <th scope="row">- <?php _e('Number of Images', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[number_images]" value="<?php echo $this->default_val($options, 'number_images', '');
        ?>" class="regular-text" /><br />
        <span class="description"><?php _e('The number of images to use in the gallery. Leave blank for all images. External sources default to 20.', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Gallery Layout', 'showcase');
        ?></th>
        <td><label><select name="showcase_settings[gallery_layout]">
        <option value=""<?php if ($this->default_val($options, 'gallery_layout', '') == '') {
            echo ' selected="selected"';
        }
        ?>>Thumbnail Grid</option>
        <option value="full"<?php if ($this->default_val($options, 'gallery_layout', '') == 'full') {
            echo ' selected="selected"';
        }
        ?>>Full Size Images</option>
        <option value="full_data"<?php if ($this->default_val($options, 'gallery_layout', '') == 'full_data') {
            echo ' selected="selected"';
        }
        ?>>Full Size Images + Exif Data</option>
        <option value="slider"<?php if ($this->default_val($options, 'gallery_layout', '') == 'slider') {
            echo ' selected="selected"';
        }
        ?>>Image Slider Only</option>
        </select>
        <span class="description"><?php _e('Choose a layout for the gallery', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Enable Lightbox', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[enable_lightbox]" value="off" />
        <label><input type="checkbox" name="showcase_settings[enable_lightbox]" value="on"<?php if ($this->default_val($options, 'enable_lightbox', 'on') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Show the lightbox when a user clicks on an image', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" id="enable_image_slider">
        <th scope="row"><?php _e('Enable Image Slider', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[show_slideshow]" value="off" />
        <label><input type="checkbox" name="showcase_settings[show_slideshow]" value="on"<?php if ($this->default_val($options, 'show_slideshow', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Show an image slider above the gallery', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Custom Thumbnail Size', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[dim_x]" value="<?php echo $this->default_val($options, 'dim_x', '');
        ?>" /> x
        <input type="text" name="showcase_settings[dim_y]" value="<?php echo $this->default_val($options, 'dim_y', '');
        ?>" /><br />
        <span class="description"><?php _e('(Size in px) Override the default thumbnail size in the <a href="'. admin_url('options-media.php') .'">Media Settings</a>', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Enable Thumbnail Captions', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[show_thumb_caption]" value="off" />
        <label><input type="checkbox" name="showcase_settings[show_thumb_caption]" value="on"<?php if ($this->default_val($options, 'show_thumb_caption', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Show image captions below thumbnails    ', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Animation Effect', 'showcase');
        ?></th>
        <td><select name="showcase_settings[slider_animation]">
        <option value="fade"<?php if ($this->default_val($options, 'slider_animation', '') == 'fade') {
            echo ' selected="selected"';
        }
        ?>>Fade</option>
        <option value="slide"<?php if ($this->default_val($options, 'slider_animation', '') == 'slide') {
            echo ' selected="selected"';
        }
        ?>>Slide</option>
        </select>
        <span class="description"><?php _e('Select the animation effect of the slider', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Direction', 'showcase');
        ?></th>
        <td><select name="showcase_settings[slider_direction]">
        <option value="horizontal"<?php if ($this->default_val($options, 'slider_direction', '') == 'horizontal') {
            echo ' selected="selected"';
        }
        ?>>Horizontal</option>
        <option value="vertical"<?php if ($this->default_val($options, 'slider_direction', '') == 'vertical') {
            echo ' selected="selected"';
        }
        ?>>Vertical</option>
        </select>
        <span class="description"><?php _e('Select the sliding direction', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Animation Duration', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_animate_duration]" value="<?php echo $this->default_val($options, 'slider_animate_duration', '300'); ?>" /><br />
        <span class="description"><?php _e('Set the speed of animations, in milliseconds', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Slideshow', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_slideshow]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_slideshow]" value="on"<?php if ($this->default_val($options, 'slider_slideshow', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Animate slider automatically', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Slideshow Speed', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_slideshow_speed]" value="<?php echo $this->default_val($options, 'slider_slideshow_speed', '7000'); ?>" /><br />
        <span class="description"><?php _e('Set the speed of the slideshow cycling, in milliseconds', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Direction Nav', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_direction_nav]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_direction_nav]" value="on"<?php if ($this->default_val($options, 'slider_direction_nav', 'on') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Create navigation for previous/next navigation', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Control Nav', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_control_nav]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_control_nav]" value="on"<?php if ($this->default_val($options, 'slider_control_nav', 'on') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Create navigation for paging control of each slide', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Keyboard Nav', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_keyboard_nav]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_keyboard_nav]" value="on"<?php if ($this->default_val($options, 'slider_keyboard_nav', 'on') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Allow slider navigating via keyboard left/right keys', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Previous Text', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_prev_text]" value="<?php echo $this->default_val($options, 'slider_prev_text', 'Previous'); ?>" /><br />
        <span class="description"><?php _e('Set the text for the "previous" directionNav item', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Next Text', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_next_text]" value="<?php echo $this->default_val($options, 'slider_next_text', 'Next'); ?>" /><br />
        <span class="description"><?php _e('Set the text for the "next" directionNav item', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Pause Play', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_pause_play]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_pause_play]" value="on"<?php if ($this->default_val($options, 'slider_pause_play', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Creates pause/play dynamic element', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Pause Text', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_pause_text]" value="<?php echo $this->default_val($options, 'slider_pause_text', 'Pause'); ?>" /><br />
        <span class="description"><?php _e('Set the text for the "pause" pausePlay item', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Play Text', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_play_text]" value="<?php echo $this->default_val($options, 'slider_play_text', 'Play'); ?>" /><br />
        <span class="description"><?php _e('Set the text for the "play" pausePlay item', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Randomise', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_random]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_random]" value="on"<?php if ($this->default_val($options, 'slider_random', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Randomise slide order', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Start Slide', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_settings[slider_start_slide]" value="<?php echo $this->default_val($options, 'slider_start_slide', '0'); ?>" /><br />
        <span class="description"><?php _e('The slide that the slider should start on. 0 is the first slide', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Pause on Action', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_pause_action]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_pause_action]" value="on"<?php if ($this->default_val($options, 'slider_pause_action', 'on') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Pause the slideshow when interacting with control elements', 'showcase');
        ?></span></label></td>
        </tr>
        <tr valign="top" class="slider_settings">
        <th scope="row"><?php _e('Slider Pause on Hover', 'showcase');
        ?></th>
        <td><input type="hidden" name="showcase_settings[slider_pause_hover]" value="off" />
        <label><input type="checkbox" name="showcase_settings[slider_pause_hover]" value="on"<?php if ($this->default_val($options, 'slider_pause_hover', 'off') == 'on') {
            echo ' checked="checked"';
        }
        ?> />
        <span class="description"><?php _e('Pause the slideshow when hovering over slider, then resume when no longer hovering', 'showcase');
        ?></span></label></td>
        </tr>
        </table>
        <?php
    }
    
    function default_val($options, $value, $default = '' )
    {
        if (!isset($options[$value]) ) {
            return $default;
        } else {
            return $options[$value];
        }
    }
    
    function save_post($post_id )
    {
        if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if (!isset($_POST['showcase_noncename']) || !wp_verify_nonce($_POST['showcase_noncename'], plugin_basename(__FILE__ ) ) )
        return;
        
        // Check permissions
        if('page' == $_POST['post_type'] ) {
            if (!current_user_can('edit_page', $post_id ) ) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id ) ) {
                return;
            }
        }
        
        // Good to go
        $settings = $_POST['showcase_settings'];
        
        if (!is_numeric($settings['dim_x']) || $settings['dim_x'] <= 0 ) {
            $settings['dim_x'] = '';
        }
        if (!is_numeric($settings['dim_y']) || $settings['dim_y'] <= 0 ) {
            $settings['dim_y'] = '';
        }
        
        update_post_meta($post_id, 'showcase_settings', $settings );
    }
    
    function meta_box_upload()
    {
        global $post;
        ?>
        <ul id="showcase-images"></ul>
        <div id="showcase-file-uploader">
        <ul id="filelist"></ul>
        <a id="pickfiles" href="#" class="button">Select files</a> <a id="uploadfiles" href="#" class="button">Upload files</a>
        </div>
        <div id="showcase-edit-image">
        <p><strong>Edit Image</strong></p>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Image Caption', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_meta_caption" id="showcase_meta_caption" value="" class="regular-text" /><br />
        <span class="description"><?php _e('e.g. Example caption', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Image Link', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_meta_link" id="showcase_meta_link" value="" class="regular-text" /><br />
        <span class="description"><?php _e('e.g. http://www.example.com', 'showcase');
        ?></span></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Alt Text', 'showcase');
        ?></th>
        <td><input type="text" name="showcase_meta_alt" id="showcase_meta_alt" value="" class="regular-text" /><br />
        <span class="description"><?php _e('e.g. An image title', 'showcase');
        ?></span></td>
        </tr>
        </table>
        <p class="submit"><input type="button" name="showcase_meta_submit" id="showcase_meta_submit" class="button-primary" value="<?php _e('Save Changes', 'showcase' );
        ?>"></p>
        </div>
        <?php
    }
    
    function load_images()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' ) || !isset($_POST['source'])) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        $response['images'] = array();
        $images = array();
        
        $number_images = (isset($_POST['number_images']) && $_POST['number_images'] != '') ? $_POST['number_images'] : -1;
        
        if ($_POST['source'] == 'upload') {
            $args = array('orderby'        => 'menu_order ID',
            'order'          => 'ASC',
            'post_type'      => 'attachment',
            'post_parent'    => $_POST['id'],
            'post_mime_type' => 'image',
            'post_status'    => null,
            'numberposts'    => -1
            );
            $attachments = get_posts($args );
            if ($attachments ) {
                foreach($attachments as $attachment ){
                    $image = wp_get_attachment_image_src($attachment->ID, 'thumbnail' );
                    $images[] = array('id' => $attachment->ID,
                    'src' => $image[0]
                    );
                }
            }
        } else if ($_POST['source'] == 'gallery') {
            $args = array('orderby'        => 'menu_order ID',
            'order'          => 'ASC',
            'post_type'      => 'attachment',
            'post_parent'    => $_POST['gallery'],
            'post_mime_type' => 'image',
            'post_status'    => null,
            'numberposts'    => $number_images
            );
            $attachments = get_posts($args );
            if ($attachments ) {
                foreach($attachments as $attachment ){
                    $image = wp_get_attachment_image_src($attachment->ID, 'thumbnail' );
                    $images[] = array('id' => $attachment->ID,
                    'src' => $image[0]
                    );
                }
            }
        } else {
            $image_source =  $_POST['source'];
            $options = get_option('showcase_settings' );
            if (isset($options[$image_source .'-settings'])) {
                $source_settings = $options[$image_source .'-settings'];
                $access_token = $source_settings['access-token'];
                $source_dir = dirname(__FILE__) .'/includes/sources/';
                include_once($source_dir . $image_source .'.php');
                $var = 'showcase_source_'. $image_source;
                $obj = new $var($access_token['oauth_token'], $access_token['oauth_token_secret']);
                $method = $_POST['method'];
                $count = $_POST['number_images'];
                $params = array();
                if (isset($_POST['param'])) {
                    $params[] = $_POST['param'];
                }
                if ($count != '') {
                    $params[] = $count;
                }
                $source_images = call_user_func_array(array($obj, $method), $params);
                if ($source_images) {
                    foreach($source_images as $image){
                        $images[] = array('id' => $image['id'],
                        'src' => $image['thumbnail']
                        );
                    }
                    
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Failed to get '. $image_source .' feed';
                }
            }
        }
        
        $response['images'] = $images;
        
        echo json_encode($response);
        die;
    }
    
    function upload_image()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $wp_uploads = wp_upload_dir();
        if (isset($wp_uploads['error']) && $wp_uploads['error'] != false ) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open upload directory"}, "id" : "id"}');
        }
        
        //@set_time_limit(5 * 60);
        $targetDir = $wp_uploads['path'];
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        
        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . "/" . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
            
            $count = 1;
            while (file_exists($targetDir . "/" . $fileName_a . '_' . $count . $fileName_b)) {
                $count++;
            }
            
            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }
        
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        
        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        }
        
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $contentType = $_SERVER["CONTENT_TYPE"];
        }
        
        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($targetDir . "/" . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");
                    
                    if ($in) {
                        while ($buff = fread($in, 4096)) {
                            fwrite($out, $buff);
                        }
                    } else {
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    }
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                }
            } else {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
        } else {
            // Open temp file
            $out = fopen($targetDir . "/" . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");
                
                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                } else {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
                
                fclose($in);
                fclose($out);
            } else {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
        }
        
        // Attach image to the post
        $uploadfile = $targetDir . "/" . $fileName;
        $wp_filetype = wp_check_filetype(basename($uploadfile), null );
        $attachment = array('post_mime_type' => $wp_filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploadfile)),
        'post_content' => '',
        'post_status' => 'inherit',
        'menu_order' => 1
        );
        $attach_id = wp_insert_attachment($attachment, $uploadfile, $_GET['post_id'] );
        $attach_data = wp_generate_attachment_metadata($attach_id, $uploadfile );
        wp_update_attachment_metadata($attach_id,  $attach_data );
        
        $response['error'] = false;
        $response['message'] = '';
        $response['upload_path'] = $wp_uploads['url'];
        if (!empty($attach_data['sizes'])) {
            $response['file'] = $attach_data['sizes']['thumbnail']['file'];
            $response['file_medium'] = $attach_data['sizes']['medium']['file'];
        } else {
            $response['file'] = basename($attach_data['file']);
            $response['file_medium'] = basename($attach_data['file']);
        }
        $response['file_full'] = basename($attach_data['file']);
        $response['attachment_id'] = $attach_id;
        $response['success'] = true;
        
        echo htmlspecialchars(json_encode($response), ENT_NOQUOTES );
        die;
    }
    
    function load_image_meta()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        
        $meta = wp_get_attachment_metadata($_POST['id']);
        if (isset($meta['wp_showcase']['caption'])) {
            $response['caption'] = $meta['wp_showcase']['caption'];
        }
        if (isset($meta['wp_showcase']['link'])) {
            $response['link'] = $meta['wp_showcase']['link'];
        }
        if (isset($meta['wp_showcase']['alt'])) {
            $response['alt'] = $meta['wp_showcase']['alt'];
        }
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function edit_image()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        
        $meta = wp_get_attachment_metadata($_POST['id']);
        $meta['wp_showcase'] = array('caption' => strip_tags($_POST['caption']),
        'link' => strip_tags($_POST['link']),
        'alt' => strip_tags($_POST['alt'])
        );
        wp_update_attachment_metadata($_POST['id'], $meta );
        
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function remove_image()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        
        wp_delete_attachment($_POST['data'] );
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function save_order()
    {
        // Verify this came from the our screen and with proper authorization
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_showcase' )) {
            return 0;
        }
        
        $response['error'] = false;
        $response['message'] = '';
        
        $i = 0;
        $data = $_POST['attachment'];
        foreach($data as $attach_id ){
            $attachment = array();
            $attachment['ID'] = $attach_id;
            $attachment['menu_order'] = $i;
            wp_update_post($attachment );
            $i++;
        }
        
        $response['data'] = $data;
        $response['message'] = 'success';
        
        echo json_encode($response);
        die;
    }
    
    function meta_box_shortcode()
    {
        global $post;
        
        echo '<p>'. __('To use this slider in your posts or pages use the following shortcode:', 'showcase') .'</p>
<p><span class="showcase-code">[showcase id="'. $post->ID .'"]</span>';
        if ($post->post_name != '') {
            echo ' or</p><p><span class="showcase-code">[showcase slug="'. $post->post_name .'"]</span>';
        }
        echo '</p>';
    }
    
    
    function get_usefullinks()
    {
        $links = array(array('label' => __('Website:', 'showcase'),
        'url'   => 'http://showcase.dev7studios.com',
        'title' => 'Showcase'
        ),
        array('label' => __('Created by:', 'showcase'),
        'url'   => 'http://dev7studios.com',
        'title' => 'Dev7studios'
        ),
        array('label' => __('Support:', 'showcase'),
        'url'   => 'http://support.dev7studios.com/discussions',
        'title' => 'Support Forums'
        )
        );
        return apply_filters('wp_showcase_useful_links', $links );
    }
    
    function meta_box_usefullinks()
    {
        $links = $this->get_usefullinks();
        $html = '';
        foreach($links as $link) {
            $html .= '<p>'.$link['label'].' <a href="'.$link['url'].'" target="_blank">'.$link['title'].'</a></p>';
        }
        echo $html;
    }
    
    function shortcode($atts )
    {
        extract(shortcode_atts(array('id' => 0,
        'slug' => ''
        ), $atts ) );
        
        if (!$id && !$slug) {
            return __('Invalid Gallery', 'showcase');
        }
        
        if (!$id) {
            $gallery = get_page_by_path($slug, OBJECT, 'showcase_gallery' );
            if ($gallery) {
                $id = $gallery->ID;
            } else {
                return __('Invalid Gallery Slug', 'showcase');
            }
        }
        
        $output = '';
        $options = get_post_meta($id, 'showcase_settings', true );
        $attachments = $this->get_gallery_images($id );
        
        if ($attachments ) {
            
            do_action('wp_showcase_before_showcase');
            $output .= '<div id="wp-showcase-'. $id .'" class="wp-showcase'. (($options['enable_lightbox'] == 'on') ? ' enable-lightbox' : '') .'">';
            // Slideshow
            if ($options['show_slideshow'] == 'on' || $options['gallery_layout'] == 'slider') {
                do_action('wp_showcase_before_slider');
                $output .= '<div class="flexslider cf"><ul class="slides">';
                foreach($attachments as $attachment ){
                    $image_full = $attachment['full'];
                    $meta = $attachment['meta'];
                    
                    $output .= '<li><img src="'. $image_full .'"';
                    if (isset($meta['wp_showcase']['alt']) && $meta['wp_showcase']['alt'] ) {
                        $output .= ' alt="'. $meta['wp_showcase']['alt'] .'"';
                    }
                    $output .= ' />';
                    if ((isset($meta['wp_showcase']['caption']) && $meta['wp_showcase']['caption']) ||
                    (isset($meta['wp_showcase']['link']) && $meta['wp_showcase']['link']) ) $output .= '<p class="flex-caption">';
                    if(isset($meta['wp_showcase']['caption']) && $meta['wp_showcase']['caption'] ) {
                        $output .= $meta['wp_showcase']['caption'] .' ';
                    }
                    if (isset($meta['wp_showcase']['link']) && $meta['wp_showcase']['link'] ) {
                        $output .= '<a href="'. $meta['wp_showcase']['link'] .'">'. $meta['wp_showcase']['link'] .'</a>';
                    }
                    if ((isset($meta['wp_showcase']['caption']) && $meta['wp_showcase']['caption']) ||
                    (isset($meta['wp_showcase']['link']) && $meta['wp_showcase']['link']) ) $output .= '</p>';
                    $output .= '</li>';
                }
                $output .= '</ul></div>';
                do_action('wp_showcase_after_slider');
            }
            
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
            
            $output .= '</div>';
            do_action('wp_showcase_after_showcase');
            
            // Flexslider JS
            if ($options['gallery_layout'] == 'slider' || $options['show_slideshow'] == 'on') {
                
                $output .= '<script type="text/javascript">' ."\n";
                $output .= 'jQuery(window).load(function(){' ."\n";
                $output .= '    jQuery("#wp-showcase-'. $id .' .flexslider").flexslider({' ."\n";
                if (isset($options['slider_animation'])) {
                    $output .= '        animation: "'. $options['slider_animation'].'",' ."\n";
                }
                if (isset($options['slider_direction'])) {
                    $output .= '        direction: "'.$options['slider_direction'].'",' ."\n";
                }
                if (isset($options['slider_animate_duration'])) {
                    $output .= '        animationSpeed: '.$options['slider_animate_duration'].',' ."\n";
                }
                if (isset($options['slider_slideshow'])) {
                    $output .= '        slideshow: '.(($options['slider_slideshow'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_slideshow_speed'])) {
                    $output .= '        slideshowSpeed: '.$options['slider_slideshow_speed'].',' ."\n";
                }
                if (isset($options['slider_direction_nav'])) {
                    $output .= '        directionNav: '.(($options['slider_direction_nav'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_control_nav'])) {
                    $output .= '         controlNav: '.(($options['slider_control_nav'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_keyboard_nav'])) {
                    $output .= '         keyboard: '.(($options['slider_keyboard_nav'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_prev_text'])) {
                    $output .= '        prevText: "'.$options['slider_prev_text'].'",' ."\n";
                }
                if (isset($options['slider_next_text'])) {
                    $output .= '        nextText: "'.$options['slider_next_text'].'",' ."\n";
                }
                if (isset($options['slider_pause_play'])) {
                    $output .= '         pausePlay: '.(($options['slider_pause_play'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_pause_text'])) {
                    $output .= '        pauseText: "'.$options['slider_pause_text'].'",' ."\n";
                }
                if (isset($options['slider_play_text'])) {
                    $output .= '        playText: "'.$options['slider_play_text'].'",' ."\n";
                }
                if (isset($options['slider_random'])) {
                    $output .= '         randomize: '.(($options['slider_random'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_start_slide'])) {
                    $output .= '        slideToStart: '.$options['slider_start_slide'].',' ."\n";
                }
                if (isset($options['slider_pause_action'])) {
                    $output .= '         pauseOnAction: '.(($options['slider_pause_action'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                if (isset($options['slider_pause_hover'])) {
                    $output .= '         pauseOnHover: '.(($options['slider_pause_hover'] == 'on') ? 'true' : 'false').',' ."\n";
                }
                $output .= '    });' ."\n";
                $output .= '});' ."\n";
                $output .= '</script>' ."\n";
                
            }
            
        }
        
        return $output;
    }
    
    function get_gallery_images($post_id, $limit = -1 )
    {
        $options = get_post_meta($post_id, 'showcase_settings', true );
        if (!$options) {
            return;
        }
        $showcase_options = get_option('showcase_settings' );
        $sources = $showcase_options['image_sources'];
        
        if (!array_key_exists($options['source'], $sources)) {
            $image_source = $this->image_source_default();
        } else {
            $image_source = $this->default_val($options, 'source', $this->image_source_default());
        }
        
        $images = array();
        
        if ($image_source == 'upload') {
            $args = array('orderby'        => 'menu_order ID',
            'order'          => 'ASC',
            'post_type'      => 'attachment',
            'post_parent'    => $post_id,
            'post_mime_type' => 'image',
            'post_status'    => null,
            'numberposts'    => $limit
            );
            $attachments = get_posts($args );
            
        } else if ($image_source == 'gallery') {
            $gallery = $this->default_val($options, 'source_gallery');
            $args = array('orderby'        => 'menu_order ID',
            'order'          => 'ASC',
            'post_type'      => 'attachment',
            'post_parent'    => $gallery,
            'post_mime_type' => 'image',
            'post_status'    => null,
            'numberposts'    => $limit
            );
            $attachments = get_posts($args );
        } else {
            if (isset($showcase_options[$image_source .'-settings'])) {
                $source_settings = $showcase_options[$image_source .'-settings'];
                $access_token = $source_settings['access-token'];
                $source_dir = dirname(__FILE__) .'/includes/sources/';
                include_once($source_dir . $image_source .'.php');
                $var = 'showcase_source_'. $image_source;
                $obj = new $var($access_token['oauth_token'], $access_token['oauth_token_secret']);
                if (isset($options[$image_source .'_type'])) {
                    $method = $options[$image_source .'_type'];
                    $params = array();
                    if (isset($options[$image_source .'_'. $method])) {
                        $params[] = $options[$image_source .'_'. $method];
                    }
                    $count = $this->default_val($options, 'number_images', '');
                    if ($count != '') {
                        $params[] = $count;
                    }
                    $images = call_user_func_array(array($obj, $method), $params);
                }
            }
        }
        
        if (isset($attachments)) {
            foreach($attachments as $attachment ){
                
                $full = wp_get_attachment_image_src($attachment->ID, 'full' );
                $thumb = wp_get_attachment_image_src($attachment->ID, 'thumbnail' );
                
                $images[] = array('id' => $attachment->ID,
                'full' => $full[0],
                'thumbnail' => $thumb[0],
                'meta' => wp_get_attachment_metadata($attachment->ID )
                );
            }
        }
        
        return $images;
    }
    
    function mce_add_plugin($plugin_array )
    {
        $plugin_array['showcase'] = plugins_url($this->plugin_folder . '/scripts/mce-showcase/showcase.js');
        return $plugin_array;
    }
    
    function mce_register_button($buttons )
    {
        array_push($buttons, '|', 'showcase');
        return $buttons;
    }
    
    /*
    * Resize images dynamically using wp built in functions
    * Victor Teixeira
    *
    * php 5.2+
    *
    * Example usage:
    *
    * <?php
    * $thumb = get_post_thumbnail_id();
    * $image = resize_image($thumb, '', 140, 110, true );
    * ?>
    * <img src="<?php echo $image[url];
    ?>" width="<?php echo $image[width];
    ?>" height="<?php echo $image[height];
    ?>" />
    *
    * @param int $attach_id
    * @param string $img_url
    * @param int $width
    * @param int $height
    * @param bool $crop
    * @return array
    */
    function resize_image($attach_id = null, $img_url = null, $width, $height, $crop = false )
    {
        // this is an attachment, so we have the ID
        if ($attach_id ) {
            
            $image_src = wp_get_attachment_image_src($attach_id, 'full' );
            $file_path = get_attached_file($attach_id );
            
            // this is not an attachment, let's use the image url
        } else if ($img_url ) {
            
            $file_path = parse_url($img_url );
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
            
            //$file_path = ltrim($file_path['path'], '/' );
            //$file_path = rtrim(ABSPATH, '/' ).$file_path['path'];
            
            if (!file_exists($file_path) ) {
                return new WP_Error('broke', __('File doesn\'t  exist: '. $file_path, 'showcase'));
            }
            
            $orig_size = getimagesize($file_path );
            
            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }
        
        $file_info = pathinfo($file_path );
        $extension = '.'. $file_info['extension'];
        // the image path without the extension
        $no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
        $cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
        // checking if the file size is larger than the target size
        // if it is smaller or the same size, stop right here and return
        if ($image_src[1] > $width || $image_src[2] > $height ) {
            // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
            if (file_exists($cropped_img_path ) ) {
                $cropped_img_url = str_replace(basename($image_src[0] ), basename($cropped_img_path ), $image_src[0] );
                
                $vt_image = array('url' => $cropped_img_url,
                'width' => $width,
                'height' => $height
                );
                
                return $vt_image;
            }
            // $crop = false
            if ($crop == false ) {
                
                // calculate the size proportionaly
                $proportional_size = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height );
                $resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;
                // checking if the file already exists
                if (file_exists($resized_img_path ) ) {
                    
                    $resized_img_url = str_replace(basename($image_src[0] ), basename($resized_img_path ), $image_src[0] );
                    $vt_image = array('url' => $resized_img_url,
                    'width' => $proportional_size[0],
                    'height' => $proportional_size[1]
                    );
                    
                    return $vt_image;
                }
            }
            // no cache files - let's finally resize it
            $new_img_path = image_resize($file_path, $width, $height, $crop );
            if (is_wp_error($new_img_path) ) {
                return $new_img_path;
            }
            $new_img_size = getimagesize($new_img_path );
            $new_img = str_replace(basename($image_src[0] ), basename($new_img_path ), $image_src[0] );
            // resized output
            $vt_image = array('url' => $new_img,
            'width' => $new_img_size[0],
            'height' => $new_img_size[1]
            );
            
            return $vt_image;
        }
        // default output - without resizing
        $vt_image = array('url' => $image_src[0],
        'width' => $image_src[1],
        'height' => $image_src[2]
        );
        
        return $vt_image;
    }
    
}

if (!function_exists('showcase')) {
    
    function showcase($slider)
    {
        
        $slug = '';
        $id = 0;
        
        if (is_numeric($slider)) {
            $id = $slider;
        } else {
            $slug = $slider;
        }
        
        echo do_shortcode('[showcase slug="'.$slug.'" id="'.$id.'"]');
        
    }
}

?>