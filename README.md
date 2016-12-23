SteganographyKit
================

[![PHP 7 ready](http://php7ready.timesplinter.ch/picamator/SteganographyKit/dev/badge.svg)](https://travis-ci.org/picamator/SteganographyKit)
[![Latest Stable Version](https://poser.pugx.org/picamator/steganographykit/v/stable.svg)](https://packagist.org/packages/picamator/steganographykit)
[![License](https://poser.pugx.org/picamator/steganographykit/license.svg)](https://packagist.org/packages/picamator/steganographykit)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e71d0e53-1709-4449-9ae0-9cb1a838a63b/mini.png)](https://insight.sensiolabs.com/projects/e71d0e53-1709-4449-9ae0-9cb1a838a63b)

Master
------
[![Build Status](https://travis-ci.org/picamator/SteganographyKit.svg?branch=master)](https://travis-ci.org/picamator/SteganographyKit)
[![Coverage Status](https://coveralls.io/repos/github/picamator/SteganographyKit/badge.svg?branch=master)](https://coveralls.io/github/picamator/SteganographyKit?branch=master)

Dev
---
[![Build Status](https://travis-ci.org/picamator/SteganographyKit.svg?branch=dev)](https://travis-ci.org/picamator/SteganographyKit)
[![Coverage Status](https://coveralls.io/repos/github/picamator/SteganographyKit/badge.svg?branch=dev)](https://coveralls.io/github/picamator/SteganographyKit?branch=dev)

SteganographyKit is a package with implementation several algorithms for image Steganography.

Steganography is the art and science of hiding information by embedding messages within other, seemingly harmless messages [1].
General overview of Steganography can be found in [3], [7].
SteganographyKit is used terminology described by Christian Cachin [1].

SteganographyKit contains:

* Least Significant Bit (LSB) 
  * Pure Steganography 
  * Secret Key Steganography 

Requirements
------------
* [PHP 5.6](http://php.net/manual/en/migration56.new-features.php) or [PHP 7.0](http://php.net/manual/en/migration70.new-features.php)
* [GD](http://www.php.net/manual/en/book.image.php)
* [Zip](http://ua2.php.net/manual/en/book.zip.php)
* Only for [Suhosin](https://suhosin.org/stories/index.html):
```
  suhosin.srand.ignore = Off
  suhosin.mt_srand.ignore = Off
```

Installation
------------
The best way to install SteganographyKit is use composer:

Update your `composer.json` with:

```json
{
    "require": {
        "picamator/steganographykit": "~1.1"
    }
}
```

Usage
-----
### Encode
```php
<?php

require __DIR__ . '/vendor/autoload.php';

$stegoContainer = new Picamator\SteganographyKit\StegoContainer();

// cover-image.png|.jpg - path to existing image to cover secretText
// stego-image.png  - path where new stegoImage should be saved
$stegoContainer->encode('/path/to/cover-image.png', 
    '/path/to/stego-image.png', 'secret test');

// output raw image 
$stegoContainer->renderImage();

```

### Decode
```php
<?php

require __DIR__ . '/vendor/autoload.php';

$stegoContainer = new Picamator\SteganographyKit\StegoContainer();

// stego-image.png
$secretText = $stegoContainer->decode('/path/to/stego-image.png');

echo $secretText;

```

### Use other stegoSystem
``` php
<?php

require __DIR__ . '/vendor/autoload.php';

$stegoContainer = new Picamator\SteganographyKit\StegoContainer();
$stegoSystem    = new Picamator\SteganographyKit\StegoSystem\SecretLsb();

// configure secret key
$secretKey = 123456;
$stegoKey  = new Picamator\SteganographyKit\StegoKey\RandomKey($secretKey);

$stegoSystem->setStegoKey($stegoKey);
$stegoContainer->setStegoSystem($stegoSystem);

// it's not necessary to set second parameter if result will put in stream 
$stegoContainer->encode('/path/to/cover-image.png', '', 'secret test');

// output raw image
header('Content-Type: image/png');
$stegoContainer->renderImage();

```

Least Significant Bit (LSB)
---------------------------
LSB method is modified least significant bit of coverText to get stegoText. 
Detailed description with example can be found in [4] or in "Steganography in Depth" section [5].

SteganographyKit has implementation of LSB with such conditions:
* png or jpg images as coverText,
* text as a secretText.

### Pure Steganography
Pure Steganography is a Steganography system that doesn't require prior exchange of some secret information before sending message [2].
 
Additionally it's possible to configurate channels that will be used in algorithm. 
For instance secretText can use only Red or Green and Blue or Red, Green, Blue. Moreover order in witch channels are used is important.
So channels can be interpreted as Secret Key. 

*Note*:
Some researches use only Blue channel for steganography because that color is less perceived by human eye. 
Such conclusion is based on experiment [6]. But it should be taken critically because first of all stegoanalyze use computer technique to identify picture 
with hidden information, the second digital picture is displayed on a screen that has enough light.

### Secret Key Steganography
For Secret Key Steganography is similar with Pure Steganography but Secret Key is used for encode-decode process [2].

SteganographyKit is used approach described in [2], accordingly them Secret Key is a seed for pseudo-random generator [8]. 
Such seed is used to create sequences of coordinates of coverText's pixels for covering secretText. 

SteganogrpahyKit implements Secret Key Steganography with such conditions:
* SecretKey has limit: from 4 to 8 numbers. It uses as a seed for `mt_srand` function.

Encode/Decode algorithm is differ from Pure Steganography by:
* Method of choosing pixels in CoverText. In Pure Steganography it gets one by one but in Secret Key Steganography gets accordingly pseudo-random algorithm.
* Method of use RGB channels. In Pure Steganography order is the same as user set but for Secret Key Steganography is changes accordingly pixel's coordinates. 

If pixel coordinates `X` and `Y` and array of channels is `['red', 'green', 'blue']` then 'red' will have `(X + Y) % 3` index in channel array the 
channel that had `(X + Y) % 3` would be moved to old red's place. For instance `X = 4, Y = 10` them `(2 + 10) % 3 = 2` then new channels array is
`['blue', 'green', 'red']`. So using such approach secretText will be randomly spread through coverText bits but also through channels. 
 
Documentation
-------------
* UML class diagram: [class.diagram.png](docs/uml/class.diagram.png)
* LSB encode/decode: [lsb-encode-decode.png](docs/uml/lsb-encode-decode.png)
* Generated documentation: [phpdoc](docs/phpdoc), please build it following [instruction](bin/phpdoc)

Developing
----------
To configure developing environment please:

1. Follow [Docker installation steps](bin/docker/README.md)
2. Run inside Docker container `composer install`

Contribution
------------
To start helping the project please review [CONTRIBUTING](CONTRIBUTING.md).

License
-------
SteganographyKit is licensed under the BSD-3-Clause License. Please see the [LICENSE](LICENSE.txt) file for details.

References
----------
1. Christian Cachin "Digital Steganography". IBM Research, 17 February 2005, 
   https://www.zurich.ibm.com/~cca/papers/encyc.pdf

2. Zaidoon Kh. AL-Ani, A.A.Zaidan, B.B.Zaidan and Hamdan.O.Alanaz "Overview: Main Fundamentals for Steganography"//
   Journal of computing, vol. 2, issue 3, March 2010
   http://arxiv.org/pdf/1003.4086.pdf

3. Sean-Philip Oriyano "Using steganography to avoid observation Hiding in plain sight." IBM Research, 02 June 2009,
   http://www.ibm.com/developerworks/web/library/wa-steganalysis/index.html?ca=dat

4. Vijay Kumar Sharma, Vishal Shrivastava "A steganography algorithm for hiding image in image by improved lsb substitution by minimize detection" // 
   Journal of Theoretical and Applied Information Technology, vol. 36 issue 1, 15th February 2012
   http://www.jatit.org/volumes/Vol36No1/1Vol36No1.pdf

5. Gregory Kipper "Investigator's Guide to Steganography", CRC Press, Oct 27, 2003, 240 pages
   http://books.google.com.ua/books?id=qGcum1ZWkiYC&pg=PA37&source=gbs_toc_r&cad=3#v=onepage&q&f=false

6. Seling Hecht, Simon Shlaer, Maurice Henri Pirenne "Energy, quanta and vision"// JGP vol.25 no. 6, 819-840, July 20, 1942
   http://rieke-server.physiol.washington.edu/People/Fred/Classes/532/Hecht1942.pdf

7. Ali K. Hmood, B.B. Zaidan, A.A. Zaidan and Hamid A. Jalab "An Overview on Hidden Information Technique in Images"// Journal of Applied Science vol.10, issue 18, pages 2094-2100, 2010
   http://scialert.net/abstract/?doi=jas.2010.2094.2100

8. Craig Buckler "How to Create Your Own Random Number Generator in PHP", February 8, 2012
   http://www.sitepoint.com/php-random-number-generator/
