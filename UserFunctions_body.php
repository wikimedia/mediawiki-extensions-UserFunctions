<?php

class ExtUserFunctions {

	/**
	 * @param $parser Parser
	 * @return bool
	 */
	function clearState(&$parser) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	/**
	 * @param $parser Parser
	 * @param $then string
	 * @param $else string
	 * @return string
	 */
	function ifanon( &$parser, $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();
		if($wgUser->isAnon()){
			return $then;
		}
		return $else;
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */
	function ifanonObj( &$parser, $frame, $args ) {
		global $wgUser;
		if($wgUser->isAnon()){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param $parser Parser
	 * @param $then string
	 * @param $else string
	 * @return string
	 */
	function ifblocked( &$parser, $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($wgUser->isBlocked()) {
			return $then;
		}
		return $else;
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */
	function ifblockedObj( &$parser, $frame, $args ) {
		global $wgUser;
		if($wgUser->isBlocked()){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param $parser Parser
	 * @param $then string
	 * @param $else string
	 * @return string
	 */
	function ifsysop( &$parser, $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();
		if($wgUser->isAllowed('protect')) {
			return $then;
		}
		return $else;
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */
	function ifsysopObj( &$parser, $frame, $args ) {
		global $wgUser;
		if($wgUser->isAllowed('protect')){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param $parser Parser
	 * @param $grp string
	 * @param $then string
	 * @param $else string
	 * @return string
	 */
	function ifingroup( &$parser, $grp = '', $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($grp!=='' && in_array($grp,$wgUser->getEffectiveGroups())){
			return $then;
		}
		return $else;
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */
	function ifingroupObj( &$parser, $frame, $args ) {
		global $wgUser;
		$grp = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';

		if($grp!=='' && in_array($grp,$wgUser->getEffectiveGroups())){
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
		return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
	}

	/**
	 * @param $parser Parser
	 * @param $alt string
	 * @return String
	 */
	function realname( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getRealName();
	}

	/**
	 * @param $parser Parser
	 * @param $alt string
	 * @return String
	 */
	function username( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getName();
	}

	/**
	 * @param $parser Parser
	 * @param $alt string
	 * @return String
	 */
	function useremail( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getEmail();
	}

	/**
	 * @param $parser Parser
	 * @param $alt string
	 * @return String
	 */
	function nickname( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($wgUser->isAnon()) {
			if ( $alt!=='') {
				return $alt;
			}
			return $wgUser->getName();
		}
		$nickname = $wgUser->getOption( 'nickname' );
		$nickname = $nickname === '' ? $wgUser->getName() : $nickname;
		return $nickname;
	}

	/**
	 * @param $parser Parser
	 * @return string
	 */
	function ip( &$parser ) {
		$parser->disableCache();
		return wfGetIP();
	}

}
