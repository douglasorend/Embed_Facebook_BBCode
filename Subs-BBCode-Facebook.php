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
	global $context, $user_info, $modSettings, $sourcedir, $settings;
	
	// Make sure that we have a default language specified in the $modSettings array:
	if (empty($modSettings['fb_default_lang']))
	{
		$dummy_vars = array();
		BBCode_Facebook_Settings($dummy_vars);
		require_once($sourcedir . '/Subs-Admin.php');
		$lang = (!empty($modSettings['fb_default_lang']) ? $modSettings['fb_default_lang'] : 'en-US');
		unset($modSettings['fb_default_lang']);
		updateSettings(array('fb_default_lang' => $lang));
	}

	// Make sure that the user's facebook lang is set to default language if none specified:
	$user_info['facebook_lang'] = (!empty($user_info['facebook_lang']) ? $user_info['facebook_lang'] : 
		(!empty($modSettings['fb_default_lang']) ? $modSettings['fb_default_lang'] : 'en-US'));

	// Add the headers we need for the forum:
	$context['html_headers'] .= '
	<script>
		window.fbAsyncInit = function() {
			FB.init();
		};

		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/' . $user_info['facebook_lang'] . '/sdk.js#xfbml=1&version=v2.3";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, "script", "facebook-jssdk"));
	</script>';
}

function BBCode_Facebook(&$bbc)
{
	global $modSettings;

	// Format: [facebook width=x]{facebook URL}[/facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+|auto)'),
			'lang' => array('optional' => true),
		),
		'content' => '{width}',
		'validate' => 'BBCode_Facebook_Validate',
		'disabled_content' => '$1',
	);

	// Format: [facebook]{Facebook URL}[/Facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'content' => '0',
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
	global $modSettings, $txt;
	
	// Set up for a run through the bbcode:
	$tag['content'] = $txt['fb_invalid'];
	if (empty($data))
		return;
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
		
	// Determine approprate width for Facebook display:
	$def_width = ($tag['content'] == 'auto' ? 'auto' : (int) $tag['content']);
	$vid_width = (empty($def_width) && !empty($modSettings['fb_default_video_width']) ? $modSettings['fb_default_video_width'] : $def_width);
	$pst_width = (empty($def_width) && !empty($modSettings['fb_default_post_width']) ? $modSettings['fb_default_post_width'] : $def_width);

	// Is this a Facebook post URL?
	if (preg_match('#(https?):\/\/(|(.+?).)facebook.com/[\d\w\.\_\-]+/posts/(\d+)(\?(.+?=(\d)(|amp))+?)?#i', $data, $parts))
		$tag['content'] = '<div class="fb-post" data-href="' . $data . '" data-width="' . (!empty($pst_width) ? $pst_width : 'auto') . '"></div>';
	// ---OR--- Is this a regular Facebook permalink URL?
	elseif (preg_match('#(https?):\/\/(|(.+?).)facebook.com/permalink.php\?(story_fbid=(\d+))?(&amp;)?(id=(\d+))?#i', $data, $parts))
		$tag['content'] = '<div class="fb-post" data-href="' . $data . '" data-width="' . (!empty($vid_width) ? $vid_width : 'auto') . ' data-allowfullscreen="true"></div>';
	// ---OR--- Is this a Facebook video URL?
	elseif (preg_match('#(https?):\/\/(|(.+?).)facebook.com/([\d\w\.\_\-]+/videos/|video.php\?v=)(\d+)#i', $data, $parts))
		$tag['content'] = '<div class="fb-video" data-href="https://www.facebook.com/video.php?v=' . $parts[5] . '" data-width="' . (!empty($vid_width) ? $vid_width : 'auto') . '" data-allowfullscreen="true"></div>';
	// ---OR--- Is this a Facebook photo URL?
	elseif (preg_match('#(https?):\/\/(|(.+?).)facebook.com/photo.php\?fbid=(\d+)&amp;set=(.+?)&amp;type=(\d+)#i', $data, $parts))
		$tag['content'] = '<div' . (!empty($vid_width) ? ' width="' . $vid_width . '"' : '') . ' class="fb-post" data-allowfullscreen="true" data-href="' . $data . '"></div>';
	// ---OR--- Is this a Facebook photo URL?
	elseif (preg_match('#(https?):\/\/(|(.+?).)facebook.com/([\d\w\.\_\-]+)/photos/(\w)+\.(\d+)(?:\.(\d+)\.(\d+))?/(\d+)/\?type=(\d+)(?:&amp;theater)?#i', $data, $parts))
		$tag['content'] = '<div' . (!empty($vid_width) ? ' width="' . $vid_width . '"' : '') . ' class="fb-post" data-allowfullscreen="true" data-href="' . $data . '"></div>';
	// ---OR--- Is this a Facebook event permalink URL?
	elseif (preg_match('#(https?):\/\/(|(.+?).)facebook.com/events/(\d+)/permalink/(\d+)#i', $data, $parts))
		$tag['content'] = '<div class="fb-post" data-href="' . $data . '" data-width="' . (!empty($vid_width) ? $vid_width : 'auto') . '"></div>';
	// [[ LAST CASE ]]=> If no valid FaceBook URL, then return to caller:
	else
		return;

	// Are we running Tapatalk?  If so, return ONLY the link!!!
	if (defined('IN_MOBIQUO'))
		$tag['content'] = '<a href="' . $data . '">' . $data . '</a>';
	// Otherwise, add the Facebook URL if admin says so:
	elseif (!empty($modSettings['fb_include_link']))
		$tag['content'] .= '<br /><a href="' . $data . '">' . $data . '</a>';
}

function BBCode_Facebook_Settings(&$config_vars)
{
	global $txt, $modSettings;

	if (($langs = cache_get_data('fb_langs', 3600)) == null)
		BBCode_Facebook_Get_Languages($langs, $config_vars);
	if (empty($langs))
		BBCode_Facebook_No_List($config_vars);
	else
	{
		if (empty($modSettings['fb_default_lang']))
			$modSettings['fb_default_lang'] = (isset($langs[$txt['lang_locale']]) ? $txt['lang_locale'] : 'en_US');
		$config_vars[] = array('select', 'fb_default_lang', $langs);
	}
	$config_vars[] = array('int', 'fb_default_post_width');
	$config_vars[] = array('int', 'fb_default_video_width');
	$config_vars[] = array('check', 'fb_include_link');
}

function BBCode_Facebook_Get_Languages(&$langs, &$config_vars)
{
	global $sourcedir, $context;

	$langs = array();
	require_once($sourcedir . '/Subs-Package.php');
	$search_results = fetch_web_data('http://www.facebook.com/translations/FacebookLocales.xml');
	$pattern = '~<\?xml version=(.+?)>*(<locales>.+?</locales>)~is';
	if (!$search_results || preg_match($pattern, $search_results, $matches) != true)
		return BBCode_Facebook_No_List($config_vars);
	loadClassFile('Class-Package.php');
	$results = new xmlArray($search_results, false);
	if (!$results->exists('locales'))
		return BBCode_Facebook_No_List($config_vars);
	$results = $results->path('locales[0]');
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

function BBCode_Facebook_No_List(&$config_vars)
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
		'label' => $txt['fb_default_lang'],
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

function BBCode_Facebook_Embed(&$message, &$smileys, &$cache_id, &$parse_tags)
{
	if ($message === false)
		return;
	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$0[/url]' : '[facebook]$0[/facebook]');
	$pattern = '~(?<=[\s>\.(;\'"]|^)(https?\:\/\/)(|www\.)facebook.com\/(?:[\d\w\.\_\-]+?/posts/|.+?/videos/|video.php\?v=)(\d+)(?:(&amp;|\?)(.+?=([\w\d\{\}\%])+)+?)?+\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	$pattern = '~(?<=[\s>\.(;\'"]|^)(https?\:\/\/)(|www\.)facebook.com\/photo.php\?fbid=(\d+)&amp;set=(.+?)&amp;type=(\d+)(?:&amp;theater)?+\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	$pattern = '~(?<=[\s>\.(;\'"]|^)(https?\:\/\/)(|www\.)facebook.com\/permalink.php\?(story_fbid=(\d+))?(&amp;)?(id=(\d+))?+\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	$pattern = '~(?<=[\s>\.(;\'"]|^)(https?):\/\/(|(.+?).)facebook.com/([\d\w\.\_\-]+)/photos/(\w)+\.(\d+)(?:\.(\d+)\.(\d+))?/(\d+)/\?type=(\d+)(?:&amp;theater)?\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	$pattern = '~(?<=[\s>\.(;\'"]|^)(https?):\/\/(|www\.)facebook.com/events/(\d+)/permalink/(\d+)/?[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	$pattern = '~\[facebook\](https?\:\/\/)?(|www\.)\[facebook\](.+?)\[/facebook\]\[/facebook\]~';
	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$1$2$3[/url]' : '[facebook]$1$2$3[/facebook]');
	$message = preg_replace($pattern, $replace, $message);
	if (strpos($cache_id, 'sig') !== false)
		$message = preg_replace('#\[facebook.*\](.*)\[\/facebook\]#i', '[url]$1[/url]', $message);
}

?>