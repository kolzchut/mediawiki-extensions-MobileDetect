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

### $wgMobileDetectSkin
Allows setting a different skin for mobile:
    
    $wgMobileDetectSkin = 'vector'

### Parser tags:  ``<nomobile>`` and ``<mobileonly>``
These tags allow users to control which content is displayed only in mobile browsers, and
which content is displayed only in desktop browsers.

### $wgMobileDetectFilterModules - ResourceLoader mobile-specific modules
To enable this, set `$wgMobileDetectFilterModules = true;` (false by default)

RL Modules can have a "targets" option, with the possible values being "desktop" and "mobile".
If not set, "desktop" is asssumed. Otherwise, you can specify either/both, e.g.:

    'mediawiki.ui' => array(
    	'targets' => array( 'desktop', 'mobile' ),
    ),

See core's ```Resources.php``` to see the settings for every ResourceLoader module.

If you set this to true, you __must__ make sure the targets for any module
you require include 'mobile'. As an example, core's `mediawiki.legacy.shared`
is not enabled for mobile by default. If you need it to be, you have to do so
yourself - see the instructions here (we are using the same hook, "BeforePageDisplay"):
https://www.mediawiki.org/wiki/ResourceLoader/Writing_a_MobileFrontend_friendly_ResourceLoader_module#Enabling_your_existing_modules

## Planned functionality
Please see [TODO.md](TODO.md)
