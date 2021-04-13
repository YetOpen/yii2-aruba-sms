<?php
namespace yetopen\smsaruba;

class SmsAruba
{
 
    const BASEURL = "https://adminsms.aruba.it/API/v1.0/REST/";

    const MESSAGE_HIGH_QUALITY="N";
    const MESSAGE_MEDIUM_QUALITY="L";

        /**
     * Authenticates the user given it's username and password.
     * Returns the pair user_key, Session_key
     */
    function login($username, $password) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::BASEURL .
                    'login?username=' . $username .
                    '&password=' . $password);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200) {
            return null;
        }
        return explode(";", $response);
    }

    public function sendSms( $username, $password, $tel=[], $message="", $sender=NULL, $prefix="+39",$delivery_time=NULL)
    { 
        $auth_key = $this->login($username,$password);
        
        foreach($tel as $nt){
            $telp[] = substr($nf, 0, 1)=="+" ? $nt : $prefix.$nt;
        }
        $payload = [
            "message_type" => self::MESSAGE_HIGH_QUALITY,
            "message" => $message,
            "recipient" => $telp,
        ];
        
        if (!is_null($sender)){
            $payload['sender'] = $sender;
        }
        if (!is_null($delivery_time)){
            $payload['scheduled_delivery_time'] = $delivery_time;
        }
        $payload['returnCredits'] = "true2";

        echo($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sms_config['site']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'user_key: '.$auth_key[0],
            // Use this when using session key authentication
            'Session_key: '.$auth_key[1],
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 201) {
            echo('Error! http code: ' . $info['http_code'] . ', body message: ' . $response);
        }
        else {
            $obj = json_decode($response);
            print_r($obj);
        }
    }
}
