<?php

/**
 * Rah_swap plugin for Textpattern CMS
 *
 * @author Jukka Svahn
 * @date 2011-
 * @license GNU GPLv2
 *
 * Copyright (C) 2013 Jukka Svahn <http://rahforum.biz>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	function rah_swap($atts, $thing = null)
	{	
		global $rah_swap, $txpcfg, $prefs, $is_article_body;
		static $default_cfg = null;

		if ($default_cfg === null)
		{
			$default_cfg = $txpcfg;
		}

		if (!php('echo 1;'))
		{
			return '';
		}

		if (isset($atts['link']))
		{
			if (!isset($rah_swap[$atts['link']]))
			{
				trigger_error(gTxt('invalid_attribute_value', array('{name}' => $atts['link'])));
				return;
			}

			$atts = (array) $rah_swap[$atts['link']];
		}

		$opt = lAtts(array(
			'link'         => '',
			'reset'        => 0,
			'db'           => null,
			'user'         => '',
			'pass'         => '',
			'host'         => 'localhost',
			'dbcharset'    => 'utf8',
			'client_flags' => 0,
		), $atts);

		extract($opt);

		if (count($atts) == 1 && $db !== null)
		{
			mysql_select_db($db, $GLOBALS['DB']->link);
		}
		else if (!$reset)
		{
			mysql_close($GLOBALS['DB']->link);
			$txpcfg = $opt;
			$GLOBALS['DB'] = new DB;
		}

		if ($thing !== null)
		{
			$reset = 1;
			$r = parse($thing);
		}
		else
		{
			$r = '';
		}

		if ($reset)
		{
			mysql_close($GLOBALS['DB']->link);
			$txpcfg = $default_cfg;
			$GLOBALS['DB'] = new DB;
		}

		return $r;
	}
?>