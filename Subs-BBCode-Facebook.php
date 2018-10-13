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

function BBCode_Facebook_Theme()
{
	global $context, $user_info, $modSettings, $sourcedir;
	
	$lang = (!empty($user_info['facebook_lang']) ? $user_info['facebook_lang'] : 
		(isset($modSettings['fb_default_lang']) ? $modSettings['fb_default_lang'] : false));
	if (empty($lang))
	{
		$dummy_vars = array();
		BBCode_Facebook_Settings($dummy_vars);
		require_once($sourcedir . '/Subs-Admin.php');
		$lang = (!empty($modSettings['fb_default_lang']) ? $modSettings['fb_default_lang'] : 'en-US');
		unset($modSettings['fb_default_lang']);
		updateSettings(array('fb_default_lang' => $lang));
	}
	if (!empty($lang))
		$context['html_headers'] .= '
	<script src="//connect.facebook.net/' . $lang . '/sdk.js#xfbml=1&version=v2.2" async></script>';
}

function BBCode_Facebook(&$bbc)
{
	// Format: [facebook width=x]{facebook URL}[/facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+)'),
			'lang' => array('optional' => true),
		),
		'content' => '<div class="fb-post" data-href="$1" data-width="{width}"></div>',
		'validate' => 'BBCode_Facebook_Validate',
		'disabled_content' => '$1',
	);

	// Format: [facebook]{Facebook URL}[/Facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'content' => '<div class="fb-post" data-href="$1" data-width="500"></div>',
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
	if (empty($data))
		return ($tag['content'] = '');
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	if (!preg_match('#(http|https)://(|(.+?).)facebook.com/(.+?)/posts/(\d+)(|((/|\?)(.+?)))#i', $data, $parts))
		return ($tag['content'] = '');
}

function BBCode_Facebook_Settings(&$config_vars)
{
	global $sourcedir, $txt, $modSettings, $context;

	if (($langs = cache_get_data('fb_langs', 3600)) == null)
	{
		require_once($sourcedir . '/Subs-Package.php');
		$search_results = fetch_web_data('http://www.facebook.com/translations/FacebookLocales.xml');
		$pattern = '~<\?xml version=(.+?)>*(<locales>.+?</locales>)~is';
		if (!$search_results || preg_match($pattern, $search_results, $matches) != true)
			return BBCode_Facebook_Default_Settings($config_vars);
		loadClassFile('Class-Package.php');
		$results = new xmlArray($search_results, false);
		if (!$results->exists('locales'))
			return BBCode_Facebook_Default_Settings($config_vars);
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
		cache_put_data('fb_langs', ($context['facebook_lang'] = $langs), 3600);
	}
	if (empty($langs))
		return BBCode_Facebook_Default_Settings($config_vars);
	if (empty($modSettings['fb_default_lang']))
		$modSettings['fb_default_lang'] = (isset($langs[$txt['lang_locale']]) ? $txt['lang_locale'] : 'en_US');
	$config_vars[] = array('select', 'fb_default_lang', $langs);
}

function BBCode_Facebook_Default_Settings(&$config_vars)
{
	global $modSettings;
	if (empty($modSettings['fb_default_lang']))
		$modSettings['fb_default_lang'] = 'en_US';
	$config_vars[] = array('text', 'fb_default_lang');
}

function BBCode_Facebook_Profile(&$profile_fields)
{
	global $txt, $user_info;

	$profile_fields['facebook_lang'] = array(
		'type' => 'select',
		'label' => $txt['facebook_lang'],
		'options' => 'global $context; return $context[\'facebook_lang\'];',
		'permission' => 'profile_extra',
		'value' => $user_info['facebook_lang'],
		'preload' => create_function('', '
			global $context, $txt;
			if (($context[\'facebook_lang\'] = cache_get_data(\'fb_langs\', 86400)) == null)
			{
				$dummy_vars = array();
				BBCode_Facebook_Settings($dummy_vars);
			}
			$context[\'facebook_lang\'] = array_merge(
				array(\'\' => $txt[\'theme_forum_default\']),
				is_array($context[\'facebook_lang\']) ? $context[\'facebook_lang\'] : array());
			return is_array($context[\'facebook_lang\']);
		'),
	);
}

?>