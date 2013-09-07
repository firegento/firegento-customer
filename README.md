FireGento_Customer
==================

This extension extends the core functionality of the customer module of Magento.

Branches
--------
* master => stable version of the extension
* develop => contains new features

Facts
-----
- Version: check [config.xml](https://github.com/firegento/firegento-customer/blob/master/src/app/code/community/FireGento/Customer/etc/config.xml)
- [Extension on GitHub](https://github.com/firegento/firegento-customer/)

Description
-----------
This extension extends the core functionality of the customer module of Magento. The features of this extension are:

* Customers can be deactivated. Deactivated customers can be filtered in customer grid.
* Customers are temporarily deactivated if wrong password is entered too often.
* Password can be validated and rejected (password strength, password length, ..)

Requirements
------------
- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------
1. Install the extension by copying all the extension files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. You can now configure the extenion via *System -> Configuration -> Customer -> Customer Configuration -> Password*

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/firegento/firegento-customer/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
- Website: [http://firegento.com](http://firegento.com)
- Twitter: [@firegento](https://twitter.com/firegento)

Licence
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2013 FireGento
