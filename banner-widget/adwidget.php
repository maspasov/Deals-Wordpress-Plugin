<?php
/**
* Plugin Name: Banner Widget
* Version:     2.1.0
* Description: The easiest way to place ads in your Wordpress sidebar. Version 2.0 is a complete rewrite of the plugin
* Author:      FOS WP Team
* Author URI:  vortex.1stonlinesolutions.com/dev/WPU/
**/

/**
 * Define Plugin Name
 */
define('BANNER_WIDGET_NAME', 'banner-widget');

add_action('admin_init', array('AdWidget_Core', 'registerScripts'));
add_action('widgets_init', array('AdWidget_Core', 'registerWidgets'));

/**
 * Other Plugin Functionalities
 * - Post Expiration Plugin
 * - Offer Custom Post type
 * - Base Theme Banner Widget Class. This simplifies the creation of widgets. Extend this class when adding new widgets...
 */
require_once 'lib/plugin-updates/plugin-update-checker.php';
require_once 'lib/custom-tinymce-shortcode-button/custom-tinymce-shortcode-button.php';
require_once 'lib/post-expirator/post-expirator.php';
require_once 'lib/theme-post-type.php';
require_once 'lib/widgets.php';

/**
 * Update Plugin Checker
 */
$MyUpdateChecker = PucFactory::buildUpdateChecker(
    'http://vortex.1stonlinesolutions.com/dev/WPU/?action=get_metadata&slug=' . BANNER_WIDGET_NAME, //Metadata URL.
    __FILE__, //Full path to the main plugin file.
    BANNER_WIDGET_NAME //Plugin slug. Usually it's the same as the name of the directory.
);

function offers_init_theme() {
    wp_register_style('offers-style', plugin_dir_url(__FILE__) . 'lib/assets/css/offers.css', false, '1.0.0');
    wp_enqueue_style('offers-style');
}

add_action('wp_enqueue_scripts', 'offers_init_theme');

/**
 * This class is the core of Ad Widget
 */
class AdWidget_Core
{

    /**
     * The callback used to register the scripts
     */
    static function registerScripts()
    {

    }
    
    /**
     * The callback used to register the widget
     */
    static function registerWidgets()
    {   
        register_widget('AdWidget_BannerWidget');
    }
    
    /**
     * Get the base URL of the plugin installation
     * @return string the base URL
     */
    public static function getBaseURL()
    {   
        return (get_bloginfo('url') . '/wp-content/plugins/banner-widget/');
    }   
       
    /**
     * Sets a Wordpress option
     * @param string $name The name of the option to set
     * @param string $value The value of the option to set
     */
    public static function setOption($name, $value)
    {
        if (get_option($name) !== FALSE)
        {
            update_option($name, $value);
        }
        else
        {
            $deprecated = ' ';
            $autoload   = 'yes';
            add_option($name, $value, $deprecated, $autoload);
        }
    }

    /**
     * Gets a Wordpress option
     * @param string    $name The name of the option
     * @param mixed     $default The default value to return if one doesn't exist
     * @return string   The value if the option does exist
     */
    public static function getOption($name, $default = FALSE)
    {
        $value = get_option($name);
        if( $value !== FALSE ) return $value;
        return $default;
    }
}

/**
 * Banner Widget
 */
class AdWidget_BannerWidget extends ThemeBannerWidgetBase
{
    function AdWidget_BannerWidget()
    {
        $available_banners = get_posts('post_type=banners_and_deals&posts_per_page=-1');
        $banners_select = array();
        foreach ($available_banners as $banner) {
            $banners_select[$banner->ID] = $banner->post_title;
        }

        $widget_opts = array(
            'classname' => 'theme-widget-banners',
            'description' => __('Shows Theme Banners')
        );
        $control_ops = array(
            'width' => 250,
        );
        $this->WP_Widget('theme-widget-banners', 'Banner Widget', $widget_opts, $control_ops);
        $this->custom_fields = array(
            array(
                'name' => 'title',
                'type' => 'text',
                'title' => 'Title',
                'default' => ''
            ),
            array(
                'name' => 'banner_id',
                'type' => 'select',
                'title' => 'Select a Banner',
                'options' => $banners_select,
                'default' => false
            ),
            array(
                'name' => 'target',
                'type' => 'select',
                'title' => 'Open In New Window',
                'options' => array(false => 'No', true => 'Yes'),
                'default' => false
            ),
        );
    }

    function front_end($args, $instance)
    {
        extract($args);
        extract($instance);

        if (get_post_status($banner_id) == "publish") {

            $banner = get_post($banner_id);

            if ($title) {
                echo $before_title . $title . $after_title;
            }

            ?>
            <a href="<?php echo ( $custom_url = get_post_meta($banner_id, '_destination', true) ) ? $custom_url : get_permalink(get_option('contact_us_slug')) ?>?offer_id=<?php echo $banner_id ?>" <?php echo ( $target ) ? 'target="_blank"' : '' ?> title="<?php echo $banner->post_title ?>">

                <?php echo get_the_post_thumbnail( $banner_id, 'full', array('alt' => $banner->post_title, 'title' => $banner->post_title, 'class' => 'img_banner') ); ?>

            </a>
            <?php
        }
    }
}

if ( isset($_GET['offer_id']) && !empty($_GET['offer_id']) ) {

    add_action( "the_post", "edit_post_content", 99 );

    add_filter( "gform_pre_render", "gform_offer_populate",99 );
    add_filter( "wpcf7_form_tag", "cf7_offer_populate",99 );
}

function edit_post_content($post) {

    $offer_id = $_GET['offer_id'];
    $offer = get_post( $offer_id );

    if ( !empty($offer) ) {

        if ( !empty($offer->post_title) ) {
            $post->post_content = '<h2>'.$offer->post_title.'</h2>';
        }

        $offer_image = get_the_post_thumbnail( $offer_id, 'full', array('alt' => $offer->post_title, 'title' => $offer->post_title, 'class' => 'alignleft img_banner_contact') );
        if ( !empty($offer_image) ) {
            $post->post_content .= $offer_image;
        }
        
        if ( !empty($offer->post_content) ) {
            $post->post_content .= $offer->post_content;
            $post->post_content .= '<div class="clearfix clear cl">&nbsp;</div>';
        }

        $form = get_post_meta( $post->ID, 'form', true );
        if ( !empty($form) ) {
            $post->post_content .= $form;
        }

        if ( !empty($offer->post_excerpt) ) {
            $post->post_content .= $offer->post_excerpt;
        }
    }
}

function gform_offer_populate($form) {

    foreach($form["fields"] as &$field) { 
        if($field['allowsPrepopulate'] == '1') { 
            $field['choices'] = array( array(
                'text'       => get_the_title($_GET['offer_id']), 
                'value'      => get_the_title($_GET['offer_id']), 
                'isSelected' => true
                ) 
            );
        }
    }

    return $form;
}

function cf7_offer_populate($tag) { 

    if ( (strpos($tag['type'], 'select') !== false) && (stripos($tag['values'][0], 'service') !== false) ) {

        $tag['raw_values']  = array( get_the_title($_GET['offer_id']) );
        $tag['values']      = array( get_the_title($_GET['offer_id']) );
        $tag['labels']      = array( get_the_title($_GET['offer_id']) );
    }

    return $tag; 
}

/* Custom Deals Shortcode - nice content wrapper that takes care of the linking...  */
function deals_link_shortcode( $atts, $content = null ) {

    $deal_atts = shortcode_atts( array(
        'id' => '',
        'target' => '',
    ), $atts );

    if ($deal_atts['id'] == '') {
        return;
    }
    if ($deal_atts['target'] != '') {
        $deal_atts['target'] = 'target="' . $deal_atts['target'] . '"';
    }

    if (get_post_status($deal_atts['id']) != "publish") {
        return;
    }

    $banner = get_post($deal_atts['id']);

    if ( $custom_url = get_post_meta($deal_atts['id'], '_destination', true) ) {
        $url = $custom_url;
    } else {
        $url = get_permalink(get_option('contact_us_slug'));
    } 
    $url .= '?offer_id=' . $deal_atts['id'];

    return '<a class="fos-deal" href="' . $url . '" ' . $deal_atts['target'] . ' title="' . $banner->post_title . '">' . $content . '</a>';

}
add_shortcode( 'deals_link', 'deals_link_shortcode' );
