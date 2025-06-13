<?php

class Walker_Page_GGP extends Walker_Page {

	var $current_page_id;
	var $the_first;
	var $element_count;
	
	public function __construct($current_page_id) {
		$this->current_page_id = $current_page_id;
	}
	
	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements,
									 $max_depth, $depth = 0, $args, &$output ) {
		if ( $depth === 0 ) {
			// guard against undefined key or non-array
			if ( isset( $children_elements[ $element->ID ] )
				&& is_array( $children_elements[ $element->ID ] ) ) {
				$this->element_count = count( $children_elements[ $element->ID ] );
			} else {
				$this->element_count = 0;
			}
		}

		// now safe to call the parent
		parent::display_element( $element, $children_elements,
			$max_depth, $depth, $args, $output );
	}

	/**
	 * Outputs the beginning of the current level in the tree before elements are output.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
	 * @param array  $args   Optional. Arguments for outputting the next level.
	 *                       Default empty array.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		$indent = str_repeat( $t, $depth );
		if ($depth == 0) {
			$class = '"expander"';
		} else {
			$class = '"children"';
		}
		$output .= "{$n}{$indent}<ul class=$class>{$n}";
		$this->the_first = true;
	}

	/**
	 * Outputs the end of the current level in the tree after elements are output.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
	 * @param array  $args   Optional. Arguments for outputting the end of the current level.
	 *                       Default empty array.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		$indent = str_repeat( $t, $depth );
		$output .= "{$indent}</ul>{$n}";
	}

	/**
	 * Outputs the beginning of the current element in the tree.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string  $output       Used to append additional content. Passed by reference.
	 * @param WP_Post $page         Page data object.
	 * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
	 * @param array   $args         Optional. Array of arguments. Default empty array.
	 * @param int     $current_page Optional. Page ID. Default 0.
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);

		if ($depth == 0) {
			if ($args['has_children']) {
				$href = 'class="snav trigger"';
			} else {
				$href = 'class="snav nochild" href="' . get_page_link($page->ID) . '"';
			}
		} else {
			$href = 'href="' . get_page_link($page->ID) . '"';
		}

		if ($this->the_first) {
			$li_class = 'first';
		} else {
			$li_class = '';
		}
		$this->the_first = false;

		if ($depth == 1) {
			$this->element_count--;
			if ($this->element_count == 0) {
				$li_class .= ' last';
			}
		}

		if ($page->ID == $this->current_page_id) {
			$li_class .= ' active';
		}
		$li_class = trim($li_class);
		$li_class = " class=\"$li_class\"";
		
		$output .= $indent . '<li' . $li_class . '><a ' . $href . '>' . apply_filters('the_title', $page->post_title) . '</a>';
	}

	/**
	 * Outputs the end of the current element in the tree.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::end_el()
	 *
	 * @param string  $output Used to append additional content. Passed by reference.
	 * @param WP_Post $page   Page data object. Not used.
	 * @param int     $depth  Optional. Depth of page. Default 0 (unused).
	 * @param array   $args   Optional. Array of arguments. Default empty array.
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		$output .= "</li>{$n}";
	}

}

?>
