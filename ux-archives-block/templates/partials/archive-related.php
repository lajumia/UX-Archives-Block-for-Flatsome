<?php
/**
 * Archives Related Slider (Flatsome Card Design + Flickity Slider)
 * @package Flatsome\Templates
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get the first category
$terms   = get_the_terms( get_the_ID(), 'archives_category' );
$term_id = $terms ? current( $terms )->term_id : '';

$args = array(
	'post_type'      => 'archives',
	'posts_per_page' => 8,
	'post__not_in'   => array( get_the_ID() ),
	'tax_query'      => array(
		array(
			'taxonomy' => 'archives_category',
			'field'    => 'term_id',
			'terms'    => $term_id,
		),
	),
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) : ?>
	<div class="portfolio-related-wrapper">
		<div class="row portfolio-related large-columns-4 medium-columns-3 small-columns-2 row-small slider row-slider slider-nav-reveal slider-nav-push" id="archives-related-slider">
			<?php while ( $query->have_posts() ) : $query->the_post(); 
				$categories = get_the_term_list( get_the_ID(), 'archives_category', '', ', ', '' );
				?>
				<div class="col" data-terms="<?php echo esc_attr( wp_strip_all_tags( $categories ) ); ?>">
					<div class="col-inner">
						<a href="<?php the_permalink(); ?>" class="plain">
							<div class="portfolio-box box has-hover">
								<div class="box-image">
									<div>
										<?php the_post_thumbnail( 'medium', ['class' => 'attachment-medium size-medium wp-post-image'] ); ?>
									</div>
								</div>
								<div class="box-text text-center">
									<div class="box-text-inner">
										<h6 class="uppercase portfolio-box-title"><?php the_title(); ?></h6>
										<?php if ( $categories ) : ?>
											<p class="uppercase portfolio-box-category is-xsmall op-6">
												<span class="show-on-hover"><?php echo wp_kses_post( $categories ); ?></span>
											</p>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</a>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</div>

	<!-- Include Flickity JS and CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flickity@2/dist/flickity.min.css">
	<script src="https://cdn.jsdelivr.net/npm/flickity@2/dist/flickity.pkgd.min.js"></script>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const slider = document.querySelector('#archives-related-slider');
		if (slider && typeof Flickity !== 'undefined') {
			const flkty = new Flickity(slider, {
				cellAlign: 'left',
				contain: true,
				pageDots: false,
				groupCells: true,
				wrapAround: true,
				prevNextButtons: true,
				imagesLoaded: true,
				draggable: '>1',
				selectedAttraction: 0.015,
				friction: 0.25,
				arrowShape: {  // disable built-in SVG arrows
					x0: 0, x1: 0, y1: 0, x2: 0, y2: 0, x3: 0
				}
			});

			// Replace SVG arrows with Flatsome icons
			const prevBtn = slider.parentElement.querySelector('.flickity-prev-next-button.previous');
			const nextBtn = slider.parentElement.querySelector('.flickity-prev-next-button.next');
			if (prevBtn) prevBtn.innerHTML = '<i class="icon-angle-left" aria-hidden="true"></i>';
			if (nextBtn) nextBtn.innerHTML = '<i class="icon-angle-right" aria-hidden="true"></i>';
		}
	});
	</script>

	<style>
	.portfolio-related-wrapper {
		margin-top: 60px;
		position: relative;
	}
	.portfolio-related .col {
		width: 25%;
		padding: 0 10px;
		box-sizing: border-box;
	}
	.portfolio-related .box-image img {
		width: 100%;
		height: auto;
		border-radius: 6px;
	}
	.portfolio-related .portfolio-box {
		transition: transform 0.3s ease;
	}
	.portfolio-related .portfolio-box:hover {
		transform: translateY(-5px);
	}
	.portfolio-related .portfolio-box-title {
		font-weight: 600;
		font-size: 14px;
		margin-top: 10px;
	}

	/* Flickity arrows styling */
	.flickity-prev-next-button {
		background: rgba(255,255,255,0.9);
		border-radius: 5%;
		width: 40px;
		height: 40px;
		display: flex;
		align-items: center;
		justify-content: center;
		box-shadow: 0 2px 5px rgba(0,0,0,0.15);
	}
	.flickity-prev-next-button:hover {
		background: #fff;
	}
	.flickity-prev-next-button i {
		font-size: 20px;
		color: #111;
	}
	.flickity-prev-next-button.previous {
		left: -25px;
	}
	.flickity-prev-next-button.next {
		right: -25px;
	}
	</style>

<?php endif;
wp_reset_postdata();
?>
