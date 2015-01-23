# EBANX OpenCart Payment Gateway Extension

This plugin allows you to integrate your OpenCart store with the EBANX payment gateway.
It includes support to installments and custom interest rates.

## Requirements

* OpenCart 1.5.6.4 or less

### Installation
#### Source
1. Clone the git repo to your OpenCart root folder
```
git clone --recursive https://github.com/ebanx/ebanx-opencart.git
```
2. Visit your OpenCart payment settings at **Extensions > Payment**.
3. Click the _Install_ link, and wait for the extension installation to complete.
4. Click the _Edit_ link to change the EBANX settings.
5. Add the integration key you were given by the EBANX integration team. You will need to use different keys in test and production modes.
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

#### Zip file
1. Upload the EBANX OpenCart extension to your OpenCart installation directory.
2. Extract the file contents to the root OpenCart directory.
3. Go to your store admin area.
5. Visit your OpenCart payment settings at **Extensions > Payment**.
6. Click the _Install_ link, and wait for the extension installation to complete.
7. Click the _Edit_ link to change the EBANX settings.
8. Add the integration key you were given by the EBANX integration team. You will need to use different keys in test and production modes.
9. Change the other settings if needed.
10. Go to the EBANX Merchant Area, then to **Integration > Merchant Options**.
  1. Change the _Status Change Notification URL_ to:
```
{YOUR_SITE}/index.php?route=payment/ebanx/notify/
```
  2. Change the _Response URL_ to:
```
{YOUR_SITE}/index.php?route=payment/ebanx/callback/
```
11. That's all!

## Changelog
* 2.1.0: Peru support
* 2.0.0: implemented the Direct API.
* 1.2.2: updated EBANX library
* 1.2.1: fixed JavaScript event error on checkout.
* 1.2.0: added log features
* 1.1.3: fixed PHP notice from OpenCart helper
* 1.1.2: added logging via Slog
* 1.1.1: removed installments from checkout mode, fixed translated field issue
* 1.1.0: implemented direct checkout (boleto and credit cards)
* 1.0.1: included EUR conversion in minimum installment value
* 1.0.0: first release
