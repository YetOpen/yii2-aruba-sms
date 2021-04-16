<?php

/**
 * based on the APIs listed here: https://smsdevelopers.aruba.it/
 */

namespace yetopen\smsaruba;

use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\base\Exception;
use yii\base\DynamicModel;


class SmsAruba extends Component
{
    /** @var string Your username */
    public $username;
    /** @var string Your password */
    public $password;

    const BASEURL = "https://adminsms.aruba.it/API/v1.0/REST/";
    const MESSAGE_HIGH_QUALITY="N";
    const MESSAGE_MEDIUM_QUALITY="L";

    /**
     * Check the length of the message.
     * 
     * @param string $message Message to check.
     */
    private function messageValidator($message) {
        $model = DynamicModel::validateData(compact('message'), [['message', 'string', 'length' => [2, 1000]]]);
        if ($model->hasErrors()) {
            throw new SmsArubaException('Error! message must be between 2 and 1000 char');
        }
    }

    /**
     * Authenticates the user given it's username and password.
     * 
     * @return string $response Response from Aruba: returns the pair user_key, Session_key.
     */
    function login() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::BASEURL.'login?username='.$this->username.'&password='.urlencode($this->password));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] == 404) {
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response);
            throw new SmsArubaException(Yii::t('app','Login Failed: Credentials are incorrect'));  #404: credentials are incorrect
        } else if ($info['http_code'] != 200) {
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response);
            throw new SmsArubaException(Yii::t('app','Error! http code: {http_code}, body message: {response}', ['http_code' => $info['http_code'], 'response' => $response]));die();
        }
        return explode(";", $response);
    }

    /**
     * Send the message given the phone number and message.
     * 
     * @param array $tel Recipient number.
     * 
     * @param string $message Message to send.
     * 
     * @param string|NULL $sender (optional) Sender phone,l which takes a string or can be NULL.
     * 
     * @param string $prefix Prefix (optional) Prefix number, default is: "+39".
     * 
     * @param string|NULL $delivery_time (optional) Time of sending the message, which takes a string or can be NULL.
     * 
     * @return string $response Response from Aruba.
     */
    public function sendSms($tel=[], $message="", $sender=NULL, $prefix="+39",$delivery_time=NULL)
    {
        $auth_key = $this->login();

        $this->messageValidator($message);

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
        curl_setopt($ch, CURLOPT_URL, self::BASEURL.'sms');
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

        if ($info['http_code'] == 401) {
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response);
            throw new SmsArubaException(Yii::t('Sending failed: User_key, Token or Session_key are invalid or not provided'));
        }
        else if ($info['http_code'] != 201) {
            Yii::error('Error! http code: ' . $info['http_code'] . ', body message: ' . $response);
            throw new SmsArubaException(Yii::t('app','Error! http code: {http_code}, body message: {response}', ['http_code' => $info['http_code'], 'response' => $response]));
        }
        else {
            Yii::trace($response);
        }
    }
}

class SmsArubaException extends Exception {
    
}