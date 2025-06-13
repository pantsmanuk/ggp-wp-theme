<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */
?>
		<div id="footer">
			<div class="inner">
				<div id="newsfeeds">
					<div id="feed_1">
						<h3>Twitter feed</h3>
						<?php echo do_shortcode('[custom-twitter-feeds]'); ?>
					</div>
					<div id="feed_2">
						<h3>Latest blog posts</h3>
						<?php $blog_cat_id = get_cat_ID('Blogs'); echo get_catefory_feed($blog_cat_id); ?>
					</div>
					<div id="feed_3">
						<h3>Latest news</h3>
						<?php $news_cat_id = get_cat_ID('News'); echo get_catefory_feed($news_cat_id); ?>
					</div>
				</div>
				<div id="feed_footer">
					<div id="footer_1">
						<p></p>
					</div>
					<div id="footer_2">
						<a class="rsslink" href="<?php echo get_category_feed_link($blog_cat_id, ''); ?>" target="_blank">subscribe to this blog</a>
					</div>
					<div id="footer_3">
						<a class="rsslink" href="<?php echo get_category_feed_link($news_cat_id, ''); ?>" target="_blank">subscribe to newsfeed</a>
					</div>
				</div>
				<div id="details">Directors:  T.D. Maxwell, A. Maxwell - Registered Office:  Suite 33, AMP House, Croydon, CR0 2LX - Registered in England:  2685491 - VAT no:  574 3916 15<br />
				Copyright &copy; <?php echo date("Y"); ?> GGP Systems Ltd. Contains Ordnance Survey data. &copy; Crown copyright and database rights 2013 Ordnance Survey.</div>
			</div>
		</div>
		</div> <!-- What does this match with? Container?-->
		<?php do_action('wp_footer'); ?>
		<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/functions.js"></script>
		<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/domready.js"></script>
	</body>
</html>
