<?php

/**
 * Rah_swap plugin for Textpattern CMS
 *
 * @author Jukka Svahn
 * @date 2011-
 * @license GNU GPLv2
 *
 * Copyright (C) 2012 Jukka Svahn <http://rahforum.biz>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	function rah_swap($atts, $thing=NULL) {
		
		global $rah_swap, $txpcfg;
		
		static $default_cfg = NULL;
		
		if($default_cfg === NULL)
			$default_cfg = $txpcfg;
		
		if(isset($atts['link']) && isset($rah_swap[$atts['link']]))
			$atts = (array) $rah_swap[$atts['link']];

		$opt = lAtts(array(
			'link' => '',
			'reset' => 0,
			'db' => NULL,
			'user' => '',
			'pass' => '',
			'host' => 'localhost',
			'dbcharset' => 'utf8',
			'client_flags' => 0,
		), $atts);
		
		extract($opt);
		
		if(count($atts) == 1 && $db !== NULL)
			mysql_select_db($db, $GLOBALS['DB']->link);

		else if(!$reset) {
			mysql_close($GLOBALS['DB']->link);
			$txpcfg = $opt;
			$GLOBALS['DB'] = new DB;
		}
		
		$r = !$reset && $thing !== NULL && ($reset = 1) ? parse($thing) : '';
		
		if($reset) {
			mysql_close($GLOBALS['DB']->link);
			$txpcfg = $default_cfg;
			$GLOBALS['DB'] = new DB;
		}
		
		return $r;
	}
?>