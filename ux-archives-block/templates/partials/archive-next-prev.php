<?php
// Previous post in same 'archives_category'
function archives_previous_post_link_custom() {
    $prev_post = get_previous_post(true, '', 'archives_category');
    if ($prev_post) {
        $title = esc_html(get_the_title($prev_post->ID));
        $url   = get_permalink($prev_post->ID);
        echo '<a class="prev-link plain" href="' . esc_url($url) . '"><i class="icon-angle-left" aria-hidden="true"></i> ' . $title . '</a>';
    }
}

// Next post in same 'archives_category'
function archives_next_post_link_custom() {
    $next_post = get_next_post(true, '', 'archives_category');
    if ($next_post) {
        $title = esc_html(get_the_title($next_post->ID));
        $url   = get_permalink($next_post->ID);
        echo '<a class="next-link plain" href="' . esc_url($url) . '">' . $title . ' <i class="icon-angle-right" aria-hidden="true"></i></a>';
    }
}
?>



<div class="row">
    <div class="large-12 col pb-0">
        <div class="flex-row flex-has-center next-prev-nav bt bb">
            <div class="flex-col flex-left text-left">
                <?php archives_previous_post_link_custom(); ?>
            </div>
            <div class="flex-col flex-right text-right">
                <?php archives_next_post_link_custom(); ?>
            </div>
        </div>
    </div>
</div>
