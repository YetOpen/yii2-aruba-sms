<?php


namespace yetopen\smsaruba;

use yetopen\smssender\BaseSmsSender;
use Yii;
use yii\base\Exception;
use yii\base\DynamicModel;


/**
 * Based on the APIs listed here: https://smsdevelopers.aruba.it/
 *
 * @property $minTextLength int
 * @property $maxTextLength int
 */
class SmsAruba extends BaseSmsSender
{
    /** @var string Your username */
    public $username;
    /** @var string Your password */
    public $password;
    /** @var string The default sender name */
    public $senderName;
    /** @var bool Enable the logging of errors */
    public $enableLogging = TRUE;

    const BASEURL = "https://adminsms.aruba.it/API/v1.0/REST/";
    const MESSAGE_HIGH_QUALITY="N";
    const MESSAGE_MEDIUM_QUALITY="L";

    const MIN_TEXT_LENGTH = 2;
    const MAX_TEXT_LENGTH = 918;

    /**
     * @return int
     */
    public function getMinTextLength()
    {
        return static::MIN_TEXT_LENGTH;
    }

    /**
     * @return int
     */
    public function getMaxTextLength()
    {
        return static::MAX_TEXT_LENGTH;
    }

    /**
     * Check the length of the message.
     * 
     * @param string $message Message to check.
     */
    private function messageValidator($message) {
        $model = DynamicModel::validateData(compact('message'), [
            ['message', 'string', 'length' => [$this->minTextLength, $this->maxTextLength]]
        ]);
        if ($model->hasErrors()) {
            throw new SmsArubaException(Yii::t('app','Error! message must be between {min} and {max} char', [
                'min' => $this->minTextLength,
                'max' => $this->maxTextLength,
            ]));
        }
    }

    /**
     * Authenticates the user given it's username and password.
     *
     * @return string $response Response from Aruba: returns the pair user_key, Session_key.
     * @throws SmsArubaException
     */
    function login() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::BASEURL.'login?username='.$this->username.'&password='.urlencode($this->password));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] == 404) {
            $this->logError(
                'Error! Login Failed: Credentials are incorrect. Http code: ' . $info['http_code'] . ', body message: ' . $response,
                __METHOD__
            );
            return FALSE;
        } else if ($info['http_code'] != 200) {
            $this->logError('Error! http code: ' . $info['http_code'] . ', body message: ' . $response, __METHOD__);
            return FALSE;
        }
        return explode(";", $response);
    }

    /**
     * {@inheritdoc}
     * @throws SmsArubaException
     */
    public function sendMessage($message)
    {
        $auth_key = $this->login();
        if($auth_key === FALSE) {
            return FALSE;
        }

        $this->messageValidator($message->content);

        $numbers = array_map(function ($number) use ($message) {
            return substr($number, 0, 1)=="+" ? $number : $message->prefix.$number;
        }, $message->numbers);
        $payload = [
            "message_type" => self::MESSAGE_HIGH_QUALITY,
            "message" => $message->content,
            "recipient" => $numbers,
        ];

        $sender = $message->sender ?: $this->senderName;
        if (!is_null($sender)){
            $payload['sender'] = $sender;
        }
        if (!is_null($message->deliveryTime)){
            $payload['scheduled_delivery_time'] = $message->deliveryTime;
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
            $this->logError(
                'Error! Sending failed: User_key, Token or Session_key are invalid or not provided. Http code: ' . $info['http_code'] . ', body message: ' . $response,
                __METHOD__
            );
            return FALSE;
        } else if ($info['http_code'] != 201) {
            $this->logError("Error! Http code: {$info['http_code']}, body message: $response", __METHOD__);
            return FALSE;
        } else {
            $this->debug($response, __METHOD__);
        }
        return TRUE;
    }

    /**
     * @deprecated
     * @see SmsAruba::send()
     */
    public function sendSms($tel, $message, $sender=NULL, $prefix="+39",$delivery_time=NULL)
    {
        return $this->send($tel, $message, $sender, $prefix, $delivery_time);
    }
}

class SmsArubaException extends Exception {
    
}