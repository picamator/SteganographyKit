SteganographyKit
================
Steganography is the art and science of hiding information by embedding messages within other, seemingly harmless messages [1].
SteganographyKit is a package with implementation several stegoSystems for image steganography.
SteganographyKit is used terminology that was described by Christian Cachin [1].

SteganographyKit contains:
* Least Significant Bit (LSB) 
  * Pure Steganography 
  * Secret Key Steganography 

General overview of Steganography and existing tools described by Sean-Philip Oriyano [3].

Requirements
------------
* PHP 5.4+
* GDLib
* Only for Suhosin patch special configuration should be added:
```
  suhosin.srand.ignore = Off
  suhosin.mt_srand.ignore = Off
```

Least Significant Bit (LSB)
---------------------------
LSB method is modified least significant bit of coverText to get stegoText. 
Detailed description with example can be found in [6] or in "Steganography in Depth" section [7].

SteganographyKit has implementation of LSB with such conditions:
* png image [4] as coverText,
* text with ASCII characters [5] as a secretText.

### Pure Steganography
Pure Steganography is a Steganography system that doesn't require prior exchange of some secret information before sending message [2].
 
Encode algorithm can be described by steps:
  1. Convert secretText to binary string
  2. Add to secretText end text mark (it is used for decode algorithm)
  3. Get number of bits accordingly number of channels from secretText
  4. Change last bit of each RGB cannel for first pixel of coverText by bits from step 3
  5. Save changing if step 3 really change RGB bit
  6. Move to next coverText pixel and change last bit of each RGB channel
  7. Repeat step 3-6 for each secretText item

Decode algorithm can be described by steps:
  1. Read every last bit for RGB channel of stegoText
  2. Stop step 1 if end text mark was found or it's read last pixel of stegoText
  3. Convert binary secretText to ASCII characters

*Note*:
Additionally it's possible to configurate channel that will be used in algorithm, for instance Red, Green or Green only, etc.
So knowledge about use channels can be interpreted as Secret Key. 

Some researches use only Blue channel for steganography because that color is less perceived by human eye. 
Such conclusion is based on experiment [8]. But it should be taken critically because first of all stegoanalyze use computer technique to identify picture 
with hidden information, the second digital picture is displayed on a screen that has enough light.

### Secret Key Steganography
For Secret Key Steganography is similar with Pure Steganography but Secret Key is used for encode-decode process [2].

SteganographyKit is used approach described in [2], accordingly them Secret Key is a seed for pseudo-random generator. 
Such seed is used to create sequences of numbers that shows in what order coverText's pixels should be taken for embed secretText. 

SteganogrpahyKit implements Secret Key Steganography with such conditions:
* Max length of secretText is in 4 times less in compare with Pure Steganography. It means that only half of pixels are going to modify.
Such restriction helps to make room for better random distribution of secretText. 
* SecretKey has limit on seed: from 4 to 8 numbers. It uses `mt_srand` that has period `2^19937 - 1`.
Of course such conditions should be investigated to find out optimize min parameters.

Encode/Decode algorithm is differ from Pure Steganography by:
* Method of choosing pixels in CoverText. In Pure Steganography it gets one by one but in Secret Key Steganography gets accordingly pseudo-random algorithm.
* Method of use RGB channels. In Pure Steganography order is the same as user set but for Secret Key Steganography is changes accordingly pixel's coordinates. 

If pixel coordinates `X` and `Y` and array of channels is `['red', 'green', 'blue']` then 'red' will have `(X + Y) % 3` index in channel array the 
channel that had `(X + Y) % 3` would be moved to old red's place. For instance `X = 4, Y = 10` them `(2 + 10) % 3 = 2` then new channels array is
`['blue', 'green', 'red']`. So using such approach secretText will be randomly spread through coverText bits but also through channels. 
 
UnitTest
--------
Tests can be found in '/tests' folder. 
It should be noticed that `PureLsbTest::testEncodeDecode` or `SecretLsbTest::testEncodeDecode` includes random generated dataProvider with 100 items.

BackLog
-------
Wait to implement Pure Steganography with:
* Create Stegoanalyze for LSB
* Add diagrams for algorithm's description

References
----------
1. Christian Cachin "Digital Steganography". IBM Research, 17 February 2005, 
   https://www.zurich.ibm.com/~cca/papers/encyc.pdf

2. Zaidoon Kh. AL-Ani, A.A.Zaidan, B.B.Zaidan and Hamdan.O.Alanaz "Overview: Main Fundamentals for Steganography"//
   Journal of computing, vol. 2, issue 3, March 2010
   http://arxiv.org/pdf/1003.4086.pdf

3. Sean-Philip Oriyano "Using steganography to avoid observation Hiding in plain sight." IBM Research, 02 June 2009,
   http://www.ibm.com/developerworks/web/library/wa-steganalysis/index.html?ca=dat

4. http://www.w3.org/TR/PNG-Structure.html

5. http://www.asciitable.com/

6. Vijay Kumar Sharma, Vishal Shrivastava "A steganography algorithm for hiding image in image by improved lsb substitution by minimize detection" // 
   Journal of Theoretical and Applied Information Technology, vol. 36 issue 1, 15th February 2012
   http://www.jatit.org/volumes/Vol36No1/1Vol36No1.pdf

7. Gregory Kipper "Investigator's Guide to Steganography", CRC Press, Oct 27, 2003, 240 pages
   http://books.google.com.ua/books?id=qGcum1ZWkiYC&pg=PA37&source=gbs_toc_r&cad=3#v=onepage&q&f=false

8. Seling Hecht, Simon Shlaer, Maurice Henri Pirenne "Energy, quanta and vision"// JGP vol.25 no. 6, 819-840, July 20, 1942
   http://rieke-server.physiol.washington.edu/People/Fred/Classes/532/Hecht1942.pdf

9. Ali K. Hmood, B.B. Zaidan, A.A. Zaidan and Hamid A. Jalab "An Overview on Hidden Information Technique in Images"// Journal of Applied Science vol.10, issue 18, pages 2094-2100, 2010
   http://scialert.net/abstract/?doi=jas.2010.2094.2100

10. Craig Buckler "How to Create Your Own Random Number Generator in PHP", February 8, 2012
   http://www.sitepoint.com/php-random-number-generator/