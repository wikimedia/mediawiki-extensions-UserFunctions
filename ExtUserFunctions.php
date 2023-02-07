<?php

use MediaWiki\MediaWikiServices;

class ExtUserFunctions {

	/**
	 * @param Parser $parser
	 * @return bool
	 */
	public static function clearState( Parser $parser ) {
		$parser->pf_ifexist_breakdown = [];

		return true;
	}

	/**
	 * Register ParserClearState hook.
	 * We defer this until needed to avoid the loading of the code of this file
	 * when no parser function is actually called.
	 */
	public static function registerClearHook() {
		static $done = false;

		if ( !$done ) {
			global $wgHooks;

			$wgHooks['ParserClearState'][] = __CLASS__ . '::clearState';

			$done = true;
		}
	}

	/**
	 * @return User
	 */
	private static function getUserObj() {
		return RequestContext::getMain()->getUser();
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return string
	 */
	public static function ifanonObj( Parser $parser, PPFrame $frame, array $args ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( !$pUser->isRegistered() ) {
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return string
	 */
	public static function ifblockedObj( Parser $parser, PPFrame $frame, array $args ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( $pUser->getBlock() ) {
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return string
	 */
	public static function ifsysopObj( Parser $parser, PPFrame $frame, array $args ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( $pUser->isAllowed( 'protect' ) ) {
			return isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		} else {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		}
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return string
	 */
	public static function ifingroupObj( Parser $parser, PPFrame $frame, array $args ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		$grp = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';

		if ( $grp !== '' ) {
			# Considering multiple groups
			$allgrp = explode( ',', $grp );

			$userGroups = MediaWikiServices::getInstance()->getUserGroupManager()
				->getUserEffectiveGroups( $pUser );
			foreach ( $allgrp as $elgrp ) {
				if ( in_array( trim( $elgrp ), $userGroups ) ) {
					return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
				}
			}
		}

		return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
	}

	/**
	 * @note usage is determined by $wgUFEnabledPersonalDataFunctions
	 * @see onParserFirstCallInit()
	 *
	 * @param Parser $parser
	 * @param string $alt
	 * @return string
	 */
	public static function realname( Parser $parser, string $alt = '' ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( !$pUser->isRegistered() && $alt !== '' ) {
			return $alt;
		}

		return $pUser->getRealName();
	}

	/**
	 * @note usage is determined by $wgUFEnabledPersonalDataFunctions
	 * @see onParserFirstCallInit()
	 *
	 * @param Parser $parser
	 * @param string $alt
	 * @return string
	 */
	public static function username( Parser $parser, string $alt = '' ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( !$pUser->isRegistered() && $alt !== '' ) {
			return $alt;
		}

		return $pUser->getName();
	}

	/**
	 * @note usage is determined by $wgUFEnabledPersonalDataFunctions
	 * @see onParserFirstCallInit()
	 *
	 * @param Parser $parser
	 * @param string $alt
	 * @return string
	 */
	public static function useremail( Parser $parser, string $alt = '' ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( !$pUser->isRegistered() && $alt !== '' ) {
			return $alt;
		}

		return $pUser->getEmail();
	}

	/**
	 * @note usage is determined by $wgUFEnabledPersonalDataFunctions
	 * @see onParserFirstCallInit()
	 *
	 * @param Parser $parser
	 * @param string $alt
	 * @return string
	 */
	public static function nickname( Parser $parser, string $alt = '' ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$pUser = self::getUserObj();

		if ( !$pUser->isRegistered() ) {
			if ( $alt !== '' ) {
				return $alt;
			}

			return $pUser->getName();
		}

		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();

		$nickname = $userOptionsLookup->getOption( $pUser, 'nickname' );
		$nickname = $nickname === '' ? $pUser->getName() : $nickname;

		return $nickname;
	}

	/**
	 * @note usage is determined by $wgUFEnabledPersonalDataFunctions
	 * @see onParserFirstCallInit()
	 *
	 * @param Parser $parser
	 * @return string
	 */
	public static function ip( Parser $parser ) {
		$parser->getOutput()->updateCacheExpiry( 0 );
		$request = self::getUserObj()->getRequest();

		return $request->getIP();
	}

	/**
	 * @param Parser $parser
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		global $wgUFEnabledPersonalDataFunctions, $wgUFAllowedNamespaces, $wgUFEnableSpecialContexts;

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
		if ( !$special ) {
			if ( isset( $wgUFAllowedNamespaces[$cur_ns] ) ) {
				if ( $wgUFAllowedNamespaces[$cur_ns] ) {
					$process = true;
				}
			}
		} elseif ( $wgUFEnableSpecialContexts ) {
			if ( $special ) {
				$process = true;
			}
		}

		if ( $process ) {
			// These functions accept DOM-style arguments
			$parser->setFunctionHook( 'ifanon', [ __CLASS__, 'ifanonObj' ], Parser::SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifblocked', [ __CLASS__, 'ifblockedObj' ], Parser::SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifsysop', [ __CLASS__, 'ifsysopObj' ], Parser::SFH_OBJECT_ARGS );
			$parser->setFunctionHook( 'ifingroup', [ __CLASS__, 'ifingroupObj' ], Parser::SFH_OBJECT_ARGS );

			foreach ( $wgUFEnabledPersonalDataFunctions as $function ) {
				if ( method_exists( __CLASS__, $function ) ) {
					$parser->setFunctionHook( $function, [ __CLASS__, $function ] );
				}
			}
		}
	}
}
