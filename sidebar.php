<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */
?>
	<div id="sidebar">
		<?php if (is_front_page() || isset($_GET['s'])) {
			echo get_home_subnav();
		} else {
			echo get_subnav();
		} ?>
	</div>