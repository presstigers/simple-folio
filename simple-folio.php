<?php
/**
 * @link              https://wordpress.org/plugins/simple-folio/
 * @since             1.0.0
 * @package           Simple_Folio
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Folio
 * Plugin URI:        https://wordpress.org/plugins/simple-folio/
 * Description:       This plugin lets you to create beautiful filterable slide down responsive portfolio.
 * Version:           1.0.1
 * Author:            PressTigers
 * Author URI:        http://www.presstigers.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl.html
 * Text Domain:       simple-folio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-folio-activator.php
 */
function activate_simple_folio() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-folio-activator.php';
    Simple_Folio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-folio-deactivator.php
 */
function deactivate_simple_folio() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-folio-deactivator.php';
    Simple_Folio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simple_folio' );
register_deactivation_hook( __FILE__, 'deactivate_simple_folio' );

/**
 * Define constants
 */
define( 'SIMPLE_FOLIO_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SIMPLE_FOLIO_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require 'includes/class-simple-folio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_folio() {
    $plugin = new Simple_Folio();
    $plugin->run();
}
run_simple_folio();