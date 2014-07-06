SteganographyKit
================

Introduction
------------
SteganographyKit - package of implementation several stegoSystems for image steganography.
Such Kit is used termininalogy that was described in Christian Cachin [1].
Kit works with basic types of Steganography [2]:
* Pure Steganography
* Secret Key Steganography
* Public key Steganography

Each algorithm of SteganographyKit is well documented and based on scientific articles 
moreover comments inside code helps to understand what is going on and create your own modification. 

General overview of Steganography and existing tools described by Sean-Philip Oriyano [3].

Pure Steganography
----------------------------
### Least Significant Bit (LSB)
LSB method is modified least significant bit of coverText to get stegoText.
SteganographyKit has implemented it for png image [4] as a coverText and text with ASCII characters [5] as a secretText.

In general encode LSB can be described by those steps:

1. Convert secretText to binary string

2. Add to secretText end text mark (it is used for decode algorithm)

3. Change last bit of each RGB cannel for first pixel of coverText

4. Save changing if step 3 really change RGB bits

5. Move to next coverText pixel and change last bit of each RGB channel

6. Repeat step 5 for each secretText item

Beside decode LSB looks like:

1. Read every last bit for RGB channel of stegoText

2. Stop step 1 if end text mark was found or it's read last pixel of stegoText

3. Convert binary secretText to ASCII characters

More information can be found in [2].

UnitTest that make comparison between encode and decode of secretText can be found here:
`LsbTest::testEncodeDecode`. It's generate randomly 100 secretTexts

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
