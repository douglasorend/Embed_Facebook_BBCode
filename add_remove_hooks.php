<?php
/**********************************************************************************
* add_remove_hooks.php                                                            *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
if (SMF == 'SSI')
	db_extend('packages');
	
// Define the hooks
$hook_functions = array(
	'integrate_pre_include' => '$sourcedir/Subs-BBCode-Facebook.php',
	'integrate_load_theme' => 'BBCode_Facebook_Theme',
	'integrate_bbc_codes' => 'BBCode_Facebook',
	'integrate_bbc_buttons' => 'BBCode_Facebook_Button',
	'integrate_general_mod_settings' => 'BBCode_Facebook_Settings',
// SMF 2.1+ Hooks below this line:
	'integrate_load_profile_fields' => 'BBCode_Facebook_Profile',
);

// Adding or removing them?
if (!empty($context['uninstalling']))
	$call = 'remove_integration_function';
else
	$call = 'add_integration_function';

// Do the deed
foreach ($hook_functions as $hook => $function)
	$call($hook, $function);

// Let's attempt to insert the correct language for the default Facebook language:
global $modSettings;
if (!function_exists('BBCode_Facebook_Settings'))
	require_once(dirname(__FILE__) . '/Subs-BBCode-Facebook.php');
$dummy_vars = array();
BBCode_Facebook_Settings($dummy_vars);
$temp = $modSettings['fb_default_lang'];
unset($modSettings['fb_default_lang']);
updateSettings(array('fb_default_lang' => $temp));

if (SMF == 'SSI')
   echo 'Congratulations! You have successfully installed this mod!';

?>