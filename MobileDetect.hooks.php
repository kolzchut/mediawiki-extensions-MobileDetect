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
	 *
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


	/**
	 * MakeGlobalVariablesScript hook handler
	 * For values that depend on the current page, user or request state.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 * @param &$vars array Variables to be added into the output
	 * @param $out OutputPage instance calling the hook
	 * @return bool true in all cases
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$vars['wgIsMobile'] = MobileDetect::isMobile();
		$vars['wgMobileDetectDeviceType'] = MobileDetect::getDeviceType();

		return true;
	}


}
