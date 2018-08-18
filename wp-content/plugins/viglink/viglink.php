<?php
/*
Plugin Name: VigLink
Version: 1.0.6
Description: The easiest way to monetize the links on your site.  Link directly to other sites, just like you do today.  VigLink automatically affiliates those links -- even links on posts you've already written -- with no extra editing!  Get stats on which links are making you the most money, which are most clicked, and more.
Author: VigLink
Author URI: http://www.viglink.com
License: GPLv3

* +--------------------------------------------------------------------------+
* | Copyright (c) 2010 VigLink, Inc.                                         |
* +--------------------------------------------------------------------------+
* | This program is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by     |
* | the Free Software Foundation; either version 2 of the License, or        |
* | (at your option) any later version.                                      |
* |                                                                          |
* | This program is distributed in the hope that it will be useful,          |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
* | GNU General Public License for more details.                             |
* |                                                                          |
* | You should have received a copy of the GNU General Public License        |
* | along with this program; if not, write to the Free Software              |
* | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
* +--------------------------------------------------------------------------+
*/

define( 'VIGLINK_MIN_WORDPRESS_REQUIRED', "2.7" );
define( 'VIGLINK_WORDPRESS_VERSION_SUPPORTED', version_compare( get_bloginfo( "version" ), VIGLINK_MIN_WORDPRESS_REQUIRED, ">=" ) );
define( 'VIGLINK_ENABLED', VIGLINK_WORDPRESS_VERSION_SUPPORTED && viglink_validate_option( 'key' ) );

/**
 * Print the VigLink <script/> tags (see: http://www.viglink.com/install)
 */
function viglnk_script() {
  $key = get_option( "key" );
  if( $key ) {
?>
  <!-- VigLink: http://viglink.com -->
  <script type="text/javascript">
    var vglnk = { key: '<?php print addslashes( $key ); ?>' };

    (function(d, t) {
      var s = d.createElement(t); s.type = 'text/javascript'; s.async = true;
      s.src = '//cdn.viglink.com/api/vglnk.js?key=' + vglnk.key;
      var r = d.getElementsByTagName(t)[0]; r.parentNode.insertBefore(s, r);
    }(document, 'script'));
  </script>
  <!-- end VigLink -->
<?php
  }
}

/**
 * Print the VigLink plugin settings page
 */
function viglink_options() { ?>
  <div class="wrap">
    <div class="icon32">&nbsp;</div>
    <h2>VigLink Settings</h2>
  <?php
    if( ! VIGLINK_WORDPRESS_VERSION_SUPPORTED ) {
  ?>
    <p style="width: 50%;">
      Thanks for your interest in VigLink!  Unfortunately, the VigLink plugin
      requires WordPress <?php print VIGLINK_MIN_WORDPRESS_REQUIRED ?> or newer.
      Please try again once you've upgraded.
    </p>
  <?php
    } else {
      if( get_option( "is-not-first-load" ) && ! viglink_validate_option( "key" ) ) {
    ?>
    <div class="error fade">
      <p>
        <strong>Invalid API Key.</strong>
        VigLink is disabled until you enter a valid API key.
      </p>
    </div>
  <?php
    }
  ?>
    <p class="instructions">
      Copy your API key from
      <a href="http://www.viglink.com/account">viglink.com</a> and paste it
      below, or <a href="#" id="viglink-fetch">click here</a> to retrieve it
      automatically.
    </p>

    <form method="post" action="options.php">
      <?php settings_fields( "viglink" ); ?>
      <table class="form-table" style="width: auto;">
        <tr valign="top">
          <th style="width: auto;">API Key</th>
          <td>
            <input id="viglink-key" type="text" name="key" value="<?php print get_option( "key" ); ?>" class="regular-text" maxlength="32"/>
            <span class="description">Required</span>
          </td>
        </tr>

    <?php
    if(viglink_validate_option( "key" )) {
    ?>
      <tr>
        <th style="width: auto;">
          Enable RSS link rewriting
        </th>
        <td>
            <input id="viglink-enable-rss-rewrites" type="checkbox" name="enable-rss-rewrites"
              <?php echo get_option( "enable-rss-rewrites" ) ? ' checked="checked" ' : '' ?> />
        </td>
      </tr>

    <?php
    }
    ?>

      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        <span class="reminder" style="display: none;">&larr; Don't forget to save!</span>
      </p>
    </form>

  </div>
<?php } ?>
  <p style="width: 50%; font-size: 0.8em;">
    Have questions or comments about VigLink or the plugin? Suggestions for
    something you'd like us to add? Please
    <a href="http://www.viglink.com/support">let us know</a>!
  </p>
<?php
}

/**
 * Validate an option value
 */
function viglink_validate_option( $name ) {
  $value = get_option( $name );

  switch( $name ) {
    case 'key':
      return preg_match( '/^[0-9a-f]{32}$/i', $value );
  }

  return true;
}

/**
 * Initialize admin-specific hooks and settings
 */
function viglink_admin_init() {
  wp_register_script( "viglink_admin_script", WP_PLUGIN_URL . "/viglink/viglink.js" );
  wp_register_style( "viglink_admin_style", WP_PLUGIN_URL . "/viglink/viglink.css" );

  // register settings for the options page
  register_setting( "viglink", "key", "viglink_sanitize_option" );
  register_setting( "viglink", "enable-rss-rewrites");
}

/**
 * Include javascript needed by the options page
 */
function viglink_admin_includes() {
  wp_enqueue_script( "viglink_admin_script" );
  wp_enqueue_style( "viglink_admin_style" );
}

/**
 * Add the options menu item to the sidebar settings panel
 */
function viglink_options_menu() {
  // add the options page to the settings menu
  $page = add_options_page( "VigLink Options", "VigLink", "manage_options", __FILE__, "viglink_options" );

  // include plugin-specific includes on the options page
  add_action( "admin_print_scripts-" . $page, "viglink_admin_includes" );
  add_action( "admin_print_styles-" . $page, "viglink_admin_includes" );
}

/**
 * Sanitize an options form field on submit
 */
function viglink_sanitize_option( $value ) {
  // now that the form has been submitted at least once, start showing the
  // error for an empty/invalid api key
  update_option( "is-not-first-load", true );

  return htmlspecialchars( $value );
}

function viglink_rss_rewrites_enabled() {
  if (viglink_validate_option('key')) {
    return (bool) get_option( 'enable-rss-rewrites' );
  } else {
    return false;
  }
}

// options

add_option( "is-not-first-load" );
add_option( "key" );
add_option( "enable-rss-rewrites" );

function viglink_get_click_url($url) {
  $scheme = "http" . ( strtolower( $_SERVER["HTTPS"] ) == "on" ? "s" : "" ) . "://";

  $loc = ( $_SERVER["HTTP_HOST"] && $_SERVER["REQUEST_URI"] ) ?
    ( $scheme . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) :
    null;

  $params = array(
    "format" => "go",
    "key" => get_option('key'),
    "loc" => $loc,
    "out" => $url,
    "ref" => $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : null,
    "title" => null,
    "txt" => null
  );

  return "http://apicdn.viglink.com/api/click?" . http_build_query( $params );
}

function viglink_is_external_link($url) {
  $parts = parse_url( $url );
  $link_host = strtolower( $parts['host'] );
  $server_host = strtolower( $_SERVER['HTTP_HOST'] );
  return $link_host && ( !$server_host || $server_host !== $link_host );
}

function viglink_rewrite_link($matches) {
  if (preg_match('/norewrite/i', $matches[0])) {
    return $matches[0];
  }

  $url = html_entity_decode($matches[2]);
  $url = viglink_is_external_link($url) ? viglink_get_click_url($url) : $url;

  return $matches[1] . $url;
}

function viglink_rewrite_links($html) {
  return preg_replace_callback( '/(<a[^>]*href=")([^"]+)/i', "viglink_rewrite_link", $html );
}

// hooks

function viglink_add_debug_info() {

?>
<!--
  VL Debug info:

  <?php print viglink_rss_rewrites_enabled() ?>

-->

<?php

}

add_action( 'template_redirect', 'viglink_rss_rewrite_hooks' );
function viglink_rss_rewrite_hooks() {
  if (viglink_rss_rewrites_enabled() ) {
    global $wp_version;

    if (version_compare($wp_version, '2.9', '<')) {
      if(is_feed()) {
        add_filter('the_content', 'vigink_rewrite_links');
      }
    } else {
      add_filter('the_content_rss', 'viglink_rewrite_links');
      add_filter('the_content_feed', 'viglink_rewrite_links');
    }
  }
}

//add_action('wp_footer', 'viglink_add_debug_info');

// register settings for the admin options page
add_action( "admin_init", "viglink_admin_init" );
// add a menu item to the "settings" sidebar menu
add_action( "admin_menu", "viglink_options_menu");

// add the <script/> tags to the footer
if( VIGLINK_ENABLED ) {
  add_action( "wp_footer", "viglnk_script" );
}
