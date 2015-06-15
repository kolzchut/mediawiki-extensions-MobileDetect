MobileDetect extension for MediaWiki
=====================================

This extension detects mobile devices by 3 optional methods, in this order:

1. First, look for a X-UA-DEVICE header, set by Varnish. This can be done
   by a [VCL file for varnish][vcl]
2. If not available, it will try to look at headers set by the Apache Mobile Filter (AMF).
3. Finally, it will fall back to using [PHP Mobile-Detect][mobile-detect], a 3rd-party
   library installed through Composer. The VCL file recommended in (1) is derived from this
   library too.
 
 [vcl]: https://github.com/willemk/varnish-mobiletranslate
 [mobile-detect]: https://github.com/serbanghita/Mobile-Detect/


## Usage

### MobileDetect::isMobile()
This static function is available anywhere, and returns true when a mobile device is detected.
 
### Parser tags:  ``<nomobile>`` and ``<mobileonly>``
These tags allow users to control which content is displayed only in mobile browsers, and
which content is displayed only in desktop browsers.


## Planned functionality
Please see [TODO.md](TODO.md)
