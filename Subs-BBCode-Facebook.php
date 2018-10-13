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
			'width' => array('value' => ' width="$1"', 'match' => '(\d+)'),
		),
		'content' => '{width}',
		'validate' => 'BBCode_Facebook_Validate',
		'disabled_content' => '$1',
	);

	// Format: [facebook]{Facebook URL}[/Facebook]
	$bbc[] = array(
		'tag' => 'facebook',
		'type' => 'unparsed_content',
		'content' => '500',
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
	static $already_included = false;

	if (empty($data))
		return;
	$data = strtr($data, array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	$tag['content'] = (empty($already_included) ? '<script src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.2" async></script>' : '') . 
		'<div class="fb-post" data-href="$1" data-width="' . $tag['content'] . '"></div>';
	$already_included = true;
}

?>