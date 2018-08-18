<?php

/*
Plugin Name: Simple Adblock Notice
Plugin URI: http://techsini.com/our-wordpress-plugins/simple-adblock-notice
Description: Simple Adblock Notice plugin shows a popup to whitelist your website if Adblock plus browser extension is installed.
Version: 2.3.7
Author: Shrinivas Naik
Author URI: http://techsini.com
License: GPL V3
*/


/*
Copyright (C) 2018 Shrinivas Naik

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/.

*/


if(!class_exists('simple_adblock_notice') && !class_exists('simple_adblock_notice')){

    class simple_adblock_notice{

        private $options;

        public function __construct(){

            //Activate the plugin for first time
            register_activation_hook(__FILE__, array($this, "activate"));

            //Initialize settings page
            require_once(plugin_dir_path(__file__) . "settings.php");
            $simple_adblock_notice_settings = new simple_adblock_notice_settings();

            //Load scipts and styles
            add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'register_styles'));

            //Run the plugin in footer
            add_action('wp_footer', array($this, 'run_plugin'));

            //Store options in a variable
            $this->options = get_option( 'simple_adblock_notice_settings' );

            //Set cookie
            add_action( 'init', array($this,'simple_adblock_notice_cookie'));
        }


        public function activate(){

            //Set default options for the plugin
            $initial_settings = array(
                'san_enabled'    =>  '1',
                'noticeinterval'    =>  '5',
                'howtowhitelist'    =>  '0'
            );
            add_option("simple_adblock_notice_settings", $initial_settings);

        }

        public function deactivate(){

        }

        public function register_scripts(){
            wp_enqueue_script('jquery');
            wp_enqueue_script('SweetAlertJs', plugins_url( 'js/sweet-alert.min.js' , __FILE__ ),array( 'jquery' ));
        }

        public function register_styles(){
            wp_enqueue_style( 'SweetAlertCSS', plugins_url('css/sweet-alert.css', __FILE__) );
            wp_enqueue_style( 'StyleCSS', plugins_url('css/style.css', __FILE__) );

        }

        public function run_plugin() {

               $sanenabled = $this->options['san_enabled'];
               $showhowtowhitelisttutor = $this->options['howtowhitelist'];

               /*-----------------------------------------
               Check if plugin is enabled else return
               -----------------------------------------*/
               if($sanenabled != "1")
               return;

                //Check if can we show the popup (cookie) and continue with it

                if($this->can_show_popup() == TRUE) {

                    ?>

                    <script type="text/javascript">
                    jQuery(document).ready(function($) {

                        setTimeout(function() {
                            if ( typeof(window.google_jobrunner) === "undefined" ) {

                                //ad blocker installed - show notice

                                swal({
                                    title: "Adblocker Detected",
                                    text: "We have detected that you are using adblocking plugin in your browser. Please whitelist our website and refresh this page to hide this message",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#01A9DB",
                                    confirmButtonText: "Yes, I'll Whitelist",
                                    cancelButtonText: "Cancel",
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                },
                                function(isConfirm){
                                    if (isConfirm) {
                                        swal("Thank you", "You will not see this message once you whitelist our website on ad blocker. :)", "success");
                                        jQuery(".san_howtowhitelist").hide('slow');
                                    } else {
                                        swal("Oh..", "Oh.. Hope you whitelist our website in your ad blocker soon!", "error");
                                        jQuery(".san_howtowhitelist").hide('slow');
                                    }
                                });

                                <?php
                                if($showhowtowhitelisttutor == "1") { ?>
                                setTimeout(function() {
                                    jQuery(".san_howtowhitelist").css('display', 'flex');
                                },2000);
                                <?php
                                }
                                ?>
                            }

                        }, 10000);


                        jQuery(".san_howtowhitelist .san_howtowhitelist_btn").click(function(e) {
                            swal.close();
                            jQuery(".san_howtowhitelist").hide();
                            setTimeout(function() {
                                jQuery(".san_howtowhitelist_modal_wrapper").show();
                            },400);

                        });

                    });
                    </script>

                    <?php
                    if($showhowtowhitelisttutor == "1") { ?>
                        <div class="san_howtowhitelist">
                            <a class="san_howtowhitelist_btn" target="_blank">How to whitelist website on AdBlocker?</a>
                        </div>
                        <div class="san_howtowhitelist_modal_wrapper">
                            <div class="san_howtowhitelist_modal_window">

                                <h3>How to whitelist website on AdBlocker?</h3>

                                    <ol>
                                        <li>Click on the AdBlock Plus icon on the top right corner of your browser</li>
                                        <li>Click on "Enabled on this site" from the AdBlock Plus option</li>
                                        <li>Refresh the page and start browsing the site</li>
                                    </ol>

                                    <a title="Close" class="close-popup"></a>
                            </div>

                        </div>


                        <script>
                        jQuery(document).ready(function() {
                            jQuery(".san_howtowhitelist_modal_window .close-popup").click(function(e) {
                                jQuery(".san_howtowhitelist_modal_wrapper").animate({
                                    opacity : 0
                                }, 1500, function(){
                                    jQuery(".san_howtowhitelist_modal_wrapper").hide();
                                });
                            });

                        });

                        </script>
                    <?php
                    }
                    ?>
                    <?php

                }

        }

        public function can_show_popup(){

            if(!isset($_COOKIE['simple_adblock_notice'])){
                //Show the popup
                return true;
            } else {
                //Do not show the popup
                return false;
            }
        }

        public function simple_adblock_notice_cookie(){

            if(!isset($_COOKIE['simple_adblock_notice']) && !is_admin()){
                $noticeinterval = $this->options['noticeinterval'];
                setcookie("simple_adblock_notice", "shown", time()+ (60 * $noticeinterval) , COOKIEPATH, COOKIE_DOMAIN, false);
            }
        }

    }

}

$simple_adblock_notice = new simple_adblock_notice();

?>
