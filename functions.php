<?php
/**
 * @package WordPress
 * @subpackage GGP_Theme
 */

require_once('lib/Walker_Page_GGP.php');
require_once('lib/Walker_Category_GGP.php');

define('GGP_META_KEY_SIDEBAR_GROUP', 'ggp_sidebar_group');
define('GGP_META_KEY_CONTAINED_GROUPS', 'ggp_sidebar_contained_groups');
define('GGP_HOMEPAGE_CATEGORY_NAME', 'Homepage Sidebar');
define('GGP_META_VALUE_FIND_US', 'How to Find Us');
define('GGP_META_VALUE_NEWS', 'News');
define('GGP_META_VALUE_RELEASES', 'Software Releases');
define('GGP_META_VALUE_BLOGS', 'Blogs');
define('GGP_SIDEBAR_VIDEO', 'ggp_sidebar_video');

function add_element_to_post_class($classes)
{
    $classes[] = 'element';
    return $classes;
}

if (!function_exists('parse_ini_string')) {
    function parse_ini_string($string)
    {
        $array = Array();
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $statement = preg_match("/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match);
            if ($statement) {
                $key = $match['key'];
                $value = $match['value'];
                # Remove quote
                if (preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
                    $value = mb_substr($value, 1, mb_strlen($value) - 2);
                }
                $array[$key] = $value;
            }
        }
        return $array;
    }
}

// Awesome code snippet downloaded for http://snipplr.com/view.php?codeview&id=17432
// To allow date-based category archives (e.g. http://www.ggpsystems.co.uk/category/news/2010/06/)
function extend_date_archives_flush_rewrite_rules()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

add_action('init', 'extend_date_archives_flush_rewrite_rules');

function extend_date_archives_add_rewrite_rules($wp_rewrite)
{
    $rules = array();
    $structures = array(
        $wp_rewrite->get_category_permastruct() . $wp_rewrite->get_date_permastruct(),
        $wp_rewrite->get_category_permastruct() . $wp_rewrite->get_month_permastruct(),
        $wp_rewrite->get_category_permastruct() . $wp_rewrite->get_year_permastruct(),
    );
    foreach ($structures as $s) {
        $rules += $wp_rewrite->generate_rewrite_rules($s);
    }
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}

add_action('generate_rewrite_rules', 'extend_date_archives_add_rewrite_rules');
// End of awesome code snippet

// And now some awesomeness from Jorge, to prevent the redirection from 
// http://www.ggpsystems.co.uk/category/news/2010/06/ to the canonical URL
//  http://www.ggpsystems.co.uk/category/news/
function extend_date_archives_redirect_canonical($canonical_url, $requested_url)
{
    $ret_val = true;
    if (strpos($canonical_url, '/category/') !== false) {
        if (preg_match('@category/(.+?)/[0-9]{4}/?@', $requested_url) > 0)
            $ret_val = false;
    }
    return $ret_val;
}

add_filter('redirect_canonical', 'extend_date_archives_redirect_canonical', 10, 2);


function get_parent_catID($post)
{
    $categs = get_terms();
    foreach ($categs as $categ) {
        //excludes the "featured in carousel" category id = 38 and the front and search pages
        if (is_front_page() || isset($_GET['s'])) {
            return 0;
        } else if (in_category($categ) && ($categ != 38)) {
            $parentcats[] = $categ;
            return $categ;
            break;
        }
    }
}

function get_top_catID($post)
{
    $parentCatId = get_parent_catID($post);
    $parentCats = get_category_parents($parentCatId, false, ',');
    $parents = explode(',', $parentCats);
    if (count($parents) > 2) {
        $topCatId = get_cat_ID($parents[1]);
    } else {
        $topCatId = $parentCatId;
    }
    return $topCatId;
}

// Get elements in the "main nav" category
function get_main_nav()
{
    $pages_args = array(
        'depth' => 1,
        'title_li' => '',
        'echo' => 1,
        'sort_column' => 'menu_order, post_title'
    );
    wp_list_pages($pages_args);
}

function get_posts_section($section, $parent_post_id)
{
    global $post;

    echo '<h4>' . $section . '<img src="' . get_template_directory_uri() . '/imgs/subnav_corner.png" alt="graphic" /></h4><ul class="subnav">';
    $categories_args = array(
        'depth' => 2,
        'title_li' => '',
        'echo' => 1,
        'hide_empty' => true,
        'walker' => new Walker_Category_GGP(get_cat_ID($section)),
        'child_of' => get_cat_ID($section),
        'orderby' => 'order'
    );

    wp_list_categories($categories_args);

    echo '</ul>';
}

function get_subnav_section($section, $parent_post_id)
{
    global $post;

    echo '<h4>' . $section . '<img src="' . get_template_directory_uri() . '/imgs/subnav_corner.png" alt="graphic" /></h4><ul class="subnav">';
    $pages_args = array(
        'depth' => 2,
        'title_li' => '',
        'echo' => 1,
        'meta_key' => GGP_META_KEY_SIDEBAR_GROUP,
        'meta_value' => $section,
        'walker' => new Walker_Page_GGP($post->ID),
        'child_of' => $parent_post_id,
        'sort_column' => 'menu_order, post_title'
    );
    wp_list_pages($pages_args);
    echo '</ul>';
}


// Retrieve subnav from categories
function get_subnav()
{
    global $post;
    global $wpdb;

    if (!is_page()) {
        $post = get_page_by_path('news-events');
        $top_ancestor_page = $post->ID;
    } else {
        if (is_array($post->ancestors)) {
            $top_ancestor_page = end($post->ancestors);
            if (!$top_ancestor_page) {
                $top_ancestor_page = $post->ID;
            }
        } else {
            $top_ancestor_page = $post->ID;
        }
    }

    $contained_groups = get_post_meta($top_ancestor_page, GGP_META_KEY_CONTAINED_GROUPS, true);
    $contained_groups = explode(',', $contained_groups);
    foreach ($contained_groups as $value) {
        switch ($value) {
            case GGP_META_VALUE_FIND_US:
                get_find_us_section($value, $top_ancestor_page);
                break;
            case GGP_META_VALUE_NEWS:
            case GGP_META_VALUE_RELEASES:
            case GGP_META_VALUE_BLOGS:
                get_posts_section($value, $top_ancestor_page);
                break;
            default:
                get_subnav_section($value, $top_ancestor_page);
        }
    }
}

function get_find_us_section($section, $parent_post_id)
{
    $postargs = array(
        'child_of' => $parent_post_id,
        'meta_key' => GGP_META_KEY_SIDEBAR_GROUP,
        'meta_value' => $section
    );
    $homesnavpost = get_pages($postargs);
    $attargs = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $homesnavpost[0]->ID
    );
    $attachments = get_posts($attargs);

    echo '<h4>' . $section . '<img src="' . get_template_directory_uri() . '/imgs/subnav_corner.png" alt="graphic" /></h4>';
    echo '<div class="subnav">';
    // OK, the block comment below should strip out the entire map (and it's logic processing).
    /*foreach ($attachments as $attachment) {
        if (wp_attachment_is_image($attachment->ID)) {
            $attimgurl = wp_get_attachment_url($attachment->ID);
        }
    }
    // Turn the map into a link to a video??? What was the thinking for this Jorge?
    $attviddata = get_post_meta($homesnavpost[0]->ID, GGP_SIDEBAR_VIDEO, true);
    $attviddata = parse_ini_string($attviddata);
    if (isset($attviddata['url'])) {
        if (isset($attviddata['width']))
            $attvidwidth = ';width=' . $attviddata['width'];
        else
            $attvidwidth = '';
        if (isset($attviddata['height']))
            $attvidheight = ';height=' . $attviddata['height'];
        else
            $attvidheight = '';
        echo '<a class="homevid" href="' . $attviddata['url'] . '" rel="shadowbox[map]' . $attvidwidth . $attvidheight . '">';
    }
    echo '<img src="' . $attimgurl . '" alt="' . $section . '" />'; // This inserts the current map
    if (isset($attviddata['url']))
        echo '</a>'; */
    echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2490.528108669966!2d-0.09688268426782225!3d51.374971279613575!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487607327cf8581f%3A0xee2f6d6f9e5a7e22!2sGGP+Systems+Ltd!5e0!3m2!1sen!2suk!4v1469181335016" width="290" height="280" frameborder="0" style="border:0" allowfullscreen></iframe>';
    echo '<div class="hometxt"><h3>' . $homesnavpost[0]->post_title . '</h3>' . $homesnavpost[0]->post_content . '</div>';
    echo '</div>';
}

// Get homepage subnav
function get_home_subnav()
{
    $postargs = array(
        'category' => get_cat_ID(GGP_HOMEPAGE_CATEGORY_NAME)
    );
    $homesnavpost = get_posts($postargs);
    $attargs = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $homesnavpost[0]->ID
    );
    $attachments = get_posts($attargs);
    foreach ($attachments as $attachment) {
        if (wp_attachment_is_image($attachment->ID)) {
            $attimgurl = wp_get_attachment_url($attachment->ID);
            $attimgid = $attachment->ID;
        }
    }
    $snav = '<h4>Introduction video<img src="' . get_template_directory_uri() . '/imgs/subnav_corner.png" alt="graphic" /></h4>';
    $snav .= '<div class="subnav">';
    $attviddata = get_post_meta($homesnavpost[0]->ID, GGP_SIDEBAR_VIDEO, true);
    $attviddata = parse_ini_string($attviddata);
    if (isset($attviddata['url'])) {
        if (isset($attviddata['width']))
            $attvidwidth = ';width=' . $attviddata['width'];
        else
            $attvidwidth = '';
        if (isset($attviddata['height']))
            $attvidheight = ';height=' . $attviddata['height'];
        else
            $attvidheight = '';
        $snav .= '<a href="//www.youtube.com/embed/1fyRD5gnGUg?autoplay=1&rel=0&TB_iframe" title="What we do at GGP Systems" class="thickbox">';
    }
    if (isset($attviddata['url']) && isset($attimgid)) {
        $attimgmeta = wp_get_attachment_metadata($attimgid);
        $attimgwidth = $attimgmeta['width'];
        $attimgheight = $attimgmeta['height'];
        $playovrheight = 102;
        $playovrwidth = 102;
        $leftpos = intval(($attimgwidth - $playovrwidth) / 2);
        $toppos = -intval(($attimgheight + $playovrheight) / 2);
        $snav .= '<img src="' . $attimgurl . '" alt="What we do at GGP Systems" /><img style="margin-bottom:-' . $playovrheight . 'px;position:relative;top:' . $toppos . 'px;left:' . $leftpos . 'px;" src="' . get_template_directory_uri() . '/imgs/play_overlay.png" alt="Play video" />';
    }
    if (isset($attviddata['url']))
        $snav .= '</a>';
    $snav .= '<div class="hometxt"><h3>' . $homesnavpost[0]->post_title . '</h3><p>' . $homesnavpost[0]->post_content . '</p></div>';
    $snav .= '</div>';
    return $snav;
}

// Get elements in the carousel category and display attachments
function get_carousel()
{
    $postargs = array(
        'orderby' => 'date',
        'category_name' => 'carousel'
    );
    $c_posts = get_posts($postargs);
    $c_links = '<div id="c_links">';
    $c_nav = '<div id="c_nav">';
    $c_container = '<ul id="c_container">';
    foreach ($c_posts as $c_post) {
        if ($c_posts[0] == $c_post) {
            $c_links .= '<a href="' . get_permalink($c_post->ID) . '">' . $c_post->post_excerpt . '<img src="' . get_template_directory_uri() . '/imgs/arrow_right_white.gif" alt="' . $c_post->post_excerpt . '" /></a>';
            $c_nav .= '<img height="12px" width="12px" class="sel" src="' . get_template_directory_uri() . '/imgs/dot.gif" alt="carousel navigation" />';
        } else {
            $c_links .= '<a href="' . get_permalink($c_post->ID) . '" class="hidden">' . $c_post->post_excerpt . '<img src="' . get_template_directory_uri() . '/imgs/arrow_right_white.gif" alt="' . $c_post->post_excerpt . '" /></a>';
            $c_nav .= '<img height="12px" width="12px" src="' . get_template_directory_uri() . '/imgs/dot.png" alt="carousel navigation" />';
        }
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' => null,
            'post_parent' => $c_post->ID
        );
        $attachments = get_posts($args);
        $c_container .= '<li><img src="' . wp_get_attachment_url($attachments[0]->ID) . '" alt="none" /></li>';
    }
    $c_links .= '</div>';
    $c_nav .= '</div>';
    $c_container .= '</ul>';

    return $c_links . $c_nav . $c_container;
}

// Get download folders
function getDirNav($path = 'wp-content/downloads', $level = 0)
{
    $ignore = array('cgi-bin', '.', '..');
    $dh = @opendir($path);
    while (false !== ($file = readdir($dh))) {
        if (!in_array($file, $ignore)) {
            if (is_dir($path . '/' . $file)) {
                if (!is_empty_dir($path . '/' . $file)) {
                    if (user_has_group($file)) {
                        $return[] = $file;
                    }
                }
            }
        }
    }
    closedir($dh);
    return $return;
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function ggp_sidebar_add_meta_boxes()
{

    add_meta_box(
        'ggp-sidebar-group',            // Unique ID
        esc_html__('Sidebar Menu', 'ggp-systems'),        // Title
        'ggp_sidebar_group_meta_box',        // Callback function
        'page',                    // Admin page (or post type)
        'side',                    // Context
        'default'                    // Priority
    );
}

/* Display the post meta box. */
function ggp_sidebar_group_meta_box($object, $box)
{
    $selected = get_post_meta($object->ID, 'ggp_sidebar_group', true);

    wp_nonce_field(basename(__FILE__), 'ggp_sidebar_group_nonce'); ?>

    <p><strong>Sidebar Menu</strong></p>
    <label class="screen-reader-text" for="ggp-sidebar-group">Sidebar Menu</label>
    <select name="ggp-sidebar-group" id="ggp-sidebar-group">
        <option value="">(no sidebar menu)</option>
        <option value="Products" <?php selected($selected, 'Products'); ?>>Products</option>
        <option value="Services" <?php selected($selected, 'Services'); ?>>Services</option>
        <option value="Sectors" <?php selected($selected, 'Sectors'); ?>>Sectors</option>
        <option value="Public" <?php selected($selected, 'Public'); ?>>Public</option>
        <option value="Members Only" <?php selected($selected, 'Members Only'); ?>>Members Only</option>
        <option value="About Us" <?php selected($selected, 'About Us'); ?>>About Us</option>
        <option value="How To Find Us" <?php selected($selected, 'How To Find Us'); ?>>How To Find Us</option>
    </select>
    <p><?php _e("Add this page to a sidebar menu.", 'ggp-systems'); ?></p>
<?php }

/* Save the meta box's post metadata. */
function ggp_sidebar_group_save_meta($post_id, $post)
{

    /* Verify the nonce before proceeding. */
    if (!isset($_POST['ggp_sidebar_group_nonce']) || !wp_verify_nonce($_POST['ggp_sidebar_group_nonce'], basename(__FILE__)))
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object($post->post_type);

    /* Check if the current user has permission to edit the post. */
    if (!current_user_can($post_type->cap->edit_post, $post_id))
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = (isset($_POST['ggp-sidebar-group']) ? $_POST['ggp-sidebar-group'] : '');

    /* Get the meta key. */
    $meta_key = 'ggp_sidebar_group';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta($post_id, $meta_key, true);

    /* If a new meta value was added and there was no previous value, add it. */
    if ($new_meta_value && '' == $meta_value)
        add_post_meta($post_id, $meta_key, $new_meta_value, true);

    /* If the new meta value does not match the old value, update it. */
    elseif ($new_meta_value && $new_meta_value != $meta_value)
        update_post_meta($post_id, $meta_key, $new_meta_value);

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ('' == $new_meta_value && $meta_value)
        delete_post_meta($post_id, $meta_key, $meta_value);
}

/* Add meta boxes on the 'add_meta_boxes_page' hook. */
add_action('add_meta_boxes_page', 'ggp_sidebar_add_meta_boxes');

/* Save post meta on the 'save_post' hook. */
add_action('save_post', 'ggp_sidebar_group_save_meta', 10, 2);

// Return file extension
function findexts($filename)
{
    $filename = strtolower($filename);
    $exts = split("[/\\.]", $filename);
    $n = count($exts) - 1;
    $exts = $exts[$n];
    return $exts;
}

// Checks if directory is empty
function is_empty_dir($dir)
{
    if (($files = @scandir($dir)) && count($files) <= 2) {
        return true;
    }
    return false;
}

// Get folder tree
function getDownloads($path, $folder, $type = 'all')
{
    $folders = '<ul>';
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path . $folder, RecursiveDirectoryIterator::KEY_AS_PATHNAME), RecursiveIteratorIterator::SELF_FIRST) as $file => $info) {
        $link = str_ireplace($path . $folder, 'wp-content/downloads/' . $folder, $file);
        if ($type == 'all') {
            if ($info->isDir()) {
                if (is_empty_dir($info)) {
                    $folders .= '<li class="folder">' . $info->getFilename() . '</li>';
                }
            } else {
                $ext = findexts($info->getFilename());
                $folders .= '<li class="file_' . $ext . '"><a href="' . $link . '" target="_blank">' . $info->getFilename() . '</a></li>';
            }
        } else if ($type == 'folders') {
            if ($info->isDir()) {
                $folders .= '<li>' . $info->getFilename() . '</li>';
            }
        } else if ($type == 'files') {
            if (!$info->isDir()) {
                $folders .= '<li>' . $info->getFilename() . '</li>';
            }
        }
    }
    $folders .= '</ul>';
    return $folders;
}

// RSS feeds for footer
function get_catefory_feed($cat, $max = 3)
{
    $feed = '<ul>';
    $postargs = array(
        'numberposts' => $max,
        'orderby' => 'date',
        'category' => $cat
    );
    $feed_posts = get_posts($postargs);
    if ($feed_posts[0]->post_content != '') {
        foreach ($feed_posts as $_post) {
            $firstdot = strpos($_post->post_content, '. ');
            if ($firstdot < 120 && $firstdot > 20) {
                $excerpt = substr(strip_tags($_post->post_content), 0, $firstdot);
            } else {
                $excerpt = substr(strip_tags($_post->post_content), 0, 90);
            }
            $feed .= '<li>';
            $feed .= '<h4>' . $_post->post_title . '</h4>';
            $feed .= '<p>' . $excerpt . '... <br /><a href="' . get_permalink($_post->ID) . '">Read more</a></p>';
            $feed .= '</li>';
        }
    } else {
        $feed .= '<li>';
        $feed .= '<h4>No posts to display</h4>';
        $feed .= '</li>';
    }
    $feed .= '</ul>';
    return $feed;
}

// Check if user is member of group
function user_has_group($groupname)
{
    global $wpdb;
    global $current_user;
    wp_get_current_user(); //get_currentuserinfo();
    $users_groups = $wpdb->get_results("SELECT * FROM wp1_user2group_rs WHERE user_id = '" . $current_user->ID . "';");
    $all_groups = $wpdb->get_results("SELECT * FROM wp1_groups_rs;");
    foreach ($all_groups as $agroup) {
        if (is_string($groupname)) {
            if (strtolower($agroup->group_name) == strtolower($groupname)) {
                $thisgroupid = $agroup->ID;
            }
        } else {
            $thisgroupid = $groupname;
        }
    }
    if (isset($thisgroupid)) {
        foreach ($users_groups as $group) {
            if ($group->group_id == $thisgroupid) {
                return true;
            }
        }
    } else {
        return false;
    }
    return false;
}

// Check if user is logged in
function isUserLoggedIn()
{
    global $current_user;
    if (0 == $current_user->ID) {
        return false;
    } else {
        return true;
    }
}

function check_excerpt()
{
    return true;
    global $wpdb;
    global $post;
    $excerpt = $wpdb->get_var($wpdb->prepare("SELECT post_excerpt FROM $wpdb->posts WHERE ID = '" . $post->ID . "';"));
    if ($excerpt != '') {
        return true;
    } else {
        return false;
    }
}

?>
