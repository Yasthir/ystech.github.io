<?php

class simple_adblock_notice_settings {

    //Holds the values to be used in the fields callbacks
    private $options;

    public function __construct(){

        add_action("admin_menu", array($this,"add_plugin_menu"));
        add_action("admin_init", array($this,"page_init"));

        add_action('admin_enqueue_scripts', array($this, 'san_register_admin_scripts'));
    }

    public function add_plugin_menu(){

        add_options_page( "Simple Adblock Notice", //page_title
                         "Simple Adblock Notice", //menu_title
                         "administrator", //capability
                         "simple-adblock-notice-settings", //menu_slug
                         array($this, "create_admin_page")); //callback function

    }

    public function san_register_admin_scripts(){
       wp_enqueue_style( 'san-custom-admin-style', plugins_url('css/san-admin-css.css', __FILE__));
   }

    public function create_admin_page(){

        $this->options = get_option( 'simple_adblock_notice_settings' );

        ?>
            <div class="wrap">

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">


                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <div class="postbox">
                                    <h3><span class="dashicons dashicons-admin-generic"></span>Simple Adblock Notice Settings</h3>
                                    <div class="inside">
                                        <form method="post" action="options.php">
                                            <?php
                                            // This prints out all hidden setting fields
                                            settings_fields( 'simple_adblock_notice_settings_group' ); //option group
                                            do_settings_sections( 'simple-adblock-notice-settings' ); //settings page slug
                                            submit_button(); ?>

                                            <br>
                                            <a href="http://techsini.com/google-chrome-built-in-ad-blocker-is-live-will-it-block-the-ads-on-my-website/">Google Chrome’s Built-in AdBlocker is Live – Will it Block the Ads on My Website?</a>
                                            <br>
                                            
                                            <br>
                                            <div class="info-msg"><b>Disable this plugin if you do not have the ad on your website.</b></div>
                                            <br>

                                        </form>
                                    </div>
                                </div>
								<div class="postbox">
									<h2>Upgrade to PRO</h2>
									<div class="inside">
                                        <h4>Upgrade to PRO and get the following features</h4>
										<ul class="san-upgrade-to-pro">
										<li>Set your own text for the adblock notice</li>
										<li>Change adblock notice title</li>
                                        <li>Disabled simple adblock notice for particular pages if you do not have the advertisement on them</li>
                                        <li>You can opt for "Strict mode" to hide your entire website until your website is whitelisted</li>
                                        <li>You can show a sticky notice always on the screen to annoy the viewer and make him/her whitelist your website</li>
										</ul>
                                        <a href="http://techsini.com/our-wordpress-plugins/simple-adblock-notice/" target="_blank"><button type="button" class="button button-primary" name="getpro">Get Pro Version Now!</button></a><br>
									</div>
								</div>
                            </div>
                        </div> <!--post-body-content-->


                        <!-- sidebar -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="meta-box-sortables">

                                <div class="postbox">
                                    <h3><span>About</span></h3>
                                    <div class="inside">
                                        <strong>Simple Adblock Notice Lite</strong> <br>
                                        Website: <a href="http://techsini.com" target="_blank">TechSini.com</a> <br>
                                        Thank you for installing this plugin.
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Rate This Plugin!</span></h3>
                                    <div class="inside">
                                        <p>Please <a href="https://wordpress.org/plugins/simple-adblock-notice/" target="_blank">rate this plugin</a> and share it to help the development.</p>
										<ul class="soc">
										   <li><a class="soc-facebook" href="https://www.facebook.com/techsini" target="_blank"></a></li>
										   <li><a class="soc-twitter" href="https://twitter.com/techsini" target="_blank"></a></li>
										   <li><a class="soc-google soc-icon-last" href="https://plus.google.com/+Techsini" target="_blank"></a></li>
										 </ul>
									</div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Our other WordPress Plugins</span></h3>
                                    <div class="inside">
                                      <ul>
                                      <li><a href="http://techsini.com/our-wordpress-plugins/fluid-notification-bar/">Fluid Notification Bar</a></li>
                                      <li><a href="http://techsini.com/our-wordpress-plugins/elegant-subscription-popup/">Elegant Subscription Popup</a></li>
                                      <li><a href="http://techsini.com/our-wordpress-plugins/stylish-notification-popup/">Stylish Notification Popup</a></li>
                                      <li><a href="http://techsini.com/our-wordpress-plugins/simple-under-construction/">Simple Under Construction</a></li>
                                      </ul>
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Our other Projects</span></h3>
                                    <div class="inside">
                                      <ul>
                                      <li><a href="http://techsini.com/multi-mockup/">Multi Device Website Mockup Generator</a></li>
                                      <li><a href="http://techsini.com/blog-title-generator/">Blog Title Generator</a></li>
                                      <li><a href="http://techsini.com/html-listgen/">HTML List Generator</a></li>
                                      <li><a href="http://techsini.com/gridgen/">GridGen - Bootstrap Grid Layout Generator</a></li>
                                      </ul>
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                            </div> <!-- .meta-box-sortables -->
                        </div> <!-- #postbox-container-1 .postbox-container -->


                    </div>
                </div>
            </div>
        <?php
    }

    public function page_init(){

        register_setting(
        'simple_adblock_notice_settings_group', // Option group
        'simple_adblock_notice_settings' // Option name
        );

        add_settings_section(
            'section_1', // ID
            '', // Title
            array( $this, 'section_1_callback' ), // Callback
            'simple-adblock-notice-settings' // Page
        );

        add_settings_field(
            'san_enabled', // ID
            'Enable Adblock Notice', // Title
            array( $this, 'san_enabled_callback' ), // Callback
            'simple-adblock-notice-settings', // Page
            'section_1' // Section
        );

        add_settings_field(
            'noticeinterval', // ID
            'Show Notice After Every', // Title
            array( $this, 'noticeinterval_callback' ), // Callback
            'simple-adblock-notice-settings', // Page
            'section_1' // Section
        );

        add_settings_field(
            'howtowhitelist', // ID
            'Show How to WhiteList Website Tutorial', // Title
            array( $this, 'howtowhitelist_callback' ), // Callback
            'simple-adblock-notice-settings', // Page
            'section_1' // Section
        );


        add_settings_field(
            'notes', // ID
            '', // Title
            array( $this, 'notes_callback' ), // Callback
            'simple-adblock-notice-settings', // Page
            'section_1' // Section
        );



    }
    public function section_1_callback(){

    }

    public function san_enabled_callback(){
        if(isset($this->options['san_enabled'])){
            $sanenabled = $this->options['san_enabled'];
        } else {
            $sanenabled = 0;
        }

        printf ('<label for="san_enabled">
        <input type = "checkbox"
        id = "san_enabled"
        name= "simple_adblock_notice_settings[san_enabled]"
        value = "1"' . checked(1, $sanenabled, false) . '/>'.
        ' Yes</label>');

    }

    public function noticeinterval_callback(){

        $noticeinterval = $this->options['noticeinterval'];
        echo ('<select id="noticeinterval" name="simple_adblock_notice_settings[noticeinterval]">' .
                '<option value="1" ' . selected($noticeinterval, "1", false) . '>1</option>' .
                '<option value="2" ' . selected($noticeinterval, "2", false) . '>2</option>' .
                '<option value="5" ' . selected($noticeinterval, "5", false) . '>5</option>' .
                '<option value="10" ' . selected($noticeinterval, "10", false) . '>10</option>' .
                '<option value="20" ' . selected($noticeinterval, "20", false) . '>20</option>' .
                '<option value="30" ' . selected($noticeinterval, "30", false) . '>30</option>' .
                '</select> Minutes <small>(Page reload required)</small>'
            );
    }

    public function howtowhitelist_callback(){
        if(isset($this->options['howtowhitelist'])){
            $howtowhitelist = $this->options['howtowhitelist'];
        } else {
            $howtowhitelist = 0;
        }

        printf ('<label for="howtowhitelist">
        <input type = "checkbox"
        id = "howtowhitelist"
        name= "simple_adblock_notice_settings[howtowhitelist]"
        value = "1"' . checked(1, $howtowhitelist, false) . '/>'.
        ' Yes</label>');

    }

    public function notes_callback(){


    }


}


?>
