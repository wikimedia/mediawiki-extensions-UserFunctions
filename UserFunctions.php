<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'UserFunctions' );

	$wgMessagesDirs['UserFunctions'] = __DIR__ . '/i18n';

	$wgExtensionMessagesFiles['UserFunctionsMagic'] = __DIR__ . '/UserFunctions.i18n.magic.php';

	wfWarn(
		'Deprecated PHP entry point used for the UserFunctions extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the UserFunctions extension requires MediaWiki 1.35+' );
}
