<?php
/**
 * Archives single.
 *
 */

 get_header();
?>
<div class="portfolio-top">
	<div class="page-wrapper row">
  	<div class="large-3 col col-divided">
  		<div class="portfolio-summary entry-summary sticky-sidebar">
  				<?php include (plugin_dir_path( __FILE__ ) . 'partials/archive-summary.php');?>
  		</div>
  	</div>

  	<div id="portfolio-content" class="large-9 col"  role="main">
  		<div class="portfolio-inner">
  			<?php include (plugin_dir_path( __FILE__ ) . 'partials/archive-content.php'); ?>
  		</div>
  	</div>
	</div>
</div>

<div class="portfolio-bottom">
	<?php include (plugin_dir_path( __FILE__ ) . 'partials/archive-next-prev.php');?>
	
	<?php include (plugin_dir_path( __FILE__ ) . 'partials/archive-related.php'); ?>
</div>


<?php
// get_footer();