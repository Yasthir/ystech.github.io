<?php
/*
Plugin Name: GoogleAdsense
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/
function my_google_adsense_placement_function() {
  $output='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-3584861056458182",
    enable_page_level_ads: true
  });
</script>';
  echo $output;
}
add_action('wp_head','my_google_adsense_placement_function');