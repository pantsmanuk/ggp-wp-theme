<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

add_filter('post_class', 'add_element_to_post_class'); 
get_header(); ?>

	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php get_sidebar(); ?>
		<div id="article">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h1><?php the_title(); ?></h1>
				<p><small>By <a href="<?php echo get_author_posts_url($authordata->ID); ?>" title="Get more from this author"><?php echo get_the_author(); ?></a></small></p>
				<p><small>Posted in <?php the_category(', ')?> on <?php the_time('F jS Y') ?> <?php the_time() ?></small></p>
				<div class="entry">
					<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
	
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php the_tags( '<p><small>Read more: ', ', ', '</small></p>'); ?>
					<?php if(function_exists('ggp_socmshare_post_footer')) { ggp_socmshare_post_footer(); } ?>
				</div>
				<p class="postmetadata"><?php comments_template(); ?></p>
			</div>	
		<?php endwhile; else: ?>
	
			<p>Sorry, no posts matched your criteria.</p>
	
		<?php endif; ?>
		</div>
	</div>
	</div>
	</div>
	<?php get_footer(); ?>
