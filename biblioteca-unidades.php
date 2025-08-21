<?php
/*
 * Plugin Name: Biblioteca Digital
 * Description: Plugin para Inserir, atualizar, ler e deletar Unidade Biblioteca
 * Version: 2.0
 * Author: Alexandre Alvarenga
 * Plugin URI: 
 * Author URI: 
 */

if(!function_exists('add_action')){
    echo 'Opa! Eu sou só um plugin, não posso ser chamado diretamente!';
    exit;
}

// setup
define('BIBLIOTECA_PLUGIN_URL', __FILE__);

register_activation_hook(BIBLIOTECA_PLUGIN_URL, 'biblioteca_table_creator');
// register_uninstall_hook(BIBLIOTECA_PLUGIN_URL, 'biblioteca_plugin');
// Hook de desinstalação
register_uninstall_hook(__FILE__, 'biblioteca_plugin_uninstall');

// includes
include('functions.php');
include('enqueue.php');


add_action('admin_menu', 'biblioteca_da_display_esm_menu');
add_action('admin_enqueue_scripts', 'biblioteca_admin_enqueue_css');



// exclui dados do banco de dados
register_deactivation_hook(__FILE__, 'exclude_data_from_biblioteca');



// insere dados no banco
register_activation_hook(__FILE__, 'cwpai_insert_data_into_biblioteca_table');


