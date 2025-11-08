<?php
/**
 * UX Archives Template
 * Receives $atts from shortcode
 */
$unique_id = $atts_for_template['unique_id'];
$posts_per_page = $atts_for_template['posts_per_page'];
$columns = $atts_for_template['columns'];
$show_filter = ! empty( $atts_for_template['show_filter'] ) && $atts_for_template['show_filter'] !== 'false';
$filter_taxonomy = $atts_for_template['filter_taxonomy'];

// 1. Category filter menu
if ( $show_filter && taxonomy_exists( $filter_taxonomy ) ) {
    $terms = get_terms(array(
        'taxonomy' => $filter_taxonomy,
        'hide_empty' => true,
    ));
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
        <div class="container mb-half">
            <ul class="nav nav-center nav-line-bottom nav-uppercase filter-nav">
                <li class="active"><a href="#" data-filter="*">All</a></li>
                <?php foreach ( $terms as $term ) : ?>
                    <li>
                        <a href="#" data-filter="[data-terms*='<?php echo esc_attr($term->slug); ?>']">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif;
}


// 2. Posts grid
$query_args = $atts_for_template['_query_args'];
$query_args['posts_per_page'] = $posts_per_page;
$query = new WP_Query( $query_args ); 
?>

<div id="<?php echo esc_attr($unique_id); ?>" class="row row-isotope large-columns-<?php echo esc_attr($columns); ?> medium-columns-3 small-columns-2 row-small row-masonry ux-archives-wrapper" data-packery-options='{"itemSelector":".col","gutter":0,"percentPosition":true}'>
    <?php if ( $query->have_posts() ) : ?>
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <?php include UX_ARCHIVES_PLUGIN_DIR . 'templates/partials/ux-archives-item.php'; ?>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
</div>

<script>
jQuery(document).ready(function($){
    var $grid = $('#<?php echo esc_js($unique_id); ?>');
    $grid.isotope({
        itemSelector: '.col',
        percentPosition: true,
        masonry: { columnWidth: '.col' }
    });
    $('.filter-nav a').click(function(e){
        e.preventDefault();
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
        $(this).closest('ul').find('li').removeClass('active');
        $(this).parent().addClass('active');
    });
});
</script>
