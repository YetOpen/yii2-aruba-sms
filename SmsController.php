<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */


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
        $sms = new \app\components\SmsAruba;
        
    	$sms->sendSms( $username, $password, $tel, $message, $sender, $prefix,$delivery_time);
    }
}
