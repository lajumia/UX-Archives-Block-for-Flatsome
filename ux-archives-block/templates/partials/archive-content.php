<?php
/**
 * Portfolio content template.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

while ( have_posts() ) : the_post(); 
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); // full-size image
?>
    <div class="portfolio-item" style="margin-bottom:30px;">
        <!-- Post Content -->
        <div class="portfolio-content" style="margin-bottom:15px;">
            <?php the_content(); ?>
        </div>

        <!-- Post Thumbnail -->
        <?php if( $thumbnail_url ): ?>
            <div class="portfolio-thumbnail" style="position:relative; display:inline-block;">
                <a href="<?php echo esc_url($thumbnail_url); ?>" data-fancybox="portfolio" data-caption="<?php the_title(); ?>">
                    <?php the_post_thumbnail('large'); ?>
                    <span class="zoom-icon" style="
                        position:absolute;
                        top:10px;
                        right:10px;
                        background:rgba(0,0,0,0.5);
                        color:#fff;
                        padding:5px 8px;
                        border-radius:50%;
                        font-size:16px;
                        pointer-events:none;
                    ">🔍</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endwhile; wp_reset_query(); ?>
