<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
add_filter('post_class', 'add_element_to_post_class'); 
get_header();
?>

	<div id="content">
	<div class="inner">
	<div class="bg">
		<?php get_sidebar(); rewind_posts(); ?>
		<div id="article">
			<?php if (have_posts()) : 
				$post = $posts[0]; // Hack. Set $post so that get_the_time() works.
				switch(true) {
					case(is_tag()):
						$archive_taxonomy = single_tag_title('', false);
						break;
					case(is_category()):
						$archive_taxonomy = single_cat_title('', false);
						break;
					default:
						$archive_taxonomy = '';
				}
				switch(true) {
					case(is_author()):
						$userdata = get_userdatabylogin(get_query_var('author_name'));
						if (!$userdata) {
							$userdata = get_user_by('slug', get_query_var('author_name'));
						}
						$archive_section = $userdata->display_name;
						break;
					case(is_day()):
						$archive_section = get_the_time('F jS, Y');
						break;
					case(is_month()):
						$archive_section = get_the_time('F, Y');
						break;
					case(is_year()):
						$archive_section = get_the_time('Y');
						break;
					default:
						$archive_section = '';
				}
				if (archive_taxonomy) {
					$archive_title = $archive_taxonomy . ' archive';
				} else {
					$archive_title = 'Archive';
				}
				if ($archive_section) {
					$archive_title .= ' for ' . $archive_section;
				}
			?>
			<h1><?php echo $archive_title; ?></h1>
			<?php while (have_posts()) : the_post(); ?>
				<div <?php post_class() ?>>
					<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p><small>By <a href="<?php echo get_author_posts_url($authordata->ID); ?>" title="Get more from this author"><?php echo get_the_author(); ?></a></small></p>
					<p><small>Posted in <?php the_category(', ')?> on <?php the_time('F jS Y') ?> <?php the_time() ?></small></p>
					<div class="entry">
						<?php
							the_excerpt();
							echo '<p><a href="';
							the_permalink();
							echo '">Read more...</a></p>';
						?>
					</div>	
				</div>
			<?php endwhile; ?>
				<div class="element navigation">
					<?php next_posts_link('&laquo; Older Entries'); if (isset($_GET['paged']) && !empty($_GET['paged'])) { echo ' || '; } previous_posts_link('Newer Entries &raquo;'); ?>
				</div>
		<?php else :
	
			if ( is_category() ) { // If this is a category archive
				if (is_date()) {
					printf("<h2>Sorry, but there aren't any posts in the %s category for that date.</h2>", single_cat_title('',false));
				} else {
					printf("<h2>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
				}
			} else if ( is_date() ) { // If this is a date archive
				echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
			} else if ( is_author() ) { // If this is a category archive
				$userdata = get_userdatabylogin(get_query_var('author_name'));
				printf("<h2>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
			} else {
				echo("<h2>No posts found.</h2>");
			}
			get_search_form();
	
		endif;?>
		</div>
	</div>
	</div>
	</div>
	<?php get_footer(); ?>
