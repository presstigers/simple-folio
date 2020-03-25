<?php if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Content Wrappers Start
 *
 * Override this template by copying it to yourtheme/simple_folio/global/content-wrapper-start.php
 * 
 * @author 	PressTigers
 * @package     Simple_Folio
 * @subpackage  Simple_Folio/templates/global 
 * @version     1.0.0
 * @since       1.0.0
 */
ob_start();

echo '<div id="simple_folio_'. $_id .'" class="simple-folio">';
$html_wrapper_start = ob_get_clean();

/**
 * Modify the Content Wrapper Start Template. 
 *                                       
 * @since   1.0.0
 * 
 * @param   html    $html_wrapper_start   Content Wrapper Start HTML.                   
 */
echo apply_filters( 'sfo_content_wrapper_start_template', $html_wrapper_start );