<?php

class MobileDetect {

	private static $isMobile;

	public static function onPageRenderingHash( &$confstr, User $user, $optionsUsed ) {
		if ( self::isMobile() ) {
			$confstr .= "!mobile";
		}

		return true;
	}

	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'nomobile', 'MobileDetect::nomobile' );
		$parser->setHook( 'mobileonly', 'MobileDetect::mobileonly' );

		return true;
	}

	public static function nomobile( $input, array $args, Parser $parser, PPFrame $frame ) {
		if ( self::isMobile() ) {
			return '';
		}

		return $parser->recursiveTagParse( $input );
	}

	public static function mobileonly( $input, array $args, Parser $parser, PPFrame $frame ) {
		if ( self::isMobile() ) {
			return $parser->recursiveTagParse( $input );
		}

		return '';
	}

	public static function isMobile() {
		if ( self::$isMobile ) {
			return self::$isMobile;
		}
		// Check for existance of the X-UA-DEVICE header ( Somebody already did the work for us)
		$request = RequestContext::getMain();
		$deviceHeader = $request->getRequest()->getHeader( 'X-UA-DEVICE');
		if ( $deviceHeader ) {
			self::$isMobile = ( $deviceHeader === 'mobile' );
		} elseif ( self::getAMF() ) {
			// If not, check if Apache Mobile Filter is in use
			self::$isMobile = true;
		} else {
			// And finally, try PHP mobile-dedect
			require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
			$detect = new Mobile_Detect;
			self::$isMobile = $detect->isMobile();
		}

		//header( 'X-UA-DEVICE: ' . self::$isMobile ? 'mobile' : 'desktop' );
		//header( 'Vary: X-UA-DEVICE' );

		return self::$isMobile;
	}

	public function getAMF() {
		global $wgMobileDetectTabletIsMobile;

		$amf = isset( $_SERVER['AMF_DEVICE_IS_MOBILE'] ) && $_SERVER['AMF_DEVICE_IS_MOBILE'] === 'true';
		if ( !$wgMobileDetectTabletIsMobile && $amf ) {
			$amf &= $_SERVER['AMF_DEVICE_IS_TABLET'] === 'false';
		}
		return $amf;
	}





}
