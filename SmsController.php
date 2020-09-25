<?php
namespace yetopen\smsaruba;

use yii\console\Controller;
use Yii;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SmsController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionSend($username, $password, $tel, $message, $sender=NULL, $prefix="+39",$delivery_time=NULL)
    {
        $sms = new yetopen\smsaruba\SmsAruba;
        
    	$sms->sendSms( $username, $password, $tel, $message, $sender, $prefix,$delivery_time);
    }
}
