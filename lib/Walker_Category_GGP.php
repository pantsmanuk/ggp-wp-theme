<?php

class Walker_Category_GGP extends Walker_Category {

	var $current_cat_id;
	var $the_category_link;
	var $the_category;
	var $the_first;

	public function __construct($cat_id) {
		$this->current_cat_id = $cat_id;
	}

	/**
	 * Starts the element output.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Optional. Depth of category in reference to parents. Default 0.
	 * @param array  $args     Optional. An array of arguments. See wp_list_categories(). Default empty array.
	 * @param int    $id       Optional. ID of the current category. Default 0.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args);

		$cat_name = esc_attr( $category->name);
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
		$link = '<a class="snav trigger" >';
		$link .= $cat_name . '</a>';

		if ( isset($current_category) && $current_category )
			$_current_category = get_category( $current_category );

		$output .= "<li>$link\n";
		$output .= "<ul class=\"expander\">\n";
		
		$args = array(
			'echo' => false,
			'type' => 'monthly',
			'limit' => 6,
		);
		
		$this->the_category_link = get_term_link( $category, $category->taxonomy );
		$this->the_category = $category->term_id;
		$this->the_first = true;
		add_filter('get_archives_link', array($this, 'archives_link'));
		add_filter('getarchives_join', array($this, 'archives_join'));
		$output .= wp_get_archives($args);
		remove_filter('get_archives_link', array($this, 'archives_link'));
		remove_filter('getarchives_join', array($this, 'archives_join'));

		$catstr = '/' . $wp_query->query_vars['category_name'];
		$class = 'last';

		if (is_category() && !is_month() && (strpos($this->the_category_link, $catstr) !== false)) {
			$class .= ' active';
		}
		if ($class) {
			$class = trim($class);
			$output .= "\t<li class=\"$class\"><a href=\"" . $this->the_category_link . "\">All category archives</a></li>\n";
		} else {
			$output .= "\t<li><a href=\"" . $this->the_category_link . "\">All category archives</a></li>\n";
		}
		$output .= "</ul>\n";
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page   Not used.
	 * @param int    $depth  Optional. Depth of category. Not used.
	 * @param array  $args   Optional. An array of arguments. Only uses 'list' for whether should append
	 *                       to output. See wp_list_categories(). Default empty array.
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;

		$output .= "</li>\n";
	}

	public function archives_link($link_html) {
		global $wp_query;
		$datestr = '/' . $wp_query->query_vars['year'] . '/' . str_pad($wp_query->query_vars['monthnum'], 2, '0', STR_PAD_LEFT);
		$catstr = '/' . $wp_query->query_vars['category_name'];
		$link_html = str_replace(get_home_url() . '/', $this->the_category_link, $link_html);
		$link_html = preg_replace("/ title='[^']*'/", '', $link_html);
		if ($this->the_first) {
			$class = 'first';
		} else {
			$class = '';
		}
		$this->the_first = false;
		if (is_month() && (strpos($link_html, $datestr) !== false) && (strpos($this->the_category_link, $catstr) !== false)) {
			$class .= ' active';
		}
		if ($class) {
			$class = trim($class);
			$link_html = str_replace('<li>',"<li class=\"$class\">", $link_html);
		}
		return $link_html;
	}

	public function archives_join() {
		global $wpdb;
		return " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) " . 
			" INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) " . 
			" AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id = $this->the_category ";
	}

}

?>
