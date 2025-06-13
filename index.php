<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */
get_header(); ?>
	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php the_post(); get_sidebar(); rewind_posts(); ?>
		<div id="article">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="element" id="post-<?php the_ID(); ?>">
						<h1><?php the_title(); ?></h1>
						<div class="entry">
							<?php the_content('Read the rest of this entry &raquo;'); ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php else : ?>
				<h2 class="center">Not Found</h2>
				<p class="center">Sorry, but you are looking for something that isn't here.</p>
				<?php get_search_form(); ?>
			<?php endif; ?>	
		</div>
	</div>
	</div>
	</div>
<?php get_footer(); ?>
