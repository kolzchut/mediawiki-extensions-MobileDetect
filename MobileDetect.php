<?php

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MobileDetect',
	'version' => '2.1a',
	'license-name' => 'GPL-2.0+',
	'descriptionmsg' => 'mobiledetect-desc',
	'author' => array( 'Matthew Tran', 'Luis Felipe Schenone', 'Dror S. [FFS]' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:MobileDetect',
);

$wgExtensionMessagesFiles['MobileDetect'] = __DIR__ . '/MobileDetect.i18n.php';
$wgMessagesDirs['MobileDetect'] = __DIR__ . '/i18n';

$wgAutoloadClasses['MobileDetect'] = __DIR__ . '/MobileDetect.body.php';
$wgAutoloadClasses['MobileDetectHooks'] = __DIR__ . '/MobileDetect.hooks.php';

$wgHooks['ParserFirstCallInit'][] = 'MobileDetectHooks::onParserFirstCallInit';
$wgHooks['PageRenderingHash'][] = 'MobileDetectHooks::onPageRenderingHash';
$wgHooks['BeforePageDisplay'][] = 'MobileDetectHooks::onBeforePageDisplay';
$wgHooks['RequestContextCreateSkin'][] = 'MobileDetectHooks::onRequestContextCreateSkin';



// Configuration
$wgMobileDetectSkin = null; // Set a skin for mobile, e.g. 'SkinVector' for Vector

// @TODO make $wgMobileDetectTabletIsMobile actually work
$wgMobileDetectTabletIsMobile = false;


// Backwards compatibility
function mobiledetect() {
	return MobileDetect::isMobile();
}
