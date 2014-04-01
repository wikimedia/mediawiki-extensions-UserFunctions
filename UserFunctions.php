<?php
/**
 * UserFunctions extension - Provides a set of dynamic parser functions that trigger on the current user.
 * @version 2.4.3 - 2014/03/16 (Based on ParserFunctions)
 *
 * @link http://www.mediawiki.org/wiki/Extension:UserFunctions Documentation
 *
 * @file UserFunctions.php
 * @ingroup Extensions
 * @package MediaWiki
 * @author Algorithm
 * @author Lexw
 * @author Louperivois
 * @author Wikinaut
 * @author Kghbln
 * @author Toniher
 * @author Uckelman
 * @copyright (C) 2006 Algorithm
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * Enable Personal Data Functions
 * Set this to true if you want your users to be able to use the following functions:
 * realname, username, useremail, nickname, ip
 * WARNING: These functions can be used to leak your user's email addresses and real names.
 * If unsure, don't activate these features.
**/
$wgUFEnablePersonalDataFunctions = false;

/** Allow to be used in places such as SF form **/
$wgUFEnableSpecialContexts = true;

/** Restrict to certain namespaces **/
$wgUFAllowedNamespaces = array(
	NS_MEDIAWIKI => true
);

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'UserFunctions',
	'version' => '2.5.0',
	'url' => 'https://www.mediawiki.org/wiki/Extension:UserFunctions',
	'author' => array( 'Algorithm ', 'Toniher', 'Kghbln', 'Wikinaut', 'Reedy', '...' ),
	'descriptionmsg' => 'userfunctions-desc',
);

$wgAutoloadClasses['ExtUserFunctions'] = dirname(__FILE__).'/UserFunctions_body.php';
$wgMessagesDirs['UserFunctions'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['UserFunctions'] = dirname( __FILE__ ) . '/UserFunctions.i18n.php';
$wgExtensionMessagesFiles['UserFunctionsMagic'] = dirname( __FILE__ ) . '/UserFunctions.i18n.magic.php';

$wgHooks['ParserFirstCallInit'][] = 'wfRegisterUserFunctions';

/**
 * @param $parser Parser
 * @return bool
 */

function wfRegisterUserFunctions( $parser ) {
	global $wgUFEnablePersonalDataFunctions, $wgUFAllowedNamespaces, $wgUFEnableSpecialContexts;

	// Whether it's a Special Page or a Maintenance Script
	$special = false;

	// Initialize NS
	$title = RequestContext::getMain()->getTitle();
	$cur_ns = $title === null ? -1 : $title->getNamespace();
	if ( $cur_ns == -1 ) {
		$special = true;
	}

	$process = false;

	// As far it's not special case, check if current page NS is in the allowed list
	if (!$special) {
		if (isset($wgUFAllowedNamespaces[$cur_ns])) {
			if ($wgUFAllowedNamespaces[$cur_ns]) {
				$process = true;
			}
		}
	}
	else {
		if ($wgUFEnableSpecialContexts) {

                        if ($special) {
                                $process = true;
                        }
		}
	}

	if ($process) {
		// These functions accept DOM-style arguments

		$parser->setFunctionHook( 'ifanon', 'ExtUserFunctions::ifanonObj', SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifblocked', 'ExtUserFunctions::ifblockedObj', SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifsysop', 'ExtUserFunctions::ifsysopObj', SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'ifingroup', 'ExtUserFunctions::ifingroupObj', SFH_OBJECT_ARGS );

		if ($wgUFEnablePersonalDataFunctions) {
			$parser->setFunctionHook( 'realname', 'ExtUserFunctions::realname' );
			$parser->setFunctionHook( 'username', 'ExtUserFunctions::username' );
			$parser->setFunctionHook( 'useremail', 'ExtUserFunctions::useremail' );
			$parser->setFunctionHook( 'nickname', 'ExtUserFunctions::nickname' );
			$parser->setFunctionHook( 'ip', 'ExtUserFunctions::ip' );
		}

	}

	return true;
}
