<?php
/**
 * UX Archives Template - Category + Pagination + Isotope
 */
$unique_id       = $atts_for_template['unique_id'];
$posts_per_page  = $atts_for_template['posts_per_page'];
$columns         = isset($atts_for_template['columns']) ? intval($atts_for_template['columns']) : 4;
$filter_taxonomy = $atts_for_template['filter_taxonomy'];

// Get categories for the menu
$terms = taxonomy_exists($filter_taxonomy) ? get_terms([
    'taxonomy' => $filter_taxonomy,
    'hide_empty' => true,
]) : [];
?>

<?php if (!empty($terms) && !is_wp_error($terms)) : ?>
<div class="container mb-half">
    <ul class="nav nav-center nav-line-bottom nav-uppercase filter-nav">
        <?php foreach ($terms as $term) : ?>
            <li>
                <a href="#" class="category-link" data-term="<?php echo esc_attr($term->slug); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Posts grid -->
<div id="<?php echo esc_attr($unique_id); ?>" class="ux-archives-wrapper row row-small"></div>

<!-- Pagination -->
<div class="text-center mt" id="<?php echo esc_attr($unique_id); ?>-pagination"></div>

<script>
jQuery(document).ready(function($){
    var $postsContainer = $('#<?php echo esc_js($unique_id); ?>');
    var $pagination     = $('#<?php echo esc_js($unique_id); ?>-pagination');
    var ajaxUrl         = '<?php echo admin_url("admin-ajax.php"); ?>';
    var postsPerPage    = <?php echo intval($posts_per_page); ?>;
    var columns         = <?php echo $columns; ?>;
    var uniqueId        = '<?php echo esc_js($unique_id); ?>';
    var currentTerm     = '';
    var $grid;

    // Function to initialize Isotope
    function initIsotope() {
        $grid = $postsContainer.isotope({
            itemSelector: '.col',
            percentPosition: true,
            masonry: {
                columnWidth: '.col'
            }
        });
    }

    // Load posts by category and page
    function loadPosts(term = '', page = 1) {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json', // expecting JSON: { html, total_pages, current_page }
            data: {
                action: 'ux_filter_posts',
                taxonomy: '<?php echo esc_js($filter_taxonomy); ?>',
                term_slug: term,
                posts_per_page: postsPerPage,
                unique_id: uniqueId,
                columns: columns,
                paged: page
            },
            beforeSend: function() {
                $postsContainer.html('<p>Loading Archives Products...</p>');
                $pagination.html('');
            },
            success: function(response) {
                if(!response || !response.html || response.html === ''){
                    $postsContainer.html('<p>No Archives found.</p>');
                    $pagination.html('');
                } else {
                    var $items = $(response.html);

                    // Remove old items and append new ones
                    $postsContainer.html($items);

                    // Initialize or reload Isotope
                    if($grid){
                        $grid.isotope('destroy'); // destroy previous instance
                    }
                    initIsotope();

                    // Wait for images to load
                    $grid.imagesLoaded(function(){
                        $grid.isotope('layout');
                    });

                    // ------------------ Lightbox ------------------
                    if(typeof GLightbox !== 'undefined'){
                        if(window.uxLightbox) window.uxLightbox.destroy(); // destroy previous instance
                        window.uxLightbox = GLightbox({
                            selector: '.plain.lightbox-gallery',
                            touchNavigation: true,
                            loop: true,
                            zoomable: true
                        });
                    }
                    // ------------------------------------------------

                    // Render pagination if more than 1 page
                    if(response.total_pages && response.total_pages > 1){
                        renderPagination(response.total_pages, response.current_page);
                    }
                }
            }
            ,
            error: function() {
                $postsContainer.html('<p>Error loading Archives.</p>');
            }
        });
    }

    // Render numbered pagination
    function renderPagination(totalPages, currentPage){
        var html = '';
        for(var i=1; i<=totalPages; i++){
            html += '<button class="pagination-btn'+(i===currentPage?' pagination-active':'')+'" data-page="'+i+'">'+i+'</button> ';
        }
        $pagination.html(html);
    }

    // Category click
    $('.filter-nav').on('click', '.category-link', function(e){
        e.preventDefault();
        currentTerm = $(this).data('term');

        // Highlight active category
        $(this).closest('ul').find('li').removeClass('active');
        $(this).parent().addClass('active');

        loadPosts(currentTerm, 1);
    });

    // Pagination click
    $pagination.on('click', '.pagination-btn', function(){
        var page = parseInt($(this).attr('data-page'));
        loadPosts(currentTerm, page);
    });

    // Load first category by default
    if($('.filter-nav .category-link').length > 0){
        $('.filter-nav .category-link').first().click();
    }
});
</script>
