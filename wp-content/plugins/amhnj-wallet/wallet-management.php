<?php

function walletCheckoutRequestProcess() {
    if ( ! isset( $_POST[ "wallet_checkout_request" ] ) ) return;

    $walletAmount = getWalletAmount();
    if ( $walletAmount === 0 ) {
        echo "<p class='woocommerce-error'>کیف پول شما خالیست!<p>";
        return;
    } else if ( $walletAmount < 100000 ) {
        echo "<p class='woocommerce-error'>حداقل مبلغ مجاز برای درخواست تسویه حساب 100.000 تومان می باشد!<p>";
        return;
    }

    $userInfo = userInfo();
    $oldRequests = get_user_meta( $userInfo->ID, "wallet-checkout-requests" );

    if ( function_exists( "parsidate" ) ) {
        $walletCheckoutRequest = array(
            "date" => parsidate( "Y-m-d" ),
            "time" => parsidate( "H:i:s" ),
            "amount" => $walletAmount,
            "status" => "created", //created, checked, in_progress, payed
        );
    } else {
        $walletCheckoutRequest = array(
            "date" => date( "Y-m-d" ),
            "time" => date( "H:i:s" ),
            "amount" => $walletAmount,
            "status" => "created", //created, checked, in_progress, payed
        );
    }

    $allRequests = [];
    if ( ! empty( $oldRequests ) && isset( $oldRequests[ 0 ] ) ) {
        $allRequests = $oldRequests[ 0 ];
    }

    $allRequests[] = $walletCheckoutRequest;

    update_user_meta( $userInfo->ID, "wallet-checkout-requests", $allRequests );
    update_user_meta( $userInfo->ID, "wallet-amount", 0 );
}

function getWalletCheckoutRequests( $echoHTML = true ) {
    $userInfo = userInfo();
    $allRequests = get_user_meta( $userInfo->ID, "wallet-checkout-requests" );

    if ( empty( $allRequests ) || ! isset( $allRequests[ 0 ] ) ) return;

    if ( $echoHTML ) {
        foreach( $allRequests[ 0 ] as $idx => $request ) {
            $rowNum = $idx + 1;
            $date = $request[ "date" ];
            $time = $request[ "time" ];
            $amount = $request[ "amount" ];
            $status = convertStatusToPersian( $request[ "status" ] );
            echo "<tr>
                <td>$rowNum</td>
                <td style='direction:ltr'>$date <small>$time</small></td>
                <td>$amount</td>
                <td>$status</td>
            </tr>";
        }
        return;
    }

    return $allRequests;
}

function getWalletAmount() {
    $userInfo = userInfo();

    $walletAmount = get_user_meta( $userInfo->ID, "wallet-amount" );

    return intval( $walletAmount[ 0 ] );
}

function updateWalletAmount( $amount = 0 ) {
    $userInfo = userInfo();

    return update_user_meta( $userInfo->ID, "wallet-amount", $amount );
}

function convertStatusToPersian( $status ) {
    switch ( $status ) {
        case "created";
            return "ایجاد شده";
        case "checked";
            return "بررسی شده";
        case "in_progress";
            return "در حال انجام";
        case "payed";
            return "پرداخت شده";
    }
}