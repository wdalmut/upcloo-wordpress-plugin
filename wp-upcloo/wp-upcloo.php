<?php
/*
Plugin Name: UpCloo WP Plugin
Plugin URI: http://www.upcloo.com/
Description: UpCloo is a cloud based and fully hosted indexing engine that helps you  to create incredible and automatic correlations between contents of your website.
Version: 1.2.0-Gertrude
Author: Corley S.r.l.
Author URI: http://www.corley.it/
License: MIT
*/

/*
 * Copyright (C) 2012 by Corley SRL
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'On');

load_plugin_textdomain('wp_upcloo', null, basename(dirname(__FILE__)));

require_once dirname(__FILE__) . '/UpCloo/Widget/Partner.php';
require_once dirname(__FILE__) . '/UpCloo/Widget/Search.php';
require_once dirname(__FILE__) . '/UpCloo/Registry.php';
require_once dirname(__FILE__) . '/vendor/upcloo-sdk/src/UpCloo/Autoloader.php';

require_once dirname(__FILE__) . '/SView.php';

//Only secure protocol on post/page publishing (now is beta test... no https)
define("UPCLOO_USERKEY", "upcloo_userkey");
define("UPCLOO_SITEKEY", "upcloo_sitekey");
define("UPCLOO_PASSWORD", 'upcloo_password');
define("UPCLOO_INDEX_CATEGORY", 'upcloo_index_category');
define("UPCLOO_INDEX_TAG", "upcloo_index_tag");
define('UPCLOO_REWRITE_PUBLIC_LABEL', 'upcloo_rewrite_public_label');
define('UPCLOO_MAX_SHOW_LINKS', "upcloo_max_show_links");
define("UPCLOO_POST_PUBLISH", "publish");
define("UPCLOO_POST_TRASH", "trash");
define("UPCLOO_RSS_FEED", "http://www.mxdesign.it/contenuti/rss/0/news.xml");
define("UPCLOO_POST_META", "upcloo_post_sent");
define("UPCLOO_CLOUD_IMAGE", '<img src="'.WP_PLUGIN_URL.'/wp-upcloo/upcloo.png" src="UpCloo-OK" />');
define("UPCLOO_NOT_CLOUD_IMAGE", '<img src="'.WP_PLUGIN_URL.'/wp-upcloo/warn.png" src="UpCloo-Missing" />');
define("UPCLOO_DEFAULT_LANG", "upcloo_default_language");
define('UPCLOO_META_LANG', 'upcloo_language_field');
define('UPCLOO_ENABLE_MAIN_CORRELATION', "upcloo_enable_main_correlation");
define('UPCLOO_DISABLE_MAIN_CORRELATION_COMPLETELY', "upcloo_disable_main_correlation_completely");
define('UPCLOO_MISSING_IMAGE_PLACEHOLDER', 'upcloo_missing_image_placeholder');
define('UPCLOO_POSTS_TYPE', "upcloo_posts_type");
define('UPCLOO_SUMMARY_LEN', 'upcloo_summary_len');

define('UPCLOO_ENABLE_VSITEKEY_AS_PRIMARY', "upcloo_enable_vsitekey_as_primary");
define('UPCLOO_VSITEKEY_AS_PRIMARY', "upcloo_vsitekey_as_primary");

define('UPCLOO_TEMPLATE_BASE', 'upcloo_template_base');
define('UPCLOO_TEMPLATE_SHOW_TITLE', 'upcloo_template_show_title');
define('UPCLOO_TEMPLATE_SHOW_FEATURED_IMAGE', 'upcloo_template_show_featured_image');
define('UPCLOO_TEMPLATE_SHOW_SUMMARY','upcloo_template_show_summary');
define('UPCLOO_TEMPLATE_SHOW_TAGS', 'upcloo_template_show_tags');
define('UPCLOO_TEMPLATE_SHOW_CATEGORIES', 'upcloo_template_show_categories');

define('UPCLOO_USER_DEFINED_TEMPLATE_FUNCTION', "upcloo_user_template_callback");
define('UPCLOO_USER_WIDGET_CALLBACK', 'upcloo_user_widget_callback');

define('UPCLOO_ENABLE_TEMPLATE_REMOTE_META', 'upcloo_enable_template_remote_meta');

define('UPCLOO_SITEMAP_PAGE', 'upcloo_sitemap');

define('UPCLOO_SEARCH_WIDGET_ID', 'upcloo_search_widget');

define('UPCLOO_SEARCH_THEME', 'search-result.php');
define("UPCLOO_SEARCH_RESULTS", 10);

define('UPCLOO_MENU_SLUG', 'upcloo_options_menu');
define('UPCLOO_MENU_KSWITCH_SLUG', 'upcloo_options_menu_kswitch');
define('UPCLOO_MENU_FEATURE_SLUG', 'upcloo_options_menu_feature');
define('UPCLOO_MENU_POST_TYPE_SLUG', 'upcloo_options_menu_post_type');
define('UPCLOO_MENU_ROI_SLUG', 'upcloo_options_menu_roi');
define('UPCLOO_MENU_THEME_SLUG', 'upcloo_options_menu_slug');
define('UPCLOO_MENU_REMOTE', 'upcloo_options_menu_remote');

define('UPCLOO_VIEW_PATH', dirname(__FILE__) . '/views');

define('UPCLOO_OPTION_CAPABILITY', 'manage_options');

add_action("admin_init", "upcloo_init");
add_action('add_meta_boxes', 'upcloo_add_custom_box');
add_action('widgets_init', create_function( '', 'register_widget("UpCloo_Widget_Partner");'));
add_action('widgets_init', create_function( '', 'register_widget("UpCloo_Widget_Search");'));
add_action('post_submitbox_misc_actions', 'upcloo_add_force_content_send_link');
add_action('manage_posts_custom_column',  'upcloo_my_show_columns');
add_action('manage_pages_custom_column',  'upcloo_my_show_columns');
add_action('save_post', 'upcloo_save_data');
add_action('wp_dashboard_setup', 'upcloo_add_dashboard_widgets' );

add_action('wp_head', 'upcloo_wp_head');
add_action('admin_notices', 'upcloo_show_needs_attention');

add_filter('the_content', 'upcloo_content');
add_filter('admin_footer_text', "upcloo_admin_footer");
add_filter('manage_pages_columns', 'upcloo_my_columns');
add_filter('manage_posts_columns', 'upcloo_my_columns');

add_action("template_redirect", "upcloo_search_result_template");

add_action( 'admin_menu', 'upcloo_plugin_menu' );

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'upcloo_install');

/* Runs on plugin deactivation*/
register_deactivation_hook(__FILE__, 'upcloo_remove');

//If have to show meta values
if (get_option(UPCLOO_ENABLE_TEMPLATE_REMOTE_META, "wp_upcloo")) {
    //Add sitemap page
    if (array_key_exists("plugin_page", $_GET) && $_GET['plugin_page'] == UPCLOO_SITEMAP_PAGE) {
        add_action('template_redirect', 'upcloo_sitemap_page');
    }
}

function upcloo_is_configured()
{
    $postTypes = get_option(UPCLOO_POSTS_TYPE);
    if (!is_array($postTypes)) {
        $postTypes = array();
    }

    if (
        trim(get_option(UPCLOO_USERKEY)) != '' &&
        trim(get_option(UPCLOO_PASSWORD)) != '' &&
        trim(get_option(UPCLOO_SITEKEY)) != '' &&
        count($postTypes) > 0
    ) {
        return true;
    } else {
        return false;
    }
}

function upcloo_show_needs_attention()
{
    if (!upcloo_is_configured()) {
        echo '<div class="updated">
        <p>' . __("Remember that your have to configure UpCloo Plugin: ", "wp_upcloo") . ' <a href="admin.php?page=upcloo_options_menu">'.__("Base Config Page", "wp_upcloo") . '</a> - <a href="admin.php?page=upcloo_options_menu_post_type">'. __("Content Types Selection", "wp_upcloo") . '</a></p>
        </div>';
    }
}

function upcloo_check_menu_capability()
{
    if ( !current_user_can(UPCLOO_OPTION_CAPABILITY) )  {
        wp_die(__( 'You do not have sufficient permissions to access this page.', "wp_upcloo"));
    }
}

//Start menu
function upcloo_plugin_menu()
{
    add_menu_page('UpCloo', __('UpCloo', "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_SLUG, 'upcloo_plugin_options');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo Post Type", __("Post Type Indexing", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_POST_TYPE_SLUG, 'upcloo_plugin_menu_post_type');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo Templating", __("Templating System", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_THEME_SLUG, 'upcloo_plugin_menu_theme');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo ROI Monitor", __("ROI Monitor", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_ROI_SLUG, 'upcloo_plugin_menu_roi');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo Indexing Feature", __("Indexing Feature", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_FEATURE_SLUG, 'upcloo_plugin_menu_feature');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo Remote Importer", __("Remote Importer", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_REMOTE_SLUG, 'upcloo_plugin_menu_remote');
    add_submenu_page(UPCLOO_MENU_SLUG, "UpCloo Key Switch", __("Key Switch", "wp_upcloo"), UPCLOO_OPTION_CAPABILITY, UPCLOO_MENU_KSWITCH_SLUG, 'upcloo_plugin_menu_kswitch');
}

function upcloo_plugin_menu_remote()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-remote-options.php";
}

function upcloo_plugin_menu_theme()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-theme-options.php";
}

function upcloo_plugin_menu_roi()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-roi-options.php";
}

function upcloo_plugin_menu_post_type()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-post-type-options.php";
}

function upcloo_plugin_menu_feature()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-feature-options.php";
}

function upcloo_plugin_menu_kswitch()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-key-switch-options.php";
}

function upcloo_plugin_options()
{
    upcloo_check_menu_capability();
    include realpath(dirname(__FILE__)) . "/options/app-config-options.php";
}
//End menu

/**
 * Generate a sitemap for UpCloo Remote Importer
 */
function upcloo_sitemap_page()
{
    header ("content-type: text/xml");

    $view = new SView();
    $view->setViewPath(UPCLOO_VIEW_PATH);

    $view->userSelected = get_option(UPCLOO_POSTS_TYPE);

    echo $view->render("sitemap.phtml");
    exit;
}


/**
 * Get Taxonomies
 *
 * @param int $pid The post ID
 * @return array Taxonomies custom
 */
function upcloo_get_taxonomies($pid)
{
    //For taxonomies remove builtin and elements must public
    $taxonomies_data = array();

    $args = array(
		'public'   => true,
      	'_builtin' => false
    );
    $taxonomies = get_taxonomies($args,'names', 'and');
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_post_terms($pid, $taxonomy);
        $taxonomies_data[$taxonomy] = array();
        foreach ($terms as $term) {
            $taxonomies_data[$taxonomy][] = $term->name;
        }
    }

    return $taxonomies_data;
}

/**
 * Use only in single.php
 *
 * Call this function only in single.php theme file
 *
 * @return string The content to attach into head.
 */
function upcloo_wp_head()
{
    $metas = '';

    if (get_option(UPCLOO_ENABLE_TEMPLATE_REMOTE_META, "wp_upcloo")) {

        $postTypes = get_option(UPCLOO_POSTS_TYPE);
        if (!is_array($postTypes)) {
            $postTypes = array();
        }


        if (is_single()) {
            $m = array();

            global $post;

            //TODO: refactor...
            if (!in_array($post->post_type, $postTypes)) {
                return;
            }

            $publish_date = $post->post_date;
            $publish_date = str_replace(" ", "T", $publish_date) . "Z";

            $m[] = '<!-- UPCLOO_POST_ID '.$post->post_type . "_" . $post->ID.' UPCLOO_POST_ID -->';
            $m[] = '<!-- UPCLOO_POST_TYPE '.$post->post_type.' UPCLOO_POST_TYPE -->';
            $m[] = '<!-- UPCLOO_POST_TITLE '.$post->post_title.' UPCLOO_POST_TITLE -->';
            $m[] = '<!-- UPCLOO_POST_PUBLISH_DATE '.$publish_date.' UPCLOO_POST_PUBLISH_DATE -->';

            $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
            if ($image) {
            	if (is_array($image)) {
            		$image = $image[0];
            	}
                $m[] = '<!-- UPCLOO_POST_IMAGE '.$image.' UPCLOO_POST_IMAGE -->';
            }

            $firstname = get_user_meta($post->post_author, "first_name", true);
            $lastname = get_user_meta($post->post_author, "last_name", true);

            $m[] = '<!-- UPCLOO_POST_AUTHOR '.$firstname . " " . $lastname .' UPCLOO_POST_AUTHOR -->';

            //LANG!
            $lang = get_post_meta($post->ID, UPCLOO_META_LANG, true);
            if ($lang && !empty($lang)) {
                $m[] = '<!-- UPCLOO_POST_LANG ' . $lang . ' UPCLOO_POST_LANG -->';
            }

            $dynTags = array();
            $taxonomies = upcloo_get_taxonomies($post->ID);
            if (is_array($taxonomies)) {
                $taxonomiesArray = array();
                foreach ($taxonomies as $slug => $taxonomy) {
                    foreach ($taxonomy as $element) {
                        $taxonomiesArray[] = $element;
                    }
                    $m[] = '<!-- UPCLOO_POST_DYNAMIC_TAG_'.strtoupper($slug).' '.implode(",", $taxonomiesArray).' UPCLOO_POST_DYNAMIC_TAG_'.strtoupper($slug).' -->';
                    $dynTags[] = $slug;
                }
            }
            $m[] = '<!-- UPCLOO_POST_DYNAMIC_TAG_LIST ' . implode(",", $dynTags) . ' UPCLOO_POST_DYNAMIC_TAG_LIST -->';

            $tags = get_the_tags($post->ID);
            if (is_array($tags)) {
                $elements = array();
                foreach ($tags as $element) {
                    $elements[] = $element->name;
                }
                $m[] = '<!-- UPCLOO_POST_TAGS '.implode(",", $elements).' UPCLOO_POST_TAGS -->';
            }

            $categories = get_the_category($post->ID);
            if (is_array($categories)) {
                $elements = array();
                foreach ($categories as $element) {
                    $elements[] = $element->name;
                }
                $m[] = '<!-- UPCLOO_POST_CATEGORIES '.implode(",", $elements).' UPCLOO_POST_CATEGORIES -->';
            }


            $metas = implode(PHP_EOL, $m) . PHP_EOL;
        }
    }

    echo $metas;
}

/**
 * During post update you can resend the content
 * to the UpCloo cloud system.
 */
function upcloo_add_force_content_send_link()
{
    global $post_ID;
    $post = get_post( $post_ID );

    if ($post->post_status == 'publish') {
        $upclooMeta = get_post_meta($post->ID, UPCLOO_POST_META, true);

        $view = new SView();
        $view->setViewPath(UPCLOO_VIEW_PATH);

        $view->upclooMeta = $upclooMeta;
        $view->post = $post;

        echo $view->render('add-force-content-send-link.phtml');
    }
}

function upcloo_my_columns($columns)
{
    $columns['upcloo'] = "UpCloo";

    if ($_GET["upcloo"] == 'reindex') {
        $upClooMeta = get_post_meta($_GET["post"], UPCLOO_POST_META, true);

        if (upcloo_content_sync($_GET["post"])) {
            update_post_meta($_GET["post"], UPCLOO_POST_META, "1", $upClooMeta);
            echo "<div class='updated fade'><p>" . __("Content correctly sent to UpCloo.", "wp_upcloo") . "</p></div>";
        } else {
            echo "<div class='error fade'><p>" . __("Unable to send this content to UpCloo.", "wp_upcloo") . "</p></div>";
        }
    }

    return $columns;
}

function upcloo_my_show_columns($name)
{
    global $post;

    switch ($name) {
        case 'upcloo':
            $upclooSent = get_post_meta($post->ID, UPCLOO_POST_META, true);
            $image = (($upclooSent == '1') ? UPCLOO_CLOUD_IMAGE : UPCLOO_NOT_CLOUD_IMAGE);

            //Only how can edit pages can send to upcloo...
            if (current_user_can("edit_posts") || current_user_can('edit_pages')) {
                echo "<a href='?post={$post->ID}&upcloo=reindex'>" . $image . '</a>';
            } else {
                echo $image;
            }

            break;
    }
}

// Create the function to output the contents of our Dashboard Widget
function upcloo_dashboard_widget_function()
{
    // Display whatever it is you want to show
    $xml = simplexml_load_file(UPCLOO_RSS_FEED);

    $blogInfo = get_bloginfo();
    $blogTitle = urlencode(strtolower($blogInfo));

    $view = new SView();
    $view->setViewPath(UPCLOO_VIEW_PATH);

    $view->xml = $xml;
    $view->blogTitle = $blogTitle;
    $view->blogInfo = $blogInfo;

    echo $view->render("dashboard-widget.phtml");
}

// Create the function use in the action hook

function upcloo_add_dashboard_widgets()
{
    wp_add_dashboard_widget('upcloo_dashboard_widget', __('UpCloo News Widget', "wp_upcloo"), 'upcloo_dashboard_widget_function');
}

function upcloo_admin_footer($text)
{
    return $text . " • <span><a target=\"_blank\" href='http://www.upcloo.com'>UpCloo Inside</a></span>";
}

/* Adds a box to the main column on the Post and Page edit screens */
function upcloo_add_custom_box() {

    $selected = get_option(UPCLOO_POSTS_TYPE);

    if (!$selected) {
        $selected = array();
    }

    if (is_array($selected)) {
        foreach ($selected as $key => $value) {
            add_meta_box(
                'upcloo_language_metabox',
                __( 'UpCloo Language Definer', 'wp_upcloo' ),
                'upcloo_inner_custom_box',
                $key
            );
        }
    }
}

/**
 * @todo remove custom box feature.
 */
function upcloo_inner_custom_box()
{
    global $post;

    $metadataLang = get_post_meta($post->ID, UPCLOO_META_LANG, true);

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'upcloo_language_metabox_nonce' );

    $view = new SView();
    $view->setViewPath(UPCLOO_VIEW_PATH);

    $view->metadataLang = $metadataLang;

    echo $view->render("inner-custom-box.phtml");
}

/**
 * Intialize the plugin
 */
function upcloo_init()
{
    /* Engaged on delete post */
    if (current_user_can('delete_posts')) {
        add_action('trash_post', 'upcloo_remove_post');
    }
}

function upcloo_remove_post()
{
    $post = get_post($pid);

    $postsType = get_option(UPCLOO_POSTS_TYPE);

    if (in_array($post->post_type, $postsType)) {
        if ($post->post_status == UPCLOO_POST_PUBLISH) {
            $manager = UpCloo_Manager::getInstance();

            $manager->setCredential(get_option(UPCLOO_USERKEY), get_option(UPCLOO_SITEKEY), get_option(UPCLOO_PASSWORD));

            $pid = $post->post_type . "_" . $post->ID;
            $result = $manager->delete($pid);
            if ($result) {
                update_post_meta($post->ID, UPCLOO_POST_META, "0");
            }

            //TODO: handle result display
        }
    }
}

function upcloo_save_data($post_id)
{
    global $meta_box;

    $new = $_POST[UPCLOO_META_LANG];
    $old = get_post_meta($post_id, UPCLOO_META_LANG, true);

    if ('' == $new && $old) {
        delete_post_meta($post_id, $new, $old);
    } else {
        update_post_meta($post_id, UPCLOO_META_LANG, $new);
    }
}

/**
 * Mantain updated the contents
 *
 * @param int $pid The content PID
 * @return boolean if the content is indexed.
 */
function upcloo_content_sync($pid)
{
    $post = get_post($pid);
    $language = get_post_meta($post->ID, UPCLOO_META_LANG, true);

    $postsType = get_option(UPCLOO_POSTS_TYPE);

    /* Check if the content must be indexed */
    if (in_array($post->post_type, $postsType)) {
        if ($post->post_status == UPCLOO_POST_PUBLISH) {
            $categories = array();
            $tags = array();

            $permalink = get_permalink($pid);

            if (get_option("upcloo_index_category") == "1") {
                $categories = get_the_category($pid);
            }

            if (get_option("upcloo_index_tag") == "1") {
                $tags = get_the_tags($pid);
            }

            //For taxonomies remove builtin and elements must public
            $taxonomies_data = array();

            $args=array(
				'public'   => true,
              	'_builtin' => false
            );
            $taxonomies = get_taxonomies($args,'names', 'and');
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($pid, $taxonomy);

                $taxonomies_data[$taxonomy] = array();
                foreach ($terms as $term) {
                    $taxonomies_data[$taxonomy][] = $term->name;
                }
            }

            $firstname = get_user_meta($post->post_author, "first_name", true);
            $lastname = get_user_meta($post->post_author, "last_name", true);

            $publish_date = $post->post_date;
            $publish_date = str_replace(" ", "T", $publish_date) . "Z";//TODO: add real date support

            $summary = $post->post_excerpt;

            //If no summary
            if (empty($summary)) {
                //Cut the first part of text
                //and use it as a summary
                $content = strip_tags($post->post_content);

                //Get the max summary len
                $len = upcloo_get_min_summary_len();
                if (strlen($content) > $len) {
                    $pos = strpos($content, ".", $len);
                    if ($pos === false) {
                        //No dot... what I do?
                        $summary = substr($content, 0, $len); // I fill the summary with content.
                    } else {
                        $summary = substr($content, 0, $pos+1);
                    }
                } else {
                    $summary = $content;
                }
            }

            $model = array(
                "id" => base64_encode($permalink),
                "sitekey" => get_option("upcloo_sitekey"),
                "password" => get_option("upcloo_password"),
                "title" => $post->post_title,
                "content" => strip_tags($post->post_content),
                "summary" => $summary,
                "publish_date" => $publish_date,
                "type" => $post->post_type,
                "url" => $permalink,
                "author" => $firstname . " " . $lastname,
                "categories" => array(),
                "tags" => array()
            );

            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
            if ($image) {
                $model['image'] = $image[0];
            }

            if ($language != '') {
                $model["lang"] = $language;
            }

            if ($categories) {
                foreach ($categories as $category) {
                    $model["categories"][] = $category->name;
                }
            }

            if ($tags) {
                foreach ($tags as $tag) {
                    $model["tags"][] = $tag->name;
                }
            }

            if (is_array($taxonomies_data) && count($taxonomies_data)) {
                $model["dynamics_tags"] = $taxonomies_data;
            }

            $manager = UpCloo_Manager::getInstance();
            $manager->setCredential(get_option(UPCLOO_USERKEY), get_option(UPCLOO_SITEKEY), get_option(UPCLOO_PASSWORD));

            return $manager->index($model);
        }
    }
}

/**
 *
 * Get the max summary len
 *
 * @return int The max summary len
 */
function upcloo_get_min_summary_len()
{
    $len = get_option(UPCLOO_SUMMARY_LEN);

    if (is_numeric($len)) {
        $len = (int)$len;

        if ($len <= 0) {
            $len = 120;
        }
    } else {
        $len = 120;
    }

    return $len;
}

/**
 * Configure options
 *
 * This method configure options for UpCLoo
 * it is called during UpCloo plugin activation
 */
function upcloo_install() {
    /* Creates new database field */
    add_option(UPCLOO_USERKEY, "", "", "yes");
    add_option(UPCLOO_SITEKEY, "", "", "yes");
    add_option(UPCLOO_PASSWORD, "", "", "no");
    add_option(UPCLOO_INDEX_CATEGORY, "1", "", "no");
    add_option(UPCLOO_INDEX_TAG, "1", "", "no");
    add_option(UPCLOO_MAX_SHOW_LINKS, "10", "", "yes");
    add_option(UPCLOO_DEFAULT_LANG, "it", "", "yes");
    add_option(UPCLOO_ENABLE_MAIN_CORRELATION, "1", "", "yes");
    add_option(UPCLOO_DISABLE_MAIN_CORRELATION_COMPLETELY, '0', '', 'yes');
    add_option(UPCLOO_MISSING_IMAGE_PLACEHOLDER, '', '', 'yes');
    add_option(UPCLOO_POSTS_TYPE, '', '', 'yes');
    add_option(UPCLOO_SUMMARY_LEN, '', '', 'no');
    add_option(UPCLOO_REWRITE_PUBLIC_LABEL, '','', 'yes');
    add_option(UPCLOO_ENABLE_VSITEKEY_AS_PRIMARY,'','','no');
    add_option(UPCLOO_VSITEKEY_AS_PRIMARY,'','','no');
    add_option(UPCLOO_TEMPLATE_BASE,'','','yes');
    add_option(UPCLOO_TEMPLATE_SHOW_TITLE,'','','yes');
    add_option(UPCLOO_TEMPLATE_SHOW_FEATURED_IMAGE,'','','yes');
    add_option(UPCLOO_TEMPLATE_SHOW_SUMMARY,'','','yes');
    add_option(UPCLOO_TEMPLATE_SHOW_TAGS,'','','yes');
    add_option(UPCLOO_TEMPLATE_SHOW_CATEGORIES,'','','yes');
    add_option(UPCLOO_ENABLE_TEMPLATE_REMOTE_META, '0', '', 'yes');
}

/**
 * Remove all options
 *
 * This method remove all UpCloo option
 * it is called by disable plugin action.
 */
function upcloo_remove() {
    /* Deletes the database field */
    delete_option(UPCLOO_USERKEY);
    delete_option(UPCLOO_SITEKEY);
    delete_option(UPCLOO_PASSWORD);
    delete_option(UPCLOO_INDEX_CATEGORY);
    delete_option(UPCLOO_INDEX_TAG);
    delete_option(UPCLOO_MAX_SHOW_LINKS);
    delete_option(UPCLOO_DEFAULT_LANG);
    delete_option(UPCLOO_ENABLE_MAIN_CORRELATION);
    delete_option(UPCLOO_DISABLE_MAIN_CORRELATION_COMPLETELY);
    delete_option(UPCLOO_MISSING_IMAGE_PLACEHOLDER);
    delete_option(UPCLOO_REWRITE_PUBLIC_LABEL);
    delete_option(UPCLOO_POSTS_TYPE);
    delete_option(UPCLOO_SUMMARY_LEN);
    delete_option(UPCLOO_ENABLE_VSITEKEY_AS_PRIMARY);
    delete_option(UPCLOO_VSITEKEY_AS_PRIMARY);
    delete_option(UPCLOO_TEMPLATE_BASE);
    delete_option(UPCLOO_TEMPLATE_SHOW_TITLE);
    delete_option(UPCLOO_TEMPLATE_SHOW_FEATURED_IMAGE);
    delete_option(UPCLOO_TEMPLATE_SHOW_SUMMARY);
    delete_option(UPCLOO_TEMPLATE_SHOW_TAGS);
    delete_option(UPCLOO_TEMPLATE_SHOW_CATEGORIES);
    delete_option(UPCLOO_ENABLE_TEMPLATE_REMOTE_META);
}

/**
 * Get content on public side
 *
 * Get the content on public side with UpCloo
 * related posts or other contents.
 *
 * You can disable the post body using the $noPostBody
 * parameter. This parameter is used only if you
 * call the UpCloo call back by hand.
 *
 * @param string $content The original post content
 * @param boolean $noPostBody disable original post content into response
 *
 * @return string The content rewritten using UpCloo
 */
function upcloo_content($content, $noPostBody = false)
{
    global $post;
    global $current_user;

    get_currentuserinfo();

    $postTypes = get_option(UPCLOO_POSTS_TYPE);
    if (!is_array($postTypes)) {
        $postTypes = array();
    }

    if (get_option(UPCLOO_ENABLE_TEMPLATE_REMOTE_META, "wp_upcloo")) {
        //TODO: this part is written twice... clear...
        if ((is_single($post) && (in_array($post->post_type, $postTypes)))) {
            $content = "<!-- UPCLOO_POST_CONTENT -->{$content}<!-- UPCLOO_POST_CONTENT -->";
        }
    }

    $original = $content;

    $upClooMeta = get_post_meta($post->ID, UPCLOO_POST_META, true);

    if (get_option(UPCLOO_DISABLE_MAIN_CORRELATION_COMPLETELY) == "1") {
        return $content;
    }

    /**
     * Check if the content is single
     *
     * @todo refactor this check
     * Use a filter login to perform this kind of selection
     *
     * Check if UpCloo is enabled
     */
    if (
        (is_single($post) && (in_array($post->post_type, $postTypes)))
        &&
        (get_option(UPCLOO_ENABLE_MAIN_CORRELATION) || (!get_option(UPCLOO_ENABLE_MAIN_CORRELATION) && $current_user->has_cap('edit_users'))))
    {
        /**
         * If not sent to upcloo send it and store the result.
         *
         * Only not logged in user can send to UpCloo in automode
         */
        if (!$current_user->id && $upClooMeta == '') {
            if (upcloo_content_sync($post->ID)) {
                update_post_meta($post->ID, UPCLOO_POST_META, "1", $upClooMeta);
            }
        }

        $view = new SView();
        $view->setViewPath(UPCLOO_VIEW_PATH);

        $view->permalink = get_permalink($post->ID);
        $view->sitekey = get_option(UPCLOO_SITEKEY);
        $view->headline = (!(get_option(UPCLOO_REWRITE_PUBLIC_LABEL)) || trim(get_option(UPCLOO_REWRITE_PUBLIC_LABEL)) == '')
            ? __("Maybe you are interested at", "wp_upcloo")
            :  get_option(UPCLOO_REWRITE_PUBLIC_LABEL);


        $content .= $view->render("upcloo-js-sdk.phtml");
    }


    return $content;
}

/**
 * Get base domain path of an url
 *
 * @param string $url
 * @return boolean The base url in case of success, false otherwise.
 */
function upcloo_is_external_site($url)
{
    $urlSchema = @parse_url($url);
    if ($urlSchema) {
        if ($urlSchema["host"] == $_SERVER["SERVER_NAME"]) {
            return false;
        } else {
            return true;
        }
    }

    return true;
}

function upcloo_search_result_template()
{
    if (!empty($_GET["s"]) && is_active_widget(false, false, UPCLOO_SEARCH_WIDGET_ID, true)) {

        //Base direcetory name
        $themeFile = get_theme_root() . DIRECTORY_SEPARATOR . basename(get_bloginfo('template_directory')) . DIRECTORY_SEPARATOR . UPCLOO_SEARCH_THEME;


        $manager = UpCloo_Manager::getInstance();
        $manager->setCredential(get_option(UPCLOO_USERKEY), get_option(UPCLOO_SITEKEY), get_option(UPCLOO_PASSWORD));

        $query = $_GET["s"];

        $page = (array_key_exists("page", $_GET) ? $_GET["page"] : 1);

        $query = $manager->search()
            ->query($query)
            ->relevancy("date")
            ->numPerPage(UPCLOO_SEARCH_RESULTS)
            ->page($page);

        //TODO: handle facets and ranges
//         if (get_option("UPCLOO_INDEX_CATEGORY") == "1") {
//             $query->facet("category");
//         }

        $results = $manager->get($query);

        UpCloo_Registry::getInstance()->set("results", $results);

        //If user rewrite the search template load it.
        if (file_exists($themeFile)) {
            include $themeFile;
        } else {
            include dirname(__FILE__) . '/search-result.php';
        }
        exit;
    }
}

function upcloo_suggests($results, $search)
{
    if (count($results->getSuggestions()) > 0) {
        $s = $results->getSuggestions();
        $q = explode(" ", $search);

        foreach ($q as $i => $t) {
            if (array_key_exists($t, $s)) {
                $q[$i] = $s[$t][0];
            }
        }

        return implode(" ", $q);
    } else {
        return "";
    }
}

function upcloo_search_have_pages($results)
{
    $elements = $results->getCount();

    $pages = ceil($elements / UPCLOO_SEARCH_RESULTS);

    return $pages;
}

function upcloo_search_paginator($results)
{
    $pages = upcloo_search_have_pages($results);

    if ($pages) {
        $p = array();
        for ($i=1; $i<=$pages; $i++) {
            $p[] = "<a href=\"/?s={$_GET["s"]}&page={$i}\">{$i}</a>";
        }
        $pages = implode(" ", $p);
    } else {
        $pages = '';
    }

    return $pages;
}
