<?php

get_header();

while ( have_posts() ) :
	the_post();
	?>


<div <?php post_class( 'flow' ); ?>>
	<h2 class="text-700"><?php the_title(); ?></h2>
	<div class="flow"><?php the_content(); ?></div>
</div>

	<?php
endwhile;

get_footer();
