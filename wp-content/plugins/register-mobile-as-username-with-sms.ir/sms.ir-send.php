<?php

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "sms.ir-class.php";

function send_sms_ir( $mobile, $param_1, $value_1, $param_2 = null, $value_2 = null ) {
	try {
        date_default_timezone_set("Asia/Tehran");

        // your sms.ir panel configuration
        $APIKey = "646a2a1a76f4451355e3745";
        $SecretKey = "amirhoseinh730016973178";

        $APIURL = "https://ws.sms.ir/";

        switch ( $param_1 ) {
            case "VerificationCode":
                $template_ID = "64651";
                break;
            case "Password":
                $template_ID = "64975";
                break;
            default:
                return;
        }

        // message data
        $data = array(
            "ParameterArray" => array(
                array(
                    "Parameter" => "$param_1",
                    "ParameterValue" => $value_1
                ),
            ),
            "Mobile" => $mobile,
            "TemplateId" => $template_ID,
        );
        if ( ! empty( $param_2 ) ) {
            $data[ "ParameterArray" ][] =  array(
                "Parameter" => "$param_2",
                "ParameterValue" => $value_2
            );
        }

        $SmsIR_UltraFastSend = new SmsIrUltraFastSend($APIKey, $SecretKey, $APIURL);
        $UltraFastSend = $SmsIR_UltraFastSend->ultraFastSend($data);

        return $UltraFastSend;
    } catch ( \Exception $e ) {
        echo 'Error UltraFastSend : ' . $e->getMessage();
    }
}