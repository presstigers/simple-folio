<?php if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Folio Items
 *
 * List all the portfolio items.
 * 
 * @author 	PressTigers
 * @package     Simple_Folio
 * @subpackage  Simple_Folio/templates 
 * @version     1.0.0
 * @since       1.0.0
 */

// Get Featured Image URL
$feature_img_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );

?>
<div class="sf-mix <?php echo implode(' ', $item_groups); ?>" data-myorder="<?php echo $item_order; ?>">
    <figure class="sf-details"><img src ="<?php echo $feature_img_url; ?>" alt = "<?php echo get_the_title($post_id); ?>"></figure>
    <div class="sf-overlay">
        <div class="sf-icons">
            <a href="<?php echo get_the_permalink($post_id); ?>"><i class="fa fa-link" aria-hidden="true"></i></a>
        </div>
    </div>
</div>