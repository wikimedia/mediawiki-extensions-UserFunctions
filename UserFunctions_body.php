<?php

class ExtUserFunctions {

	function clearState(&$parser) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	function ifanon( &$parser, $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();
	 
		if($wgUser->isAnon()){
			return $then;
		}
		return $else;
	}
     
	function ifanonObj( &$parser, $frame, $args ) {
		global $wgUser;
			
		if($wgUser->isAnon()){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	} 
     
	function ifblocked( &$parser, $then = '', $else = '' ) {
       		global $wgUser;
		$parser->disableCache();
     
		if($wgUser->isBlocked()) {
		    	return $then;
	    	}
	    	return $else;
	}
	 
	function ifblockedObj( &$parser, $frame, $args ) {
		global $wgUser;
			
		if($wgUser->isBlocked()){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	} 
	
	function ifsysop( &$parser, $then = '', $else = '' ) {
	    	global $wgUser;
	    	$parser->disableCache();
     
	    	if($wgUser->isAllowed('protect')) {
			return $then;
	    	}
	    	return $else;
	}
     
	function ifsysopObj( &$parser, $frame, $args ) {
		global $wgUser;
			
		if($wgUser->isAllowed('protect')){
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}

	} 
	   
	function ifingroup( &$parser, $grp = '', $then = '', $else = '' ) {
		global $wgUser;
		$parser->disableCache();

		if($grp!==''){
			if(in_array($grp,$wgUser->getEffectiveGroups())) {
				return $then;
			}
			return $else;
		} else {
			return $else;
		}
	}

	function ifingroupObj( &$parser, $frame, $args ) {
		global $wgUser;
		$grp = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
	
		if($grp!==''){
			if(in_array($grp,$wgUser->getEffectiveGroups())) {
				return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
			}
			return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
		} else {
			return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
		}
	} 
     
	function realname( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();
	 
		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getRealName();
	}
     
	function username( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();
	 
		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getName();
	}
     
	function useremail( &$parser, $alt = '' ) {
		global $wgUser;
		$parser->disableCache();
	 
		if($wgUser->isAnon() && $alt!=='') {
			return $alt;
		}
		return $wgUser->getEmail();
	}
     
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
     
	function ip( &$parser ) {
		$parser->disableCache();
		return wfGetIP();
	}

}
