<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * The Template for displaying all single portfolio items
 *
 * This template can be overridden by copying it to yourtheme/simple_folio/single-simple-folio.php.
 *
 * HOWEVER, on occasion PressTigers will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author      PressTigers
 * @package 	Simple_folio/Templates
 * @version     1.0.0
 */
get_header();
// Appearance Settings -> User Defined Container Class
if (get_option('sfo_container')) {
    $container = get_option('sfo_container');

    // Get Container Id
    $container_id = $container['id'];

    // Get Container Class
    $container_class = strtolower(str_replace(',', ' ', $container['class']));
} else {
    // Default Parameters
    $container_id = 'container';
    $container_class = 'container';
}
?>
<div id="<?php echo $container_id; ?>" class="<?php echo $container_class; ?>">
    <?php while (have_posts()) : the_post(); ?>
        <div class="simple-folio">
            <div class="sf-row">
                <?php $feature_img_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())); ?>
                <div class="sf-col-img"><img src="<?php echo $feature_img_url; ?>" alt="<?php echo get_the_title(); ?>" class="sf-img-responsive" /></div>
                <div class="sf-col-article">
                    <div class="sf-content">
                        <h4 class="sf-title"><?php echo get_the_title(); ?></h4>
                        <?php
                        $sfsObj = new Simple_Folio_Shortcode();
                        $categories = $sfsObj->simple_folio_get_the_categories(get_the_ID(), 'simple_folio_group');
                        $comma_sep_categories = '';
                        foreach ($categories as $category) {
                            $comma_sep_categories .= $category->name . " / ";
                        }
                        $comma_sep_categories = rtrim($comma_sep_categories, " / ");
                        ?>
                        <div class="sf-caption"><?php echo $comma_sep_categories; ?></div>
                        <p class="sf-detail"><?php echo get_the_content(); ?></p>
                    </div>
                    <?php if ( !empty(get_post_meta(get_the_ID(), '_simple_folio_item_client_name', TRUE) ) || !empty(get_post_meta(get_the_ID(), '_simple_folio_item_link', TRUE) ) || !empty( wp_get_post_terms(get_the_ID(), 'simple_folio_tag') ) )  { ?>
                        <div class="sf-features">
                            <h4 class="sf-title">Details</h4>
                            <table class="sf-table">
                                <tbody>
                                    <?php if (!empty(get_post_meta(get_the_ID(), '_simple_folio_item_client_name', TRUE))) { ?>
                                        <tr>
                                            <td><strong><?php echo __('Client', 'simple-folio'); ?></strong></td>
                                            <td><?php echo get_post_meta(get_the_ID(), '_simple_folio_item_client_name', TRUE); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if (!empty(get_post_meta(get_the_ID(), '_simple_folio_item_link', TRUE))) { ?>
                                        <tr>
                                            <td><strong><?php echo __('Link', 'simple-folio'); ?></strong></td>
                                            <td><a href="<?php echo get_post_meta(get_the_ID(), '_simple_folio_item_link', TRUE); ?>" target="_blank"><?php echo get_post_meta(get_the_ID(), '_simple_folio_item_link', TRUE); ?></a></td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                    $folio_tags = wp_get_post_terms(get_the_ID(), 'simple_folio_tag');
                                    $comma_sep_tags = '';
                                    foreach ($folio_tags as $folio_tag) {
                                        $comma_sep_tags .= $folio_tag->name . ", ";
                                    }
                                    $comma_sep_tags = rtrim($comma_sep_tags, ", ");
                                    if (!empty($comma_sep_tags)) {
                                        ?>
                                        <tr>
                                            <td><strong><?php echo __('Tools & Technologies', 'simple-folio'); ?></strong></td>
                                            <td><?php echo $comma_sep_tags; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="sf-clear"></div>
                </div>
            </div>
        </div>
        <?php
    endwhile;
    ?>
</div>
<?php
get_footer();
