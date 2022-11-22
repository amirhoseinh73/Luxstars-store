<?php

function getOrdersByColleagueCode( $colleagueCode ) {
    $orders = getOrderByMetaData( "colleague-code", $colleagueCode );

    return $orders;
}

function calcBenefitColleague( $orders ) {
    $benefit = 0;

    if ( empty( $orders ) ) return;

    foreach( $orders as $order ) {
        $orderData = $order->data;
        $orderTotal = $orderData[ "total" ];

        $benefit += intval( $orderTotal );
    }

    return number_format( $benefit );
}