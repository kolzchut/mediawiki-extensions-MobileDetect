<?php

class MobileDetectHooks {

	/**
	 * PageRenderingHash hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageRenderingHash
	 *
	 * @param string &$confstr Reference to a hash key string which can be modified
	 * @param User $user User object that is requesting the page
	 * @param array &$forOptions Array of options used to generate the $confstr hash key
	 *
	 * @return bool
	 */
	public static function onPageRenderingHash( &$confstr, User $user, &$forOptions ) {
		if ( MobileDetect::isMobile() ) {
			$confstr .= "!mobile";
		}

		return true;
	}

	/**
	 * Register parser hook
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param $parser Parser
	 * @return bool
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'nomobile', 'MobileDetect::nomobile' );
		$parser->setHook( 'mobileonly', 'MobileDetect::mobileonly' );

		return true;
	}

	/**
	 * BeforePageDisplay hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 *
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @return bool true in all cases
	 */
	public static function onBeforePageDisplay( &$out, &$sk ) {
		global $wgMobileDetectFilterModules, $wgMobileDetectModuleWhitelist;

		// Not mobile? No need to do anything at all
		if ( !MobileDetect::isMobile() ) {
			return true;
		}

		// set the mobile target for ResourceLoader modules
		if ( $wgMobileDetectFilterModules === true ) {
			$moduleStyles = [];
			$out->setTarget( 'mobile' );
			$out->addVaryHeader( 'User-Agent' );

			// Allow other extension to add to the whitelist
			Hooks::run( 'MobileDetectWhitelistModules', [ &$wgMobileDetectModuleWhitelist, &$moduleStyles ] );
			$wgMobileDetectModuleWhitelist = array_unique( $wgMobileDetectModuleWhitelist );

			// Add whitelisted modules to page
			if ( !empty( $wgMobileDetectModuleWhitelist ) ) {
				$mobileModules = self::getModuleDefinitions(
					$out->getResourceLoader(),
					$wgMobileDetectModuleWhitelist
				);

				$out->getResourceLoader()->register( $mobileModules );


				$out->getOutput()->addModules( array_keys( $mobileModules ) );

				//$out->addModuleStyles( $mobileModules );

				$out->getResourceLoader()->isModuleRegistered( 'mediawiki.legacy.shared' );
			}
		}

		// Allow other extensions to modify stuff only for mobile mode
		Hooks::run( 'BeforePageDisplayMobile', [ &$out, &$sk ] );

		return true;
	}

	/**
	 * RequestContextCreateSkin hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/RequestContextCreateSkin
	 *
	 * @param IContextSource $context
	 * @param Skin|null|string $skin
	 *
	 * @return bool
	 */
	public static function onRequestContextCreateSkin( $context, &$skin ) {
		global $wgMobileDetectSkin;

		if ( MobileDetect::isMobile()
		     && isset( $wgMobileDetectSkin )
		) {
			$skin = Skin::normalizeKey( $wgMobileDetectSkin );
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
	 * @param OutputPage $out instance calling the hook
	 * @return bool true in all cases
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, $out ) {
		$vars['wgIsMobile'] = MobileDetect::isMobile();
		$vars['wgMobileDetectDeviceType'] = MobileDetect::getDeviceType();

		return true;
	}


	/**
	 * Define some desktop modules as mobile ones, according to a whitelist
	 *
	 * @param ResourceLoader &$resourceLoader
	 * @param $modules array
	 *
	 * @return null|array
	 */
	public static function getModuleDefinitions( $resourceLoader, &$modules ) {
		if ( !is_array( $modules ) || empty( $modules ) ) {
			return null;
		}

		$mobileModules = [];
		foreach ( $modules as $module ) {
			$mobileDefinition = self::getModuleDefinition( $module, $resourceLoader );
			if (
				$mobileDefinition !== null &&
				( $mobileDefinition[ 'targets' ] === null || $mobileDefinition[ 'targets' ] === [ 'desktop' ] )
			) {
				$mobileDefinition += [ 'targets' => [ 'mobile' ] ];
				$newModuleName = $module . '.mobile';

				// $resourceLoader->register( $newModuleName, $mobileDefinition );
				// $wgResourceModules[ $newModuleName ] = $mobileDefinition;
				// $mobileModules[] = $newModuleName;
				$mobileModules[ $newModuleName ] = $mobileDefinition;
			}
		}

		return $mobileModules;

	}

	/**
	 * @param String $name the name of a ResourceLoader module
	 * @param ResourceLoader $resourceLoader
	 *
	 * @return null|[]
	 */
	protected static function getModuleDefinition( $name, ResourceLoader &$resourceLoader ) {
		global $IP, $wgResourceModules;
		$moduleDefinition = null;

		if ( $resourceLoader->isModuleRegistered( $name ) ) {
			if ( isset( $wgResourceModules[ $name ] ) ) {
				$moduleDefinition = $wgResourceModules[ $name ];
			} else {
				// The module isn't defined using $wgResourceModules, so it's probably a core module
				$coreModules      = ( include "$IP/resources/Resources.php" );
				$moduleDefinition = $coreModules[ $name ];
			}
		}

		return $moduleDefinition;
	}

}
