<?php if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Content Wrapper End
 *
 * Override this template by copying it to yourtheme/simple_folio/global/content-wrapper-end.php
 * 
 * @author 	PressTigers
 * @package     Simple_Folio
 * @subpackage  Simple_Folio/templates/global
 * @version     1.0.0
 * @since       1.0.0
 */
ob_start();
echo '</div>';
$html_wrapper_end = ob_get_clean();

/**
 * Modify the Content Wrapper End Template. 
 *                                       
 * @since   1.0.0
 * 
 * @param   html    $html_wrapper_end   Content Wrapper End HTML.                   
 */
echo apply_filters( 'sfo_content_wrapper_end_template', $html_wrapper_end );