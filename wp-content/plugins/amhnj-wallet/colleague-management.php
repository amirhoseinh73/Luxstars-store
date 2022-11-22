<?php

function getOrdersByColleagueCode( $colleagueCode ) {
    $orders = getPostByMetaData( "colleague-code", $colleagueCode );

    return $orders;
}