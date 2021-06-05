# Omnipay: Rabobank OmniKassa V2

**Rabobank OmniKassa driver for the Omnipay PHP payment processing library**

[![Unit Tests](https://github.com/thephpleague/omnipay-rabobank/actions/workflows/run-tests.yml/badge.svg)](https://github.com/thephpleague/omnipay-rabobank/actions/workflows/run-tests.yml)[![Latest Stable Version](https://poser.pugx.org/omnipay/rabobank/version.png)](https://packagist.org/packages/omnipay/rabobank)
[![Total Downloads](https://poser.pugx.org/omnipay/rabobank/d/total.png)](https://packagist.org/packages/omnipay/rabobank)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.6+. This package implements Rabobank OmniKassa V2 support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `omnipay/rabobank` with Composer:

```
composer require league/omnipay omnipay/rabobank
```


## Basic Usage

The following gateways are provided by this package:

* Rabobank

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.


## Updating to V2

In order to upgrade to Omnikassa V2 you need the following codes:

* `RefreshToken`
* `SigningKey`

You can look those up in your Omnikassa V2 Dashboard. See the [documentation on rabobank.nl](https://www.rabobank.nl/images/handleiding-signing-key-en-refresh-token-ophalen_29951774.pdf).


1. The `Merchant ID` and `Key Version` parameters are not needed anymore. The refresh token is the new merchant identification.
2. It's not possible anymore to provide more then one Payment Method per purchase by separating them with a comma. (eg. `IDEAL,CARDS`)
3. There is no option anymore for providing a notice URL per purchase. The notice URL needs to be configured in the Omnikassa V2 Dashboard.
4. The notify `POST` doesn't have any status information about the order anymore. You can fetch information about orders by making a `StatusRequest`. You'll need the ``notificationToken`` that's received in the notification `POST`.  
Make sure you fetch all open order statuses by checking the ``moreOrderResultsAvailable`` parameter in the response. If set to true, make another `StatusRequest` with the same `notificationToken`, until ``moreOrderResultsAvailable`` is `false`.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-rabobank/issues),
or better yet, fork the library and submit a pull request.
