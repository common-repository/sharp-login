<?php


register_activation_hook( __FILE__, function() {
    $sharplogin_settings = array();
    update_option( 'sharplogin_settings', $sharplogin_settings );
});

register_deactivation_hook( __FILE__, function() {
    delete_option( 'sharplogin_settings');
});