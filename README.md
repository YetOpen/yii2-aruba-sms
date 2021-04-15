Sending SMS via Aruba api
===========================
Sending SMS via Aruba api

Installation
------------

The preferred way to install this extension is through composer.

Either run
```
php composer.phar require --prefer-dist paskuale75/yii2skebby "*"
```
or add
```
"yetopen/yii2-sms-aruba": "@dev"
```
to the require section of your `composer.json` file.

**Component setup**

To use the Setting Component, you need to configure the components array in your application configuration:
```php
'components' => [
    'smsaruba' => [
        'class'     => app\components\SmsAruba::class,
        'username'  => 'MyUsername',
        'password'  => 'MyPassword',
    ],
],
```

Usage:
---------

Insert the following script in the page (Ex. About.php)
```php
//Create an action in your page...

<?php
    Yii::$app->smsaruba->sendSms(
        ['+393347335668'],
        'Hi, this is a test!'
    );
?>
```