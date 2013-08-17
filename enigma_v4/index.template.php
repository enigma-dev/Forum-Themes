<?php
// Version: 1.1; index

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.S:
	It should probably contain the copyright statement and some other things.
	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Site-wide functions
include_once('/var/www/html/enigma-dev.org/site/functions.php');

// Initialize the template... mainly little settings.
function template_init()
{

	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '1.1';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
}

require_once("/var/www/html/enigma-dev.org/site/site_template.php");

// The main sub template above the content.
function template_main_above()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	$headextra = '
	<meta name="robots" content="noindex" />
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />';
	
	$headextra .= '<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/script.js?rc2"></script>
	<script type="text/javascript" src="'. $settings['theme_url']. '/scripts/theme.js?rc2"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "'. $settings['theme_url']. '";
		var smf_default_theme_url = "'. $settings['default_theme_url']. '";
		var smf_images_url = "'. $settings['images_url']. '";
		var smf_scripturl = "'. $scripturl. '";
		var smf_iso_case_folding = '. ($context['server']['iso_case_folding'] ? 'true' : 'false'). ';
		var smf_charset = "'. $context['character_set']. '";'. ($context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : ''). '
		var ajax_notification_text = "'. $txt['ajax_in_progress']. '";
		var ajax_notification_cancel_text = "'. $txt['modify_cancel']. '";
	// ]]></script>';

	// The ?fin11 part of this link is just here to make sure browsers don't cache it wrongly.
	$headextra .= '

	<link rel="stylesheet" type="text/css" href="/site/style4.scss.css">
        <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />

	<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/css/admin.css" />
	<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/style.css?5635251" />
	
	<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/enigma.css?1" />
	
	<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/print.css?fin11" media="print" />';
    
	/* Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are big...)
		Thus, in Internet Explorer 4, 5, and Opera 6 this will show fonts one size smaller than usual.
		Note that this is affected by whether IE 6 is in standards compliance mode.. if not, it will also be big.
		Standards compliance mode happens when you use xhtml... */
	if ($context['browser']['needs_size_fix'])
		$headextra .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/fonts-compat.css" />';

	// Show all the relative links, such as help, search, contents, and the like.
	$headextra .= '
	<link rel="help" href="' . $scripturl . '?action=help" target="_blank" />
	<link rel="search" href="' . $scripturl . '?action=search" />
	<link rel="contents" href="' . $scripturl . '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']))
		$headextra .= '
	<link rel="alternate" type="application/rss+xml" title="' . $context['forum_name'] . ' - RSS" href="' . $scripturl . '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		$headextra .= '
	<link rel="prev" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=prev" />
	<link rel="next" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		$headextra .= '
	<link rel="index" href="' . $scripturl . '?board=' . $context['current_board'] . '.0" />';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

	// Output any remaining HTML headers. (from mods, maybe?)
	$headextra .= $context['html_headers'] . '

	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var current_header = ' . (empty($options['collapse_header']) ? 'false' : 'true') . ';

		function shrinkHeader(mode)
		{';

	// Guests don't have theme options!!
	if ($context['user']['is_guest'])
		$headextra .= '
			document.cookie = "upshrink=" + (mode ? 1 : 0);';
	else
		$headextra .= '
			smf_setThemeOption("collapse_header", mode ? 1 : 0, null, "' . $context['session_id'] . '");';

	$headextra .= '
			document.getElementById("upshrink").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

			document.getElementById("upshrinkHeader").style.display = mode ? "none" : "";
			document.getElementById("upshrinkHeader2").style.display = mode ? "none" : "";

			current_header = mode;
		}
	// ]]></script>';

	// the routine for the info center upshrink
	$headextra .= '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			var current_header_ic = ' . (empty($options['collapse_header_ic']) ? 'false' : 'true') . ';

			function shrinkHeaderIC(mode)
			{';

	if ($context['user']['is_guest'])
		$headextra .= '
				document.cookie = "upshrinkIC=" + (mode ? 1 : 0);';
	else
		$headextra .= '
				smf_setThemeOption("collapse_header_ic" . (mode ? 1 : 0) . null . "' . $context['session_id'] . '");';

	$headextra .= '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>';

	// Body classes
	if ($context['current_board'] == '22.0')
	{
		$bclasses .= 'nonsense';
	}
	
	$loc = $_SERVER['REQUEST_URI']; 
	$banner_href = "/";
	echo "<!-- Location: $loc -->";
	if($loc == "/forums/index.php" || $loc == "/forums/" || $loc == "/forums") { $banner_href = "/"; }
	else { $banner_href = "/forums/"; }
	
	ENIGMASite::output_header($context['page_title_html_safe'], $headextra, $bclasses, false, $banner_href);

		// Echo a2h's navigation links submenu navbar thing
		if($context['user']['is_logged'])
		{
			echo '
			<div id="submenu"><div id="menuborder">
					<ul>
						<li><a href="', $scripturl, '">Home</a></li>';
						echo '
						'.($context['allow_admin'] ? "<li><a href='$scripturl?action=admin'>Admin</a></li>" : '').'
						'.($context['allow_moderation_center'] ? "<li><a href='$scripturl?action=moderate'>Moderation</a></li>" : '').'
						<li><a href="', $scripturl, '?action=search">Search</a></li>
						<li><a href="', $scripturl, '?action=unread">View New Posts</a></li>
					</ul>
				</div></div>
			';
		}
		
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
								<b>', $txt['maintain_mode_on'], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
								', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, 
'?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', 
$txt['approve_members_waiting'], '<br />';

	// Show the menu here, according to the menu sub template.
	//template_menu();

	// The main content should go here.
}

function template_main_below()
{
  ENIGMASite::output_footer();
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '				<div class="nav" style="font-size: smaller; margin-bottom: 2ex; margin-top: 2ex;">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo '<b>', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], '</b>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;&gt;&nbsp;';
	}

	echo '</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = 'home';
	if (in_array($context['current_action'], array('admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Are we using right-to-left orientation?
	if ($context['right_to_left'])
	{
		$first = 'last';
		$last = 'first';
	}
	else
	{
		$first = 'first';
		$last = 'last';
	}

	// Show the start of the tab section.
	echo '
			<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
				<tr>
					<td class="maintab_' , $first , '">&nbsp;</td>';

	// Show the [home] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt['home'] , '</a>
				</td>' , $current_action == 'home' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Show the [help] button.
	echo ($current_action == 'help' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'help' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=help">' , $txt['help'] , '</a>
				</td>' , $current_action == 'help' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// How about the [search] button?
	if ($context['allow_search'])
		echo ($current_action == 'search' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'search' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=search">' , $txt['search'] , '</a>
				</td>' , $current_action == 'search' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo ($current_action == 'admin' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'admin' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=admin">' , $txt['admin'] , '</a>
				</td>' , $current_action == 'admin' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo ($current_action == 'profile' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'profile' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=profile">' , $txt['profile'] , '</a>
				</td>' , $current_action == 'profile' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo ($current_action == 'pm' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'pm' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</td>' , $current_action == 'pm' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo ($current_action == 'calendar' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'calendar' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar'] , '</a>
				</td>' , $current_action == 'calendar' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo ($current_action == 'mlist' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'mlist' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt['members_title'] , '</a>
				</td>' , $current_action == 'mlist' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'login' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'login' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=login">' , $txt['login'] , '</a>
				</td>' , $current_action == 'login' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'register' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'register' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=register">' , $txt['register'] , '</a>
				</td>' , $current_action == 'register' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo ($current_action == 'logout' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action == 'logout' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt['logout'] , '</a>
				</td>' , $current_action == 'logout' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// The end of tab section.
	echo '
				<td class="maintab_' , $last , '">&nbsp;</td>
			</tr>
		</table>';

}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . $txt[$value['text']] . '</a>';

		$button_strip[$key] = $buttons[$key];
	}

	if (empty($button_strip))
		return '<td>&nbsp;</td>';

	echo '
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}

?>
