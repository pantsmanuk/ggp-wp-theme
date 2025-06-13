<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */

	$form = '<form method="get" id="searchform" action="' . home_url( '/' ) . '" >
	<div><label class="screen-reader-text" for="s">' . __('Search for:', 'ggp-systems') . '</label>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="'. esc_attr__('Search', 'ggp-systems') .'" />
	</div>
	</form>';

	echo apply_filters('get_search_form', $form);
?>
