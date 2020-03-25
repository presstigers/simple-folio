<?php
/**
 * The public-facing functionality of the plugin.
 * 
 * @link       http://www.presstigers.com
 * @since      1.0.0
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/public
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        /**
         * The class is responsible for defining the post type 'simple_folio'.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-folio-post-type.php';
        
        /**
         * The class is responsible for defining the Shortcode 'simple_folio'.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-folio-shortcode.php';
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        //Enqueuing Google Font (raleway)
        wp_enqueue_style( $this->plugin_name . 'raleway-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic', false );
        
        //Enqueuing styles
        wp_enqueue_style( 'font-awesome' , plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), '4.6.1', 'all' );
        wp_enqueue_style( $this->plugin_name . '-front-end', plugin_dir_url( __FILE__ ) . 'css/simple-folio-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simple-folio-public.js', array( 'jquery' ), '1.12.3', TRUE );
    }
}