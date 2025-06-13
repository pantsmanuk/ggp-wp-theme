<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */

get_header(); ?>
	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php the_post();
		$disableSidebar = get_post_meta($post->ID, 'disableSidebar', $single = true);
		if ($disableSidebar !== 'true') { get_sidebar(); }
		rewind_posts(); ?>
		<div id="article">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="element" id="post-<?php the_ID(); ?>">
					<h1><?php the_title(); ?></h1>
					<div class="entry">
						<?php the_content('Read the rest of this entry &raquo;'); ?>
						<?php if (($id != 10) && !in_array(10, $post->ancestors)): /*hard-coded hack to hide social sharing for "Downloads" page and children*/ ?>
						<?php if(function_exists('ggp_socmshare_post_footer')) { ggp_socmshare_post_footer(); } ?>
						<?php endif; ?>
					</div>
					<p class="postmetadata">
						<?php edit_post_link('Edit this entry','',''); ?>
					</p>
				</div>
			<?php endwhile; endif; ?>
		</div>
	</div>
	</div>
	</div>
<?php get_footer(); ?>
