<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Folio_Template_Structure Class
 * 
 * This file contains template structure.
 *
 * @link       http://www.presstigers.com
 * @since      1.0.0
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/includes
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Template_Structure
{

    /**
     * Get and include plugin template files.
     * 
     * @since   1.0.0
     * 
     * @param   mixed   $template_name
     * @param   array   $args (default: array())
     * @param   string  $template_path (default: '')
     * @param   string  $default_path (default: '')
     * @return  void
     */
    public function get_plugin_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if ($args && is_array($args))
        {
            extract($args);
        }
        include( $this->locate_plugin_template( $template_name, $template_path, $default_path ) );
    }

    /**
     * Locate plugin template and return the path for inclusion.
     *
     * This is the load order:
     *
     * - yourtheme/$template_path/$template_name
     * - yourtheme/$template_name
     * - $default_path/$template_name
     *
     * @since   1.0.0
     * 
     * @param   mixed       $template_name
     * @param   string      $template_path (default: '')
     * @param   string|bool $default_path (default: '') False to not load a default
     * @return  string      $template_name
     */
    public function locate_plugin_template($template_name, $template_path = '', $default_path = '')
    {
        // Retrieve the name of the highest priority template file that exists.
        $template = locate_template(
            array(
                trailingslashit($template_path).$template_name,
                $template_name
            )
        );

        // Get default template
        if (!$template && $default_path !== FALSE) {
            $default_path = $default_path ? $default_path : untrailingslashit( plugin_dir_path( dirname(__FILE__) ) ).'/templates/';

            if ( file_exists( trailingslashit( $default_path ) . $template_name ) )
            {
                $template = trailingslashit( $default_path ) . $template_name;
            }
        }

        // Return what we found
        return apply_filters('locate_plugin_template_'.$template_path, $template, $template_name, $template_path);
    }

    /**
     * Get plugin template part (for templates in loops).
     *
     * @since   1.0.0
     * 
     * @param   string      $slug
     * @param   string      $name (default: '')
     * @param   string      $template_path (default: '')
     * @param   string|bool $default_path (default: '') False to not load a default
     */
    function get_plugin_template_part($slug, $name = '', $template_path = '', $default_path = '')
    {
        $template = '';
        
        $template = ($name) ? $template = $this->locate_plugin_template("{$slug}-{$name}.php", $template_path, $default_path) : '';
        
        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/simple_folio/slug.php
        $template = (!$template) ? $this->locate_plugin_template("{$slug}.php", $template_path, $default_path) : '';
        
        if ($template)
        {
            // Require the template file with WordPress environment.
            load_template($template, FALSE);
        }
    }
}