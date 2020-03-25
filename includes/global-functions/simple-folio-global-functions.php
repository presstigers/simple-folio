<?php
/**
 * This file contains global functions used on settings page.
 *
 * @link       http://www.presstigers.com
 * @since      1.0.0
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/includes/global-functions
 * @author     PressTigers <support@presstigers.com>
 */

global $folio_defaults;
$folio_defaults = array(
    '_id' => '<empty>', // ID, internal use only (*)
    'id' => '<empty>', // ID (*)
    'name' => '<empty>',
    'groups' => array(),
);

// list with all options and their entries in wp_options table
define('SIMPLE_FOLIO_OPTION_LIST', 'simple_folio_options');
if (false === get_option(SIMPLE_FOLIO_OPTION_LIST)) {
    add_option(SIMPLE_FOLIO_OPTION_LIST, array(), '', true);
}

// prefix for storing actual options in wp_options table
define('SIMPLE_FOLIO_OPTION_PREFIX', 'simple_folio_option_');


/*
 * Function to display links to Simple Folio groups in Admin, possibly with number of items
 *
 * @param array $groups Array with group IDs
 * @param bool $items_count Show total number of items in group, default is false
 * @return string HTML with groups as links
 */
function simple_folio_list_groups($groups, $items_count = false) {
    if ($groups && is_array($groups)) {
        $group_links = array();
        $group_url = 'edit-tags.php?taxonomy=simple_folio_group&post_type=simple_folio_item';
        foreach ($groups as $id) {
            $group = get_term($id, 'simple_folio_group');
            $group_items = get_objects_in_term($group->term_id, 'simple_folio_group', array('order' => 'ASC'));
            $group_total = $items_count ? ' (' . count($group_items) . ')' : '';
            $group_links[] = '<a href="' . $group_url . '">' . esc_html($group->name) . $group_total . '</a>';
        }
        return implode(', ', $group_links);
    } else {
        return '-';
    }
}

/**
 * Function to get list with existing options
 * 
 * @return array Array with options
 */
function simple_folio_list_options() {
    return array_keys(get_option(SIMPLE_FOLIO_OPTION_LIST));
}

/**
 * Function to get option
 *
 * @param string $option Option name
 * @return mixed Option value. False if option does not exist
 */
function simple_folio_get_option($option) {
    $option_list = get_option(SIMPLE_FOLIO_OPTION_LIST);
    if (!isset($option_list[$option]))
        return false;
    return get_option($option_list[$option]);
}

/**
 * Function to add option
 *
 * @param string $option Option name
 * @param array $value Option value
 * @return bool True if option was added. False if option already exists
 */
function simple_folio_add_option($option, $value) {
    $option_list = get_option(SIMPLE_FOLIO_OPTION_LIST);
    if (isset($option_list[$option]))
        return false;
    $option_list[$option] = SIMPLE_FOLIO_OPTION_PREFIX . $option;
    update_option(SIMPLE_FOLIO_OPTION_LIST, $option_list);
    return update_option(SIMPLE_FOLIO_OPTION_PREFIX . $option, $value);
}

/**
 * Function to update option
 *
 * @param string $option Option name
 * @param array $value New option value
 * @return bool True if option was updated. False if option does not exist
 */
function simple_folio_update_option($option, $value) {
    $option_list = get_option(SIMPLE_FOLIO_OPTION_LIST);
    if (!isset($option_list[$option]))
        return false;
    return update_option($option_list[$option], $value);
}

/**
 * Function to remove option
 *
 * @param string $option Option name
 * @return bool True if option was removed. False if option does not exist
 */
function simple_folio_remove_option($option) {
    $option_list = get_option(SIMPLE_FOLIO_OPTION_LIST);
    if (!isset($option_list[$option]))
        return false;
    delete_option($option_list[$option]);
    unset($option_list[$option]);
    return update_option(SIMPLE_FOLIO_OPTION_LIST, $option_list);
}

/**
 * Function to rename option
 * 
 * @param $option_old Old option name
 * @param $option_new New option name
 * @return True if option was successfully renamed. False otherwise
 */
function simple_folio_rename_option($option_old, $option_new) {
    $value = simple_folio_get_option($option_old);
    if ($value === false)
        return false;
    simple_folio_remove_option($option_old);
    return simple_folio_add_option($option_new, $value);
}

/**
 * Function to convert portfolio name "My Portfolio" to 
 * its sanitized form "my_portfolio" 
 * 	
 * @param string $name Portfolio name
 * @return string Sanitized name
 */
function simple_folio_sanitize_name($name) {
    return str_replace('-', '_', sanitize_title($name));
}

/**
 * Hook to add new portfolio to the database with default settings from 
 * $simple_folio_defaults global variable 
 *
 * @param array $args Array $_REQUEST 
 */
function simple_folio_add_portfolio_action_handler() {

    global $folio_defaults;
    $name = isset($_REQUEST['portfolio_name']) ? trim($_REQUEST['portfolio_name']) : false;
    if (!$name) {
        return;
    }

    // prepare settings and save in DB
    $portfolio_id = simple_folio_sanitize_name($name);

    $settings = $folio_defaults;
    $settings['_id'] = $portfolio_id;
    $settings['id'] = $portfolio_id;
    $settings['name'] = $name;
    $settings['simple_folio']['id'] = 'simple_folio_viewport_' . $portfolio_id;

    // save to db
    if (simple_folio_add_option($portfolio_id, $settings)) {
        add_settings_error('simple_folio_plugin_notice', 'simple_folio_plugin', __('Portfolio was successfully created!', 'simple_folio'), 'updated');
    } else {
        add_settings_error('simple_folio_plugin_notice', 'simple_folio_plugin', __('Portfolio with this ID already exist. Try another name!', 'simple_folio'), 'error');
    }
}

add_action('admin_init', 'simple_folio_add_portfolio_action_handler');

/**
 * Hook to remove portfolio settings from database
 *
 * @param array $args Array $_REQUEST 
 */
function simple_folio_remove_portfolio_action_handler() {

    // return if any of "id" or "confirm" are not set
    if (!isset($_REQUEST['id'], $_REQUEST['confirm'])) {
        return;
    }

    // remove portfolio from db
    if (simple_folio_remove_option($_REQUEST['id'])) {
        add_settings_error('simple_folio_plugin_notice', 'simple_folio_plugin', __('Portfolio was removed!', 'simple-folio'), 'updated');
    }
}

add_action('admin_init', 'simple_folio_remove_portfolio_action_handler');

/**
 * Function to sanitize & copy values from input array to $args array
 *
 * @param array $args	Resulting array (passed by reference)
 * @param array $input	Input array as key, value pairs
 * @param string $name	Key name to copy 
 * @param string $type	Key type
 * @return              Nothing
 */
function simple_folio_input_args(&$args, $input, $name, $type = '') {

    if (isset($input[$name]) && $input[$name] != '') {
        switch ($type) {
            case "int":
                $args[$name] = intval($input[$name]);
                break;
            case "id":
                $args[$name] = simple_folio_sanitize_name($input[$name]);
                break;
            case "array":
                $args[$name] = is_array($input[$name]) ? $input[$name] : array();
                break;
            default:
                $args[$name] = trim(strip_tags($input[$name]));
        }
    }

    // handle checkboxes
    if ($type == 'check') {
        $args[$name] = isset($input[$name]) ? true : false;
    }
}

function simple_folio_sanitize_settings($input) {
    global $folio_defaults;

    // our clean settings that we will save to DB
    $clean = $folio_defaults;

    // sanitize & copy values from input
    simple_folio_input_args($clean, $input, '_id', 'id');
    simple_folio_input_args($clean, array('id' => $input['name']), 'id', 'id');
    simple_folio_input_args($clean, $input, 'name');
    simple_folio_input_args($clean, $input, 'groups', 'array');

    // display notice
    add_settings_error('simple_folio_plugin_notice', 'simple_folio_plugin', __('Portfolio settings updated!', 'simple-folio'), 'updated');

    // return sanitized settings
    return $clean;
}

/**
 * Function to register settings, add settings sections
 * and fields
 */
function simmple_folio_register_settings() {

    // return if we have no portfolios
    if (!simple_folio_list_options()) {
        return;
    }

    // register settings for every portfolio so we can properly save 
    // them to "wp_options" table in WP database
    foreach (simple_folio_list_options() as $option_id) {

        // get portfolio settings
        $settings = simple_folio_get_option($option_id);

        // define setting sections separately for every portfolio
        $general_section = 'simple_folio_section_general_' . $option_id;

        // add settings sections to the "simple_folio_settings" page
        add_settings_section($general_section, '', '', 'simple_folio_settings');

        // all our settings fields for "General" and "Details Viewport" sections
        $general_fields = array(
            'id' => __('ID', 'simple-folio'),
            'name' => __('Name', 'simple-folio'),
            'groups' => __('Groups', 'simple-folio')
        );

        /* add setting fields to specific page/section and register callbacks to output input elements */
        foreach ($general_fields as $key => $title) {
            $args = array(
                'id' => $option_id,
                'option' => SIMPLE_FOLIO_OPTION_PREFIX . $option_id,
                's' => $settings
            );
            add_settings_field('simple_folio_' . $key, $title, 'simple_folio_output_field_' . $key, 'simple_folio_settings', $general_section, $args);
        }

        // register settings
        register_setting('simple_folio_settings_group_' . $option_id, SIMPLE_FOLIO_OPTION_PREFIX . $option_id, 'simple_folio_sanitize_settings');
    }
}

add_action('admin_init', 'simmple_folio_register_settings');

/**
 * Functions to output contents of different settings fields for "General" settings section
 *
 * @param array $args Array with keys "id" - portfolio id, "option" - wp_options entry
 * to save settings, "s" - settings for portfolio
 */
function simple_folio_output_field_id($args) {
    extract($args);
    echo '<input type="text" name="' . $option . '[id]" readonly value="' . $s['id'] . '">';
}

function simple_folio_output_field_name($args) {
    extract($args);
    echo '<input type="text" name="' . $option . '[name]" value="' . $s['name'] . '">';
    echo '<span class="simple_folio-help-icon" title="' .
    __('Renaming portfolio will require you to update shortcode that you use on your website!', 'simple-folio') . '"></span>';
}

function simple_folio_output_field_groups($args) {
    extract($args);
    $options = array();
    $groups = get_terms('simple_folio_group', 'orderby=count&hide_empty=0');

    if ($groups && !is_wp_error($groups)) {
        foreach ($groups as $group) {
            $selected = selected(in_array($group->term_id, $s['groups']), true, false);
            $options[] = '<option value="' . $group->term_id . '" ' . $selected . '>' . esc_html($group->name) . '</option>';
        }
    } else {
        $options[] = '<option value="">&nbsp; - &nbsp;</option>';
    }
    echo '<select name="' . $option . '[groups][]" multiple>' . implode('', $options) . '</select>';
    echo '<p class="description">' . __('Select one or few item groups to show in portfolio', 'simple-folio') . '</p>';
}

