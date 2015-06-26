<?php

class MobileDetectHooks {

	public static function onPageRenderingHash( &$confstr, User $user, $optionsUsed ) {
		if ( MobileDetect::isMobile() ) {
			$confstr .= "!mobile";
		}

		return true;
	}

	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'nomobile', 'MobileDetect::nomobile' );
		$parser->setHook( 'mobileonly', 'MobileDetect::mobileonly' );

		return true;
	}



	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		if ( MobileDetect::isMobile() ) {
			//$out->setTarget( 'mobile' );
		}

		return true;
	}

	/**
	 * RequestContextCreateSkin hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/RequestContextCreateSkin
	 *
	 * @param IContextSource $context
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onRequestContextCreateSkin( $context, &$skin ) {
		global $wgMobileDetectSkin;

		if ( MobileDetect::isMobile()
		     && isset( $wgMobileDetectSkin )
		     && class_exists( $wgMobileDetectSkin )
		) {
			$skin = new $wgMobileDetectSkin( $context );
			return false;
		}

		return true;

	}

}
