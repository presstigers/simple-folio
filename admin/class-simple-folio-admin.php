<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * 
 * @link       http://www.presstigers.com
 * @since      1.0.0
 *
 * @package    Simple_Folio
 * @subpackage Simple_Folio/admin
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Folio_Admin
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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Action - Add Settings Menu
        add_action( 'admin_menu', array($this, 'admin_menu'), 12 );

        // Action - Save Settings
        add_action( 'admin_notices', array($this, 'simple_folio_item_settings_save' ) );
        
        /**
         * The class is responsible for defining all the post meta options under 'simple_folio_item' post type
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-folio-meta-box.php';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simple-folio-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simple-folio-admin.js', array( 'jquery' ), $this->version, true );
    }
    
    /**
     * Add Settings Page Under Simple Folio.
     * 
     * @since   2.0.0
     */
    public function admin_menu()
    {
        add_submenu_page('edit.php?post_type=simple_folio_item', __('Settings', 'simple-folio'), __('Settings', 'simple-folio'), 'manage_options', 'simple-folio-settings', array($this, 'settings_output'));
    }
    
    /**
     * Render the DOM for Settings Page
     */
    public function settings_output() {
        ?>
        <div class="wrap">
            <h1><?php _e('Simple Folio Settings', 'simple-folio'); ?></h1>
            <div class="clear"></div>

            <!-- Settings Tabs -->
            <h2 class="nav-tab-wrapper">                
                <a href="#settings-shortcode_generator" class="nav-tab nav-tab-active"><?php echo __('Shortcode Generator', 'simple-folio'); ?></a>
                <a href="#settings-appearance" class="nav-tab "><?php echo __('Appearance', 'simple-folio'); ?></a>
            </h2>

            <!-- Shortcode Generator Section -->
            <div id="settings-shortcode_generator" class="sfo-admin-settings" style="display: block;">
                <p><em>On this page you can manage portfolios for your website.</em></p>
                <br />
                <div class="sfo-section">                   
                    <div class="simple-folio-settings-left-sec">
                    <h3>Add Portfolio</h3>
                    <p>Enter the portfolio name in below field and click <strong>"Add New"</strong> button.</p>
                    <form id="add_portfolio" method="post" action="">
                        <input type="hidden" name="simple_folio_action" value="add_simple_folio">
            <?php wp_nonce_field(basename(__FILE__)); ?>
                        <input type="text" name="portfolio_name" value="" maxlength="18" placeholder="Portfolio Name" autofocus autocomplete="off">
                        <input type="submit" name="submit" class="button-primary" value="Add New">
                    </form><br /><br />
                    </div>
                    <div class="simple-folio-settings-right-sec">
                    <h3>Portfolio List</h3>
            <?php if (simple_folio_list_options()) : ?>
                            <ul class="simple-folio-ps-header simple-folio-ps-grid">
                                <li class="simple-folio-ps-open">&nbsp;</li>
                                <li class="simple-folio-ps-name"><?php _e('Portfolio Name', 'simple-folio'); ?></li>
                                <li class="simple-folio-ps-shortcode"><?php _e('Shortcode', 'simple-folio'); ?></li>
                                <li class="simple-folio-ps-group"><?php _e('Groups', 'simple-folio'); ?></li>
                                <li class="simple-folio-ps-actions"><?php _e('Actions', 'simple-folio'); ?></li>
                            </ul>	
                <?php
            else :
                echo "<p>" . __('List empty.', 'simple-folio') . "</p>";
            endif;

            // loop portfolio settings in DB
            foreach (array_reverse(simple_folio_list_options()) as $portfolio_id) :
                // get settings
                $ps = simple_folio_get_option($portfolio_id);

                // settings section class
                $section_class = 'simple-folio-ps-section';

                // check if 'simple_folio_open_settings' cookie is set
                if (isset($_COOKIE['simple_folio_open_settings'])) {
                    if (simple_folio_sanitize_name($_COOKIE['simple_folio_open_settings']) == $ps['_id']) {
                        $section_class .= ' simple-folio-ps-opened';
                    }
                }
                ?>
                <div id="<?php echo $ps['_id']; ?>" class="<?php echo esc_attr($section_class); ?>">
                    <ul class="simple-folio-ps-main simple-folio-ps-grid">
                        <li class="simple-folio-ps-open">
                            <a href="#" title="<?php _e('Click to open settings', 'simple-folio'); ?>"><span></span></a>
                        </li>
                        <li class="simple-folio-ps-name"><a href="#"><?php echo esc_html($ps['name']); ?></a></li>
                        <li class="simple-folio-ps-shortcode">
                            <span class="simple-folio-shortcode-text">[simple_folio id=<?php echo esc_html('"' . $ps['id'] . '"'); ?>]</span>
                            <span class="simple-folio-help-icon" title="<?php _e('Copy and insert this shortcode into the page or post where you want portfolio to appear. To select shortcode simply click on it!', 'simple-folio'); ?>"></span>
                        </li>
                        <li class="simple-folio-ps-group"><?php echo simple_folio_list_groups($ps['groups'], true); ?></li>
                        <li class="simple-folio-ps-actions">
                            <form class="simple-folio-ps-remove" method="post" action="">
                                <input type="hidden" name="simple_folio_action" value="remove_portfolio">
                                <input type="hidden" name="id" value="<?php echo $portfolio_id; ?>">
        <?php wp_nonce_field(basename(__FILE__)); ?>
                                <input type="submit" name="submit" class="button-primary" value="<?php _e('Remove', 'simple-folio'); ?>" title="<?php _e('Click to remove portfolio', 'simple-folio'); ?>">
                                <label>
                                    <input type="checkbox" name="confirm" value="1">
                                    <em><?php _e('Check to remove!', 'simple-folio'); ?></em>
                                </label>
                            </form>
                        </li>
                    </ul><!-- #simple-folio-ps-main -->
                    <div class="simple-folio-ps-content">
                        <hgroup>
                            <h3><?php echo esc_html($ps['name']); ?></h3>
                            <h4><?php _e('Portfolio Settings', 'simple-folio'); ?></h4>
                        </hgroup>	
                        <form method="post" action="options.php">			
        <?php settings_fields('simple_folio_settings_group_' . $portfolio_id); ?>
        <?php
        // portfolio entry in wp_options will never change, so to keep track of it
        // and use it in register_settings's sanitize function, we add it as 
        // hidden "_id" input to the form (see includes/settings.php)

        $option = SIMPLE_FOLIO_OPTION_PREFIX . $portfolio_id;
        echo '<input type="hidden" name="' . $option . '[_id]" value="' . $portfolio_id . '">';

        // also, because we display settings for all portfolios on the same page
        // we must use do_settings_fields() instead of settings_sections() as 
        // we have settings sections separately for every portfolio 
        // (see includes/settings.php) and they must be saved under their own 
        // entries in wp_options. I know, it's tricky but it works :)
        ?>
                            <!-- general tab -->
                            <div class="simple-folio-ps-tab">	
                                <h3 class="title"><?php _e('General', 'simple-folio'); ?></h3>
                                <p class="description"><?php _e('This tab has to do with portfolio grid and items settings.', 'simple-folio'); ?></p>
                                <table class="form-table">
                                    <tbody>
        <?php do_settings_fields('simple_folio_settings', 'simple_folio_section_general_' . $portfolio_id); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clear"></div>
                            <p class="simple-folio-ps-footer">
        <?php submit_button(__('Save Changes', 'simple-folio'), 'primary', 'submit', false); ?> 
                                <a href="#" class="simple-folio-ps-cancel button-secondary"><?php _e('Cancel', 'simple-folio'); ?></a>
                            </p>
                        </form><!-- #options.php -->
                    </div><!-- #simple-folio-ps-content -->
                </div><!-- #simple-folio-ps-section -->
                <?php
            endforeach; // end loop
                ?>
                    <div class="clear"></div>
                    </div>
                </div>
            </div>

            <!-- Appearance Section -->
            <div id="settings-appearance"  class="sfo-admin-settings" style="display: none;">
                <p><em><?php echo __('Content Wrapper Styling', 'simple-folio'); ?></em></p>
                <br />   
                <form method="post" id="appearance-form">
                    <?php 
                    
                    // Save Content Wrapper Styling
                    if (!empty( $_POST['container'] ) ) {
                        update_option('sfo_container', array_map( 'esc_attr',  $_POST['container'] ) );
                    }                   
                    
                    // Get Container Options                    
                    if (get_option('sfo_container')) {
                        $container = get_option('sfo_container');
                        
                        // Get Container Id
                        $container_ids = explode(" ", $container['id']);
                        $container_id = $container_ids[0];
                        
                        // Get Container Class
                        $container_class = $container['class'];
                    } else {
                        
                        // Default Parameters
                        $container_id = 'container';
                        $container_class ='container';
                    }
                    ?>
                    <div class="sfo-section">                   
                        <div class="sfo-content">
                            <div class="sfo-form-group">
                                <label><?php _e('Container Id:', 'simple-folio'); ?></label>
                                <input type="text" name="container[id]" value="<?php echo $container_id; ?>" size="30" />
                            </div>
                            <div class="sfo-form-group">
                                <label><?php _e('Container Class:', 'simple-folio'); ?></label>
                                <input type="text" name="container[class]" value="<?php echo $container_class; ?>" size="30" >
                            </div>
                            <p><?php _e('Add classes seprated by space or comma e.g. container sfo-container or container, sfo-container', 'simple-folio'); ?></p>                        
                        </div>
                    </div>
                    <input type="hidden" value="1" name="admin_notices" />
                    <input type="submit" name="appearance_options" id="appearance-options" class="button button-primary" value="<?php echo __('Save Changes', 'simple-folio'); ?>" />
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Save Settings.
     * 
     * @since   2.2.3
     */
    public function simple_folio_item_settings_save()
    {   
        // Admin Notices
        if ( isset( $_POST['admin_notices'] ) &&  1 === $_POST['admin_notices'] ) {
            ?>
            <div class="updated">
                <p><?php echo __('Settings have been saved.', 'simple-folio'); ?></p>
            </div>

            <?php
        }
    }
}