# Sending SMS via Aruba api

Sending SMS via Aruba api

Service by: https://hosting.aruba.it/servizio-sms.aspx

## Installation

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

Make a call like this:
```php
Yii::$app->smsaruba->sendSms(
    ['+393344556677'],      // Enter the number you want to send the message to; (the prefix is not necessary)
    'Hi, this is a test!'   // The message to send
);
```