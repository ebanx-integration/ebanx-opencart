# EBANX OpenCart Payment Gateway Extension

This plugin enables you to integrate your OpenCart store with the EBANX payment gateway.
It includes support to installments and custom interest rates.

## Installation
1. Clone the git repo to your OpenCart root folder
```
git clone --recursive https://github.com/ebanx/ebanx-woocommerce.git
```
2. Visit your OpenCart payment settings:
    Extensions > Payment
3. Click the _Install_ link, and wait for the extension installation.
4. Click the _Edit_ link to change the EBANX settings.
5. Add the integration key you were given by the EBANX integration team. It will be different in test and production modes.
6. Change the other settings if needed.
7. Go to the EBANX Merchant Area, then to **Integration > Merchant Options**.
  1. Change the _Status Change Notification URL_ to:
```
{YOUR_SITE}/index.php?route=payment/ebanx/notify/
```
  2. Change the _Response URL_ to:
```
{YOUR_SITE}/index.php?route=payment/ebanx/callback/
```
8. That's all!

## Changelog
_1.0.0_: first release
