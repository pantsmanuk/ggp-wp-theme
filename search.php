<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */

add_filter('post_class', 'add_element_to_post_class');
get_header(); ?>
	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php the_post(); get_sidebar(); rewind_posts(); ?>
		<div id="article">
			<?php if (have_posts()) : ?>
				<h1>Search Results</h1>
				<?php while (have_posts()) : the_post(); ?>
					<div <?php post_class() ?>>
						<h2 id="post-<?php the_ID(); ?>">
							<?php if (check_excerpt() == true) : ?>
								<a href="<?php the_permalink(); ?>">
							<?php endif;?>
							<?php the_title();
								if (check_excerpt() == true) : ?>
							</a><?php endif; ?>
						</h2>
						<p><small>By <a href="<?php echo get_author_posts_url($authordata->ID); ?>" title="Get more from this author"><?php echo get_the_author(); ?></a></small></p>
						<p><small>Posted in <?php the_category(', ')?> on <?php the_time('F jS Y') ?> <?php the_time() ?></small></p>
						<div class="entry">
							<?php if (check_excerpt() == true) {
								the_excerpt();
								echo '<p><a href="';
								the_permalink();
								echo '">Read more...</a></p>';
							} else {
								the_content();
							} ?>
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
