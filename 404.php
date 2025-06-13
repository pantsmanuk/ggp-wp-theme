<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>
	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php the_post(); get_sidebar(); rewind_posts(); ?>
		<div id="article">
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php get_search_form(); ?>
		</div>
	</div>
	</div>
	</div>
<?php get_footer(); ?>
