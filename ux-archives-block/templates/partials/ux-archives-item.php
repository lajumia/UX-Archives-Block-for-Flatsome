<?php
$terms = get_the_terms(get_the_ID(), $filter_taxonomy);
$slugs = [];
if ( $terms && ! is_wp_error( $terms ) ) {
    foreach ( $terms as $t ) $slugs[] = $t->slug;
}
$data_terms = implode(' ', $slugs);
?>

<div class="col" data-terms="<?php echo esc_attr($data_terms); ?>">
    <div class="col-inner">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" class="plain lightbox-gallery">
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
                                    $term_names = wp_list_pluck( $terms, 'name' );
                                    echo esc_html( implode( ', ', $term_names ) );
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        <?php else : ?>
            <h6><?php the_title(); ?></h6>
        <?php endif; ?>
    </div>
</div>
