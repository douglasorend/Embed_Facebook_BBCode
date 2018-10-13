<?php
/**********************************************************************************
* Subs-BBCode-Facebook.php
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

function BBCode_Facebook(&$bbc)
{
	// Format: [facebook width=x]{facebook URL}[/facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('optional' => true, 'match' => '(\d+)'),
			'lang' => array('optional' => true),
		),
		'content' => '{lang}|{width}',
		'validate' => 'BBCode_Facebook_Validate',
		'disabled_content' => '$1',
	);

	// Format: [facebook]{Facebook URL}[/Facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'content' => '|500',
		'validate' => 'BBCode_Facebook_Validate',
		'disabled_content' => '',
	);
}

function BBCode_Facebook_Button(&$buttons)
{
	$buttons[count($buttons) - 1][] = array(
		'image' => 'facebook',
		'code' => 'facebook',
		'description' => 'facebook',
		'before' => '[facebook]',
		'after' => '[/facebook]',
	);
}

function BBCode_Facebook_Validate(&$tag, &$data, &$disabled)
{
	global $modSettings;
	static $already_included = false;

	if (empty($data))
		return;
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	list($lang, $width) = explode('|', $tag['content']);
	$width = (empty($width) ? 500 : $width);
	$lang = (empty($lang) ? false : $lang);
	if (empty($lang) && empty($modSettings['fb_default_lang']))
	{
		$dummy_vars = array();
		BBCode_Facebook_Settings($dummy_vars);
		require_once($sourcedir.'/Subs-Admin.php');
		$temp = $modSettings['fb_default_lang'];
		unset($modSettings['fb_default_lang']);
		updateSettings(array('fb_default_lang' => $temp));
		$lang = $modSettings['fb_default_lang'];
	}
	$tag['content'] = (empty($already_included) ? '<script src="//connect.facebook.net/' . $lang . '/sdk.js#xfbml=1&version=v2.2" async></script>' : '') . 
		'<div class="fb-post" data-href="$1" data-width="' . $width . '"></div>';
	$already_included = true;
}

function BBCode_Facebook_Settings(&$config_vars)
{
	global $sourcedir, $txt, $modSettings;

	if (($langs = cache_get_data('fb_langs', 3600)) == null)
	{
		require_once($sourcedir . '/Subs-Package.php');
		$search_results = fetch_web_data('http://www.facebook.com/translations/FacebookLocales.xml');
		$pattern = '~<\?xml version=(.+?)>*(<locales>.+?</locales>)~is';
		if (!$search_results || preg_match($pattern, $search_results, $matches) != true)
		{
			$config_vars[] = array('text', 'fb_default_lang');
			return;
		}
		loadClassFile('Class-Package.php');
		$results = new xmlArray($search_results, false);
		if (!$results->exists('locales'))
		{
			$config_vars[] = array('text', 'fb_default_lang');
			return;
		}
		$results = $results->path('locales[0]');
		$langs = array();
		foreach ($results->set('locale') as $locale)
		{
			if (!$locale->exists('codes'))
				continue;
			$code = $locale->path('codes[0]');
			if (!$code->exists('code'))
				continue;
			$code = $code->path('code[0]');
			if (!$code->exists('standard'))
				continue;
			$code = $code->path('standard[0]');
			$langs[ $code->fetch('representation') ] = $locale->fetch('englishName');
		}
		cache_put_data('fb_langs', $langs, 3600);
	}
	if (empty($modSettings['fb_default_lang']))
		$modSettings['fb_default_lang'] = 'en_US';
	$config_vars[] = array('select', 'fb_default_lang', $langs);
}

?>