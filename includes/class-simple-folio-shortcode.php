<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Folio_Shortcode Class
 * 
 * This file contains shortcode of 'simple_folio_item' post type.
 *
 * @link       http://www.presstigers.com
 * @since      1.0.0
 * @since      1.0.1 Update parameter for get_term_by
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/includes
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Shortcode {

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
     * @since    1.0.0
     */
    public function __construct() {
        // Create Class Object
        $this->structure_object = new Simple_Folio_Template_Structure;

        // adding simple_folio shortocde
        add_shortcode('simple_folio', array($this, 'simple_folio_callback'));

        // Hook - to remove any extra paragraph or break tags caused by shortcodes
        add_filter('the_content', array($this, 'simple_folio_shortcode_empty_paragraph_fix'));
    }

    /**
     * Register shortcode as [simple_folio id="my_portfolio"]
     */
    public function simple_folio_callback($atts) {
        // Shortcode Default Array
        $shortcode_args = array(
            'id' => '',
            'per_page' => '2'
        );

        // Extract User Defined Shortcode Attributes
        $shortcode_args = shortcode_atts($shortcode_args, $atts);

        // check if "id" attribute is provided
        if (!isset($shortcode_args['id'])) {
            return __('You should specify ID of portfolio, e.g. [simple_folio id="my_portfolio"]', 'simple-folio');
        }

        // return html for portfolio
        return $this->simple_folio_get_portfolio($this->simple_folio_sanitize_name($shortcode_args['id']), $shortcode_args, FALSE);
    }

    /**
     * Function to convert portfolio name to 
     * its sanitized form "my_portfolio" 
     * 	
     * @param string $name Portfolio name
     * @return string Sanitized name
     */
    function simple_folio_sanitize_name($name) {
        return str_replace('-', '_', sanitize_title($name));
    }

    /**
     * Function to get the portfolio DOM of Simple Folio post type
     * @return string HTML for portfolio
     */
    public function simple_folio_get_portfolio($portfolio_id, $shortcode_args = array()) {
        global $post;

        // get portfolio settings
        foreach (simple_folio_list_options() as $option_id) {
            $settings = simple_folio_get_option($option_id);
            if (is_array($settings) && $settings['id'] == $portfolio_id) {
                break;
            } else {
                $settings = FALSE;
            }
        }

        // show error if portfolio was not found
        if (!$settings) {
            $html = sprintf(__('There is no portfolio with ID "%s"', 'simple-folio'), $portfolio_id);
            return $html;
        }

        // query "Simple Folio Items" posts
        $tax_query = array();
        if (!empty($settings['groups'])) {
            $tax_query[] = array(
                'taxonomy' => 'simple_folio_group',
                'field' => 'term_id',
                'terms' => $settings['groups']
            );
        }

        // WP Query Arguments for "Simple Folio Items"
        $arguments = array(
            'post_type' => 'simple_folio_item',
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'posts_per_page' => -1,
        );

        $folio_query = new WP_Query($arguments);

        $html = '';
        ob_start();

        // check if query is not empty
        if ($folio_query->have_posts()) {
            // add current portfolio settings to $GLOBALS
            $GLOBALS['simple_folio_settings'] = $settings;
            $item_order = 1;
            /**
             * Template -> Wrapper
             */
            $html .= $this->structure_object->get_plugin_template('global/content-wrapper-start.php', $settings);

            // Display Simple Folio Filters
            $html .= $this->simple_folio_get_filters($settings['groups']);

            /*
             * Display Simple Folio Items
             * 
             * - Items Start Container
             * - Items
             * - Items End Container
             */

            // Items Start Container
            $html .= $this->structure_object->get_plugin_template('items/folio-start.php');


            // Items
            while ($folio_query->have_posts()) : $folio_query->the_post();

                // skip items without featured images
                if (!has_post_thumbnail()) {
                    continue;
                }

                // Get Item Group Filters
                $group_slugs = array();
                $filter_group_slugs = wp_get_object_terms($post->ID, 'simple_folio_group', 'fields=slugs');
                if ($filter_group_slugs && !is_wp_error($filter_group_slugs)) {
                    $group_slugs = array_merge($group_slugs, $filter_group_slugs);
                }

                $html .= $this->structure_object->get_plugin_template('items/folio.php', array('post_id' => $post->ID, 'item_groups' => $group_slugs, 'item_order' => $item_order));
                $item_order++;
            endwhile;


            // Items End Container
            $html .= $this->structure_object->get_plugin_template('items/folio-end.php');

            // unset portfolio settings
            unset($GLOBALS['simple_folio_settings']);
            $html .= $this->structure_object->get_plugin_template('global/content-wrapper-end.php');

            wp_localize_script('simple-folio', 'simple_folio', $settings);
        } else {
            $html = __('There is no items in this portfolio. Please go to "Simple folio->Items" to add some items', 'simple-folio');
        }
        // return or echo html
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Function to get HTML for portfolio filters
     * 
     * - Step 1: Get terms by using function "get_term_by" and save them in an array
     * - Step 2: Display Filter's DOM Get terms object by using function "get_term_by" and save them in an array
     * 
     * @param   array   $folio_item_groups  Folio Items groups as array
     * 
     * @return string HTML for filters
     */
    public function simple_folio_get_filters($folio_item_groups) {
        // check if called properly
        if (empty($folio_item_groups)) {
            return;
        }

        // Step 1: Get terms by using function "get_term_by" and save them in an array
        $filter_tags = $this->simple_folio_item_group_by_id($folio_item_groups);

        // Step 2: Display Filter's DOM Get terms object by using function "get_term_by" and save them in an array
        $this->structure_object->get_plugin_template('folio-filter.php', array('filter_tags' => $filter_tags));
    }

    public function simple_folio_item_group_by_id($folio_item_groups) {
        global $post_id;
        $filter_tags = array();
        foreach ($folio_item_groups as $folio_item_group) {
            $filter_tags[] = get_term_by('term_taxonomy_id', $folio_item_group, 'simple_folio_tag');
        }
        $filter_tags = array_filter($filter_tags);

        return $filter_tags;
    }

    /**
     * Function to get the categories of Simple Folio post type
     *
     * @param array $items Simple Folio Items posts as array
     * @return string HTML for filters
     */
    public function simple_folio_get_the_categories($id = false, $tcat = 'category') {
        $categories = get_the_terms($id, $tcat);
        if (!$categories)
            $categories = array();

        $categories = array_values($categories);

        foreach (array_keys($categories) as $key) {
            _make_cat_compat($categories[$key]);
        }

        return apply_filters('simple_folio_get_the_categories', $categories);
    }

    /**
     * Filters the content to remove any extra paragraph or break tags
     * caused by shortcodes.
     *
     * @since 1.0.0
     *
     * @param string $content  String of HTML content.
     * @return string $content Amended string of HTML content.
     */
    public function simple_folio_shortcode_empty_paragraph_fix($content) {
        $array = array(
            '<p>[' => '[',
            ']</p>' => ']',
            ']<br />' => ']'
        );
        return strtr($content, $array);
    }

}

new Simple_Folio_Shortcode();
