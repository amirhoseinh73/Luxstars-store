<?php

function send_sms_ir( $template, $mobile, $value ) {

    $url = "https://api.sms.ir/v1/send/verify";

    switch( $template ) {
        case "login":
            $templateID = 821420;
            break;
        case "register":
            $templateID = 144692;
            break;
    }

    $dataHeader = array(
        "Content-Type: application/json",
        "ACCEPT: application/json",
        "X-API-KEY: MP1ujNFIXu0f1AFSzSfZ0OWp5Ilj8rZ0WS7jK8956qtKMT4GXnbXYylHwOrqflPw"
    );

    $dataPost = array(
        "mobile" => $mobile,
        "templateId" => $templateID,
        "parameters" => array(
            array(
                "name" => "VERIFICATIONCODE",
                "value" => $value,
            )
        )
    );

	try {
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $dataHeader );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $dataPost ) );

        $result = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($result);
    } catch ( \Exception $e ) {
        echo 'Error sms send : ' . $e->getMessage();
    }
}