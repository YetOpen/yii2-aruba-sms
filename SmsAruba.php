<?php
namespace app\components;

use Yii;
use yii\base\Component;
use linslin\yii2\curl;
use yii\helpers\VarDumper;
use yii\helpers\Json;
use yii\base\UserException;

require __DIR__.'/../vendor/autoload.php';


define("BASEURL", "https://adminsms.aruba.it/API/v1.0/REST/");

class SmsAruba extends Component
{
    /** @var string Your username */
    public $username;
    /** @var string Your password */
    public $password;

    # const BASEURL = "https://adminsms.aruba.it/API/v1.0/REST/";

    const MESSAGE_HIGH_QUALITY="N";
    const MESSAGE_MEDIUM_QUALITY="L";

        /**
     * Authenticates the user given it's username and password.
     * Returns the pair user_key, Session_key
     */
    function login() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, BASEURL.'login?username='.$this->username.'&password='.urlencode($this->password));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200) {
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response . '<br>');
            throw new YArubaSmsException('Error! http code: ' . $info['http_code'] . ', body message: ' . $response . '<br>');  #404; credentials are incorrect
        }
        return explode(";", $response);
    }

    public function sendSms($tel=[], $message="", $sender=NULL, $prefix="+39",$delivery_time=NULL)
    {
        $auth_key = $this->login();

        foreach($tel as $nt){
            $telp[] = substr($nt, 0, 1)=="+" ? $nt : $prefix.$nt;
        };
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
        $payload['returnCredits'] = "true";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, BASEURL.'sms');
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
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response . '<br>');
            throw new YArubaSmsException('Error! http code: ' . $info['http_code'] . ', body message: ' . $response . '<br>');
        }
        else {
            $obj = json_decode($response);
            Yii::trace($obj, true);
        }
    }
}

class YArubaSmsException extends UserException {

}