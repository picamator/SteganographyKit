CHANGELOG
=========

1.1.0 (2016-12-20)
------------------
* Moved to PSR-4
* Added Object Manager
* Added Docker
* Changed exception messages
* Split tests to unit and integration
* Moved `test` to `dev\tests`
* Split tests to integration and unit
* Added developing and contribution details to readme
* Added documentation generation
* Replaced usage `array` syntax to `[]`
* **Minor breaking backward compatibility** deprecated support PHP 5.4, 5.5
* **Minor breaking backward compatibility** deprecated support .gif images
* **Minor breaking backward compatibility** removed protected method ``PlainText->validateZLib``
* **Minor breaking backward compatibility** removed protected method ``Image->validateGbLib``
* **Minor breaking backward compatibility** moved implementing ``\Countable, \IteratorAggregate`` by ``AbstractSecretText`` to extending by ``SecretTextInterface``
* **Minor breaking backward compatibility** moved implementing ``\Countable, \IteratorAggregate`` by ``Image`` to extending by ``ImageInterface``

1.0.0 (2014-11-07)
------------------
* Implemented LSB Pure Steganography
* Implemented LSB Secret Steganography 
