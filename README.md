SteganographyKit
================

Introduction
------------
SteganographyKit - package of implementation several stegosystems for image steganography.
Such Kit is used termininalogy that was decribed in Christian Cachin [1].
Kit works with basic types of Steganography [2]:
* Pure Steganography
* Secret Key Steganography
* Public key Steganography

Each algorithm of SteganographyKit is well documented and based on scientific articles 
moreover comments inside code helps to understand what is going on and create your own modification. 

General overview of Steganography and existing tools described by Sean-Philip Oriyano [3].

Encoding/Decoding algorithms
----------------------------
### Least Significant Bit (LSB)
LSB method is modified least significant bit of covertext to get stegotext.
 

References
----------
1. Christian Cachin "Digital Steganography". IBM Research, 17 February 2005, 
   https://www.zurich.ibm.com/~cca/papers/encyc.pdf

2. Zaidoon Kh. AL-Ani, A.A.Zaidan, B.B.Zaidan and Hamdan.O.Alanaz "Overview: Main Fundamentals for Steganography"//
   Journal of computing, vol. 2, issue 3, March 2010
   http://arxiv.org/pdf/1003.4086.pdf

3. Sean-Philip Oriyano "Using steganography to avoid observation Hiding in plain sight." IBM Research, 02 June 2009,
   http://www.ibm.com/developerworks/web/library/wa-steganalysis/index.html?ca=dat

