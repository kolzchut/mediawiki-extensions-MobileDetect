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

$wgHooks['ParserFirstCallInit'][] = 'MobileDetect::onParserFirstCallInit';
$wgHooks['PageRenderingHash'][] = 'MobileDetect::onPageRenderingHash';


// Configuration
// @TODO make this actually work
$wgMobileDetectTabletIsMobile = false;
// @TODO make this actually work - simplest way, just set skin according to detection
$wgMobileDetectSkin = null;


// Backwards compatibility
function mobiledetect() {
	return MobileDetect::isMobile();
}
