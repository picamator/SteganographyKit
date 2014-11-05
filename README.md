SteganographyKit
================
Steganography is the art and science of hiding information by embedding messages within other, seemingly harmless messages [1].
General overview of Steganography and existing tools can be found in [3], [7].

SteganographyKit is a package with implementation several stegoSystems for image Steganography.
SteganographyKit is used terminology that was described by Christian Cachin [1].

SteganographyKit contains:
* Least Significant Bit (LSB) 
  * Pure Steganography 
  * Secret Key Steganography 

Requirements
------------
* PHP 5.4+
* GDLib
* ZLib
* Only for Suhosin patch special configuration should be added:
```
  suhosin.srand.ignore = Off
  suhosin.mt_srand.ignore = Off
```

Installation
------------
The best way to install SteganographyKit is use composer:

* Update your `composer.json`

```json
{
    "require": {
        "picamator/steganographykit": "dev-master"
    }
}
```

* Run `composer update`

Usage
-----
### Encode
```php
<?php

require __DIR__ . '/vendor/autoload.php';

$stegoContainer = new Picamator\SteganographyKit\StegoContainer();

// cover-image.png|.jpg|.gif - path to existing image to cover secretText
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
* png, jpg or gif images as coverText,
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
 
UML Diagram
-----------
UML diagrams can be found in `/doc/uml` folder:

* Class diagram was created by [ArgoUML](http://argouml.tigris.org)
* Workflow diagram was written by Google Drawing 

License
-------
BSD 3-Clause License

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