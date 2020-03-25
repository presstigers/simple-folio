<?php if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * List all the available portfolio filters.
 *
 * Override this template by copying it to yourtheme/simple_folio/folio-filter.php
 * 
 * @author 	PressTigers
 * @package     Simple_Folio
 * @subpackage  Simple_Folio/templates 
 * @version     1.0.0
 * @since       1.0.0
 */
?>
<div class="sf-controls">
    <button class="sf-filter sf-button" data-filter="all"><?php echo __('All Portfolio', 'simple-folio'); ?></button>
    <?php foreach ($filter_tags as $tag) { ?>
    <button class="sf-filter sf-button" data-filter=".<?php echo $tag->slug; ?>"><?php echo ucwords($tag->name); ?></button>
    <?php }?>
</div>