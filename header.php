<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php echo esc_url(get_template_directory_uri()); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php wp_title('&laquo; ', true, 'right'); ?><?php bloginfo('name'); ?></title>
	<meta content="GGP Systems Ltd - <?php echo date('Y'); ?>" name="copyright"/>
	<link media="screen" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet"></link>
	<!--[if lte IE 6]>
		<link media="screen" type="text/css" href="<?php echo esc_url(get_template_directory_uri()); ?>/style_ie6.css" rel="stylesheet"></link>
		<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/supersleight.js"></script>
	<![endif]-->
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link href="<?php echo esc_url(get_template_directory_uri()); ?>/favicon.png" type="image/png" rel="icon"/>
	<link title="GGP systems updates RSS - via Twitter" type="application/rss+xml" href="http://twitter.com/statuses/user_timeline/15765590.rss" rel="alternate"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/mootools-1.2.4-core.js"></script>
	<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/mootools-1.2.4.4-more.js"></script>
	<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri()); ?>/js/shadowbox/shadowbox.js"></script>
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="container">
		<div id="c_mask">
			<div id="prev">&nbsp;</div>
			<div id="next">&nbsp;</div>
		</div>
		<div id="header">
		<div class="inner">
			<div id="toolbar">
				<a href="#" id="search">search</a>
				<div class="tblink" id="login"><?php wp_loginout(); ?></div>
				<?php wp_register('<div class="tblink" id="adminreg">', '</div>'); ?>
				<?php if (function_exists('cfmobi_request_handler')) : ?>
				<div class="tblink" id="mobilelink"><a href="?cf_action=show_mobile">Mobile</a></div>
				<?php endif; ?>
				<div id="searchfield" class="hidden">
					<?php get_search_form(); ?>
				</div>
			</div>
			<ul id="nav">
				<?php the_post(); echo get_main_nav(); rewind_posts(); ?>
			</ul>
			<div id="carousel">
				<?php echo get_carousel(); ?>
			</div>
		</div>
		</div>
