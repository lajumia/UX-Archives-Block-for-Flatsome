<?php
/**
 * UX Archives Item Partial
 * Available variables:
 * $columns — number of columns
 */
$col_class = 'col'; // base class
if ($columns) {
    $col_width = 100 / $columns; // percentage width
    $col_class .= ' col-custom';
}
?>

<div class="<?php echo esc_attr($col_class); ?>" style="width: <?php echo esc_attr($col_width); ?>%">
    <div class="col-inner">
        <a href="<?php the_post_thumbnail_url(); ?>" class="plain lightbox-gallery">
            <div class="portfolio-box box has-hover box-overlay dark box-text-middle">
                <div class="box-image">
                    <div class="image-overlay-add-50 image-zoom image-cover" style="padding-top:100%;">
                        <?php the_post_thumbnail('medium'); ?>
                        <div class="overlay"></div>
                    </div>
                </div>
                <div class="box-text show-on-hover hover-slide text-center is-large">
                    <div class="box-text-inner">
                        <h6 class="uppercase portfolio-box-title"><?php the_title(); ?></h6>
                        <p class="uppercase portfolio-box-category is-xsmall">
                            <span class="show-on-hover">
                                <?php
                                $terms = get_the_terms(get_the_ID(), $filter_taxonomy);
                                if ($terms && !is_wp_error($terms)) {
                                    echo esc_html($terms[0]->name);
                                }
                                ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
