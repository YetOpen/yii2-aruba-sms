Sending SMS via Aruba api
===========================
Sending SMS via Aruba api

Installation
------------

The preferred way to install this extension is through composer.

Either run
```
php composer.phar require --prefer-dist yetopen/yii2-sms-aruba "@dev"
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
        'class'     => yetopen\smsaruba\SmsAruba::class,
        'username'  => 'MyUsername',    // Your Aruba usurname
        'password'  => 'MyPassword',    // Your Aruba password
    ],
],
```

Usage
---------

Insert the following script in the page (Ex. About.php)
```php
// Create an action in your page...

<?php
    Yii::$app->smsaruba->sendSms(
        ['+393344556677'],      // Enter the number you want to send the message to; (the prefix is not necessary)
        'Hi, this is a test!'   // The message to send
    );
?>
```