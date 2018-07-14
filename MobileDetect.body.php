<?php

class MobileDetect {

	/** @var boolean $isMobile Describes whether reader is on a mobile device */
	private static $isMobile;
	private static $deviceType;
	private static $deviceOS;

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

	public static function getDeviceType() {
		if ( !isset( self::$deviceType ) ) {
			// Run isMobile(), which does all the needed tests
			self::isMobile();
		}

		return self::$deviceType;
	}

	public static function isMobile() {
		global $wgMobileDetectTabletIsMobile;

		self::$deviceType = 'desktop'; // Assume device to be desktop by default

		if ( PHP_SAPI === 'cli' ) {
			self::$isMobile = false;
		}

		if ( isset( self::$isMobile ) ) {
			return self::$isMobile;
		}


		// Check for existance of the X-UA-DEVICE header ( Somebody already did the work for us)
		$requestContext = RequestContext::getMain();
		if ( $requestContext->getRequest() === null ) {
			throw new MWException( "Error: no context or request! you can't use ". __METHOD__ . " here." );
		}
		$deviceHeader = $requestContext->getRequest()->getHeader( 'X-UA-DEVICE' );
		if ( $deviceHeader ) {
			self::$isMobile = ( $deviceHeader === 'mobile' );
			self::$deviceType = $deviceHeader;
		} elseif ( self::getAMF() ) {
			// If not, check if Apache Mobile Filter is in use
			self::$isMobile = true;
		} else {
			// And finally, try PHP mobile-detect
			$detect = new Mobile_Detect;
			self::$isMobile = $detect->isMobile();

			if ( self::$isMobile === true ) {
				self::$deviceType = $detect->isTablet() ? 'tablet' : 'mobile';
			}
		}

		// header( 'X-UA-DEVICE: ' . self::$isMobile ? 'mobile' : 'desktop' );
		// header( 'Vary: X-UA-DEVICE' );

		return self::$isMobile;
	}

	/**
	 * Check for mobile device when using Apache Mobile Filter (AMF)
	 *
	 * IF AMF is enabled, make sure we use it to detect mobile devices.
	 * Tablets are currently served desktop site.
	 *
	 * AMF docs: http://wiki.apachemobilefilter.org/
	 *
	 * This part was forked from Extension:MobileFrontend (MobileContext.php)
	 *
	 * @return bool
	 */
	public static function getAMF() {
		global $wgMobileDetectTabletIsMobile;

		$amf = isset( $_SERVER['AMF_DEVICE_IS_MOBILE'] ) && $_SERVER['AMF_DEVICE_IS_MOBILE'] === 'true';
		if ( !$wgMobileDetectTabletIsMobile && $amf && isset( $_SERVER['AMF_DEVICE_IS_TABLET'] ) ) {
			$amf &= $_SERVER['AMF_DEVICE_IS_TABLET'] === 'false';
		}

		if ( $amf ) {
			self::$deviceType = 'mobile';
		} elseif ( isset( $_SERVER['AMF_DEVICE_IS_TABLET'] ) && $_SERVER['AMF_DEVICE_IS_TABLET'] === 'true' ) {
			self::$deviceType = 'tablet';
		}

		return $amf;
	}


}
