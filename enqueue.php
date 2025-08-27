<?php 
function biblioteca_admin_enqueue_css(){
    
    wp_register_style(
        'biblioteca_br_style',
        plugins_url('/assets/css/style.css', BIBLIOTECA_PLUGIN_URL)
    );

    wp_enqueue_style('biblioteca_br_style');
}

function enqueue_script_biblioteca() {
    
    // Passa variáveis PHP para o JavaScript
    wp_localize_script('meu-plugin-script', 'pluginData', array(
        'url_busca' => plugin_dir_url(__FILE__) . 'busca-resultado.php',
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('meu_plugin_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_script_biblioteca');
