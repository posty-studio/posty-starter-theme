<?php

get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post();
        the_title();
    }
} else {
    echo 'No posts!';
}

get_footer();
