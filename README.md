# Sending SMS via Aruba api

Sending SMS via [Aruba SMS](https://hosting.aruba.it/servizio-sms.aspx).

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

## Configuration

In the application configuration, `components` section, add the following:

```php
    'smsaruba' => [
        'class'     => yetopen\smsaruba\SmsAruba::class,
        'username'  => 'MyUsername',    // ArubaSMS username (Smsxxx)
        'password'  => 'MyPassword',    // ArubaSMS password ()
    ],
```

## Usage

You can send SMS by calling `sendSms`:

```php
Yii::$app->smsaruba->sendSms(
    ['+393344556677'],      // Enter the number(s) you want to send the message to; (the prefix is not necessary)
    'Hi, this is a test!'   // The message to send
);
```
