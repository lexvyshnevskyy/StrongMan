<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 11.09.19
 * Time: 17:34
 */
spl_autoload_register(function ($name) {
    $fullpath = __DIR__."/controller/".$name. '.php';
    if (file_exists($fullpath)) {
        /** @noinspection PhpIncludeInspection */
        require_once $fullpath;
    }
});

spl_autoload_register(function ($name) {
    $fullpath = __DIR__."/class/".$name. '.php';
    if (file_exists($fullpath)) {
        /** @noinspection PhpIncludeInspection */
        require_once $fullpath;
    }
});

function _rest_routes() {
//    $UserBookingMapController = new UserBookingMap();
//    $UserBookingMapController->register_routes();
//
//    $GroupBookingMapController = new GroupBookingMap();
//    $GroupBookingMapController->register_routes();
//
//    $Checkout = new Checkout();
//    $Checkout->register_routes();
//
//    # liqPay response controller
//    $LiqPayResponse = new LPResponse();
//    $LiqPayResponse->register_routes();
//
//    # admin User Login
//    $admin['userLogin'] = new adminLogin();
//    $admin['userLogin']->register_routes();
//
//    # price edit system
//    $admin['priceSchedule'] = new priceSchedule();
//    $admin['priceSchedule']->register_routes();
//
//    # price edit system
//    $admin['search'] = new search();
//    $admin['search']->register_routes();
//
//    # price edit system
//    $admin['export'] = new exportData();
//    $admin['export']->register_routes();
}
add_action( 'rest_api_init', '_rest_routes' );