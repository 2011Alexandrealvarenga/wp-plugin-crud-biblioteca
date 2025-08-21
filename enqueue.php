<?php 
function biblioteca_admin_enqueue_css(){
    
    wp_register_style(
        'biblioteca_br_style',
        plugins_url('/assets/css/style.css', BIBLIOTECA_PLUGIN_URL)
    );

    wp_enqueue_style('biblioteca_br_style');
}

