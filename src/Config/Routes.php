<?php

$routes->group('admin', function ($routes) {


    $routes->resource('inventory', [
        'filter' => 'permission:inventory-permission',
        'controller' => 'InventoryController',
        'except' => 'show',
        'namespace' => 'julio101290\boilerplateinventory\Controllers',
    ]);

    $routes->get('nuevoMovimientoInventario'
            , 'InventoryController::newInventory'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->get('newInventory'
            , 'InventoryController::newInventory'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->get('editInventory/(:any)'
            , 'InventoryController::editInventory/$1'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->post('inventory/save'
            , 'InventoryController::save'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->post('inventory/getLastCode'
            , 'InventoryController::getLastCode'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->get('inventory/report/(:any)'
            , 'InventoryController::report/$1'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->get('inventory/(:any)/(:any)/(:any)'
            , 'InventoryController::inventoryFilters/$1/$2/$3'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->get('getAllProductsInventory/(:any)/(:any)/(:any)'
            , 'InventoryController::getAllProductsInventory/$1/$2/$3'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->post('inventory/getLastLot'
            , 'InventoryController::calculateLot'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->resource('saldos', [
        'filter' => 'permission:saldos-permission',
        'controller' => 'saldosController',
        'namespace' => 'julio101290\boilerplateinventory\Controllers',
        'except' => 'show'
    ]);
    $routes->post('saldos/getStoragesAjax'
            , 'SaldosController::getStoragesAjax'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );
    $routes->post('saldos/getProductsAjax'
            , 'SaldosController::getAllProducts'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );
    $routes->get('saldos/barcode/(:any)'
            , 'SaldosController::getBarcodePDF/$1'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->post('saldos/saveExtraFields'
            , 'SaldosController::saveExtraFields'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    $routes->post('saldos/getProductsFieldsExtra'
            , 'SaldosController::getProductsFieldsExtra'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );
    $routes->get('saldos/(:any)/(:any)/(:any)'
            , 'SaldosController::getSaldosFilters/$1/$2/$3'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );

    /*
     * Info inventpry for get producto with qrcode image
     */

    $routes->get('infoinventario'
            , 'SaldosController::getGetInfoProducts'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );
    $routes->post('generica'
            , 'SaldosController::getGetInfoProductsCode'
            , ['namespace' => 'julio101290\boilerplateinventory\Controllers']
    );
    
});
