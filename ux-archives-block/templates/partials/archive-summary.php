<?php
/**
 * Archives summary template.
 *
 * @package          UX_Archives_Block
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ----------------------------
// Categories (archives_category)
// ----------------------------
$categories = get_the_term_list(
    get_the_ID(),
    'archives_category',
    '',
    ' <span class="divider">|</span> ',
    ''
);

if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
    <div class="featured_item_cats breadcrumbs mb-half">
        <?php echo $categories; ?>
    </div>
<?php endif; ?>

<!-- ----------------------------
     Post Title
----------------------------- -->
<?php if ( get_the_title() ) : ?>
    <h1 class="entry-title uppercase"><?php the_title(); ?></h1>
<?php endif; ?>

<!-- ----------------------------
     Excerpt / Content
----------------------------- -->
<?php if ( has_excerpt() ) : ?>
    <div class="archive-excerpt">
        <?php the_excerpt(); ?>
    </div>
<?php endif; ?>

<!-- ----------------------------
     Share Buttons
----------------------------- -->
<?php if ( function_exists( 'flatsome_apply_shortcode' ) && apply_filters( 'ux_archives_show_share', true ) ) : ?>
    <div class="portfolio-share">
        <?php echo flatsome_apply_shortcode( 'share', array( 'style' => 'small' ) ); ?>
    </div>
<?php endif; ?>

<!-- ----------------------------
     Tags (archives_tag)
----------------------------- -->
<?php
$tags = get_the_term_list(
    get_the_ID(),
    'archives_tag',
    '',
    ' / ',
    ''
);

if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) : ?>
    <div class="item-tags is-small bt pt-half uppercase">
        <strong><?php esc_html_e( 'Tags', 'ux-archives-block' ); ?>:</strong>
        <?php echo strip_tags( $tags ); ?>
    </div>
<?php endif; ?>
