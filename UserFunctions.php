<?php
/**
 * UserFunctions extension - Provides a set of dynamic parser functions that trigger on the current user.
 * @version 2.0 - 2011/12/13 (Based on ParserFunctions)
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
 * @copyright (C) 2006 Algorithm
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupUserFunctions';
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'UserFunctions',
	'version' => '2.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UserFunctions',
	'author' => array( 'Algorithm ', 'Toniher', 'Kghbln', 'al.' ),   
	'description' => 'Enhance parser with user functions',
	'descriptionmsg' => 'ufunc_desc',
);
 

$wgAutoloadClasses['ExtUserFunctions'] = dirname(__FILE__).'/UserFunctions_body.php';
$wgExtensionMessagesFiles['UserFunctions'] = dirname( __FILE__ ) . '/UserFunctions.i18n.php';
$wgExtensionMessagesFiles['UserFunctionsMagic'] = dirname( __FILE__ ) . '/UserFunctions.i18n.magic.php';


function wfSetupUserFunctions() {
	global $wgUFHookStub, $wgHooks;

	$wgUFHookStub = new UserFunctions_HookStub;

	$wgHooks['ParserFirstCallInit'][] = array( &$wgUFHookStub, 'registerParser' );
	$wgHooks['ParserClearState'][] = array( &$wgUFHookStub, 'clearState' );
}

/**
 * Stub class to defer loading of the bulk of the code until a User function is
 * actually used.
 */
class UserFunctions_HookStub {
	var $realObj;

	function registerParser( &$parser ) {

		if ( defined( get_class( $parser ) . '::SFH_OBJECT_ARGS' ) ) {
			// These functions accept DOM-style arguments
			$parser->setFunctionHook( 'ifanon', array( &$this, 'ifanonObj' ), SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifblocked', array( &$this, 'ifblockedObj' ), SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifsysop', array( &$this, 'ifsysopObj' ), SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifingroup', array( &$this, 'ifingroupObj' ), SFH_OBJECT_ARGS );
		} else {
			$parser->setFunctionHook( 'ifanon', array( &$this, 'ifanon' ) );
			$parser->setFunctionHook( 'ifblocked', array( &$this, 'ifblocked' ) );
			$parser->setFunctionHook( 'ifsysop', array( &$this, 'ifsysop' ) );
			$parser->setFunctionHook( 'ifingroup', array( &$this, 'ifingroup' ) );
		}	 
	
		$parser->setFunctionHook( 'realname', array( &$this, 'realname' ) );
		$parser->setFunctionHook( 'username', array( &$this, 'username' ) );
		$parser->setFunctionHook( 'useremail', array( &$this, 'useremail' ) );
		$parser->setFunctionHook( 'nickname', array( &$this, 'nickname' ) );
		$parser->setFunctionHook( 'ip', array( &$this, 'ip' ) );
		
		return true;
	}

	/** Defer ParserClearState */
	function clearState( &$parser ) {
		if ( !is_null( $this->realObj ) ) {
			$this->realObj->clearState( $parser );
		}
		return true;
	}

	/** Pass through function call */
	function __call( $name, $args ) {
		if ( is_null( $this->realObj ) ) {
			$this->realObj = new ExtUserFunctions;
			$this->realObj->clearState( $args[0] );
		}
		return call_user_func_array( array( $this->realObj, $name ), $args );
	}
}
