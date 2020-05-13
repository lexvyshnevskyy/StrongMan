<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 11.09.19
 * Time: 17:34
 */

spl_autoload_register(function ($name) {
    $fullpath = __DIR__ . "/" .$name. '.php';
    if (file_exists($fullpath)) {
        /** @noinspection PhpIncludeInspection */
        require_once $fullpath;
    }
});


spl_autoload_register(function ($name) {
    $fullpath = __DIR__."/controller/".$name. '.php';
    if (file_exists($fullpath)) {
        /** @noinspection PhpIncludeInspection */
        require_once $fullpath;
    }
});



function _rest_routes() {

    $plugin_rest = array(
        'shortcut'=> new shortcutResponder(),
	    'table'=>new tableFiller(),
        'export' => new exportData()
    );

    foreach ($plugin_rest as $key=>$value){
        $value->register_routes();
    }

}
add_action( 'rest_api_init', '_rest_routes' );