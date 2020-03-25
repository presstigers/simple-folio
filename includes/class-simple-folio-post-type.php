<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Folio_Post_Type Class
 * 
 * This class is used to create custom post type for soc slider.
 *
 * @link       http://www.presstigers.com
 * @since      1.0.0
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/includes
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Post_Type
{
    /**
     * The structure_object that holds structure class object.
     *
     * @since    1.0.0
     * @access   private
     * @var      Simple_Folio_Shortcode    $structure_object   Hold the structure class object.
     */
    private $structure_object;
    
    /**
     * Initialize the class and set it's properties.
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        // Create Class Object
        $this->structure_object = new Simple_Folio_Template_Structure;
        
        // Add Hook into the 'init()' Action
        add_action('init', array($this, 'simple_folio_init'));

        // Add Hook into the 'init()' action
        add_action('admin_init', array($this, 'simple_folio_admin_init'));
    }

    /**
     * WordPress core launches at 'init' points
     *          
     * @since   1.0.0
     */
    public function simple_folio_init() {
        $this->create_post_type();

        // Flush Rewrite Rules 
        flush_rewrite_rules();
        
        // Add Filter into the Single Page Template
        add_filter( 'single_template', array( $this, 'simple_folio_single_item' ) );
    }

    /**
     * Create_post_type function.
     *
     * @since   1.0.0
     */
    public function create_post_type()
    {
        if (post_type_exists("simple_folio_item"))
            return;

        /**
         * Post Type -> Simple Folio Item
         */
        $singular = __('Item', 'simple-folio');
        $plural = __('Items', 'simple-folio');

        $rewrite = array(
            'slug' => _x('simple-folio', 'simple-folio permalink - resave permalinks after changing this', 'simple-folio'),
            'with_front' => FALSE,
            'feeds' => FALSE,
            'pages' => FALSE,
            'hierarchical' => FALSE,
        );

        // Post Type -> Simple Folio Item -> Labels
        $folio_labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => __('Simple Folio', 'simple-folio'),
            'all_items' => sprintf(__('%s', 'simple-folio'), $plural),
            'add_new' => sprintf(__('New %s', 'simple-folio'), $singular),
            'add_new_item' => sprintf(__('New %s', 'simple-folio'), $singular),
            'edit' => __('Edit', 'simple-folio'),
            'edit_item' => sprintf(__('Edit %s', 'simple-folio'), $singular),
            'new_item' => sprintf(__('New %s', 'simple-folio'), $singular),
            'view' => sprintf(__('View %s', 'simple-folio'), $singular),
            'view_item' => sprintf(__('View %s', 'simple-folio'), $singular),
            'search_items' => sprintf(__('Search %s', 'simple-folio'), $plural),
            'not_found' => sprintf(__('No %s found', 'simple-folio'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'simple-folio'), $plural),
            'parent' => sprintf(__('Parent %s', 'simple-folio'), $singular)
        );

        // Post Type -> Simple Folio Item -> Arguments
        $folio_args = array(
            'labels' => $folio_labels,
            'description' => sprintf(__('This is where you can create and manage %s.', 'simple-folio'), $plural),
            'public' => TRUE,
            'menu_icon' => 'dashicons-portfolio',
            'show_ui' => TRUE,
            'capability_type' => 'post',
            'map_meta_cap' => TRUE,
            'publicly_queryable' => TRUE,
            'exclude_from_search' => TRUE,
            'hierarchical' => FALSE,
            'rewrite' => $rewrite,
            'query_var' => TRUE,
            'can_export' => TRUE,
            'supports' => array('title', 'editor', 'thumbnail'),
            'has_archive' => TRUE,
            'show_in_nav_menus' => TRUE,
        );

        // Register Simple Folio Item Post Type
        register_post_type("simple_folio_item", apply_filters("register_post_type_simple_folio", $folio_args));
        
        /**
         * Post Type -> Simple Folio Item
         * Post Type -> Simple Folio Item -> Taxonomy -> Group
         */
        $singular = __('Group', 'simple-folio');
        $plural = __('Groups', 'simple-folio');

        // Post Type -> Simple Folio Item -> Taxonomy -> Group -> Labels
        $folio_labels_group = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => ucwords($plural),
            'all_items'             => sprintf(__('All %s', 'simple-folio'), $plural),
            'edit_item'             => sprintf(__('Edit %s', 'simple-folio'), $singular),
            'update_item'           => sprintf(__('Update %s', 'simple-folio'), $singular),
            'add_new_item'          => sprintf(__('Add %s', 'simple-folio'), $singular),
            'new_item_name'         => sprintf(__('New %s Name', 'simple-folio'), $singular),
            'parent_item'           => sprintf(__('Parent %s', 'simple-folio'), $singular),
            'parent_item_colon'     => sprintf(__('Parent %s:', 'simple-folio'), $singular),
            'add_or_remove_items'   => __('Add or remove', 'simple-folio'),
            'choose_from_most_used' => __('Choose from most used', 'simple-folio'),
            'search_items'          => sprintf(__('Search %s', 'simple-folio'), $plural),
            'popular_items'         => sprintf(__('Popular %s', 'simple-folio'), $plural),
        );

        // Post Type -> Simple Folio Item -> Taxonomy -> Group -> Arguments
        $folio_args_group = array(
            'label'              => $plural,
            'labels'             => $folio_labels_group,
            'public'             => TRUE,
            'show_in_quick_edit' => TRUE,
            'rewrite'            => TRUE,
            'show_admin_column'  => TRUE,
            'hierarchical'       => TRUE,
            'query_var'          => TRUE,
            'rewrite'            => array(
                'slug' => 'simple-folio-group',
                'hierarchical' => TRUE,
                'with_front' => FALSE
            ),
        );
        
        // Register Folio Group Taxonomy
        register_taxonomy( "simple_folio_group", array('simple_folio_item'), $folio_args_group );
        
        /**
         * Post Type -> Simple Folio Item
         * Post Type -> Simple Folio Item -> Taxonomy -> Tags
         */
        $singular = __('Filter Tag', 'simple-folio');
        $plural = __('Filter Tags', 'simple-folio');

        // Post Type -> Simple Folio Item -> Taxonomy -> Tags -> Labels
        $folio_labels_tag = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => ucwords($plural),
            'all_items'             => sprintf(__('All %s', 'simple-folio'), $plural),
            'edit_item'             => sprintf(__('Edit %s', 'simple-folio'), $singular),
            'update_item'           => sprintf(__('Update %s', 'simple-folio'), $singular),
            'add_new_item'          => sprintf(__('Add %s', 'simple-folio'), $singular),
            'new_item_name'         => sprintf(__('New %s Name', 'simple-folio'), $singular),
            'parent_item'           => sprintf(__('Parent %s', 'simple-folio'), $singular),
            'parent_item_colon'     => sprintf(__('Parent %s:', 'simple-folio'), $singular),
            'add_or_remove_items'   => __('Add or remove', 'simple-folio'),
            'choose_from_most_used' => __('Choose from most used', 'simple-folio'),
            'search_items'          => sprintf(__('Search %s', 'simple-folio'), $plural),
            'popular_items'         => sprintf(__('Popular %s', 'simple-folio'), $plural),
        );

        // Post Type -> Simple Folio Item -> Taxonomy -> Tags -> Arguments
        $folio_args_tag = array(
            'label'              => $plural,
            'labels'             => $folio_labels_tag,
            'public'             => TRUE,
            'show_in_quick_edit' => TRUE,
            'rewrite'            => TRUE,
            'show_admin_column'  => TRUE,
            'hierarchical'       => FALSE,
            'query_var'          => TRUE,
            'rewrite'            => array(
                'slug' => 'simple-folio-tag',
                'hierarchical' => TRUE,
                'with_front' => FALSE
            ),
        );
        
        // Register Simple Folio Item Tags Taxonomy
        register_taxonomy( "simple_folio_tag", array('simple_folio_item'), $folio_args_tag );
    }

    /**
     * A function hook that the WP core launches at 'admin_init' points
     * 
     * @since   1.0.0
     */
    public function simple_folio_admin_init()
    {
        // Hook - Shortcode -> Add New Column
        add_filter('manage_edit-simple_folio_item_columns', array($this, 'simple_folio_item_list_columns'));

        // Hook - Shortcode -> Add Value to New Column
        add_action('manage_simple_folio_item_posts_custom_column', array($this, 'custom_simple_folio_item_columns_value'));
    }

    /**
     * Add custom column for 'SOF' shortcode 
     *
     * @since   1.0.0
     * @param   $columns   Custom Column 
     *  
     * @return  $columns   Custom Column
     */
    public function simple_folio_item_list_columns($columns)
    {
        $columns = array(
            'cb'        => '<input type="checkbox"/>',
            'sfo_thumb'  => __('Portfolio Image', 'simple-folio'),
            'title'     => __('Title', 'simple-folio'),
            'taxonomy-simple_folio_group' =>  __('Groups', 'simple-folio'),
            'taxonomy-simple_folio_tag' =>  __('Filter Tags', 'simple-folio'),
            'date' => __('Date', 'simple-folio'),
        );
        return $columns;
    }

    /**
     * Add custom column's value
     *
     * @since   1.0.0
     * @param   $name   custom column's name
     *  
     * @return  void
     */
    public function custom_simple_folio_item_columns_value($name)
    {
        switch ($name) {
            case 'sfo_thumb':
                if( function_exists('the_post_thumbnail') ) {
                    echo the_post_thumbnail( array(50, 50) );
                }
            break;
        }
    }
    
    /**
     * Show portfolio content on single
     *
     * @since   1.0.0
     * 
     * @param   string  $content
     */
    /**
     * To load single portfolio page.
     *
     * @since   1.0.0
     * 
     * @param   string  $single_template    Default Single Page Path.        	
     * @return  string  $single_template    Plugin Single Page Path.
     */
    function simple_folio_single_item( $single_template )
    {
        global $post;
        $simpleFolioObj = New Simple_Folio();
        if ('simple_folio_item' === $post->post_type) {
            $single_template =  ( !file_exists(get_stylesheet_directory() . '/simple_folio/single-simple-folio.php') ) ?
                               $simpleFolioObj->get_plugin_directory() . '/templates/single-simple-folio.php' :
                               get_stylesheet_directory() . '/simple_folio/single-simple-folio.php';
        }
        return $single_template;
    }
}
new Simple_Folio_Post_Type();