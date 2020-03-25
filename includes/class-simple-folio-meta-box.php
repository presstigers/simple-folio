<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Folio_Meta_Box Class
 *
 * This file is used to define add or save meta box of Simple Folio. 
 *  
 * @link       http://presstigers.com
 * @since      1.0.0
 * 
 * @package    Simple_Folio
 * @subpackage Simple_Folio/includes
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Meta_Box
{

    /**
     * The ID of this plugin.
     *
     * @since   1.0.0
     * @access  protected
     * @var     array   $simple_folio_postmeta
     */
    protected $simple_folio_postmeta;

    /**
     * Initialize the class and set its properties.
     *         
     * @since   1.0.0
     */
    public function __construct() {
        global $post, $post_id;

        // Creating Meta Box on Add New Simple Folio Item Page
        $this->simple_folio_postmeta = array(
            'id' => 'simple_folio_metabox',
            'title' => __('More Item Details', 'simple-folio'),
            'context' => 'normal',
            'screen' => 'simple_folio_item',
            'priority' => 'high',
            'context' => 'normal',
            'callback' => 'simple_folio_item_output',
            'show_names' => TRUE,
            'closed' => FALSE,
        );

        // Add Hook into the 'admin_menu' Action
        add_action('add_meta_boxes', array($this, 'simple_folio_item_create_meta_box'));

        // Add Hook into the 'save_post()' Action
        add_action('save_post_simple_folio_item', array($this, 'simple_folio_save_item'));
    }

    /**
     * Getter of simple_folio_item meta box.
     *
     * @since   1.0.0
     */
    public function simple_folio_item_get_postmeta() {
        return $this->simple_folio_postmeta;
    }

    /**
     * Create Meta Box
     *
     * @since   1.0.0 
     */
    public function simple_folio_item_create_meta_box() {
        $cpt_post_meta = self::simple_folio_item_get_postmeta();
        add_meta_box($cpt_post_meta['id'], $cpt_post_meta['title'], array($this, $cpt_post_meta['callback']), $cpt_post_meta['screen'], $cpt_post_meta['context'], $cpt_post_meta['priority']);
    }

    /**
     * Meta Box Output
     *
     * @since   1.0.0 
     * 
     * @param   object  $post   Post Object
     */
    public function simple_folio_item_output($post) {

        // Add a nonce field so we can check it for later.
        wp_nonce_field('simple_folio_item_meta_box', 'simple_folio_item_meta_box_nonce');

        $link_value = get_post_meta($post->ID, '_simple_folio_item_link', TRUE);
        $client_name_value = get_post_meta($post->ID, '_simple_folio_item_client_name', TRUE);
        
        $html  = '<label>Link: </label><br/><input type="text" name="simple_folio_item_link" value="' . $link_value . '"/><br/><br/>';
        $html .= '<label>Client Name: </label><br/><input type="text" name="simple_folio_item_client_name" value="' . $client_name_value . '"/>';
        
        echo $html;
    }

    /**
     * Save Meta Box.
     *
     * @since   1.0.0
     */
    public function simple_folio_save_item() {
        global $post;

        // Check Nonce Field
        if (!isset($_POST['simple_folio_item_meta_box_nonce'])) {
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['simple_folio_item_meta_box_nonce'], 'simple_folio_item_meta_box')) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
		
        if( isset( $_POST['simple_folio_item_link'] ) )  
        update_post_meta( $post->ID, '_simple_folio_item_link', $_POST['simple_folio_item_link'] );
        
        if( isset( $_POST['simple_folio_item_client_name'] ) )  
        update_post_meta( $post->ID, '_simple_folio_item_client_name', $_POST['simple_folio_item_client_name'] );
    }
}
new Simple_Folio_Meta_Box();