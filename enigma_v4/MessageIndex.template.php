<?php
// Version: 1.1; MessageIndex

function template_main()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $sourcedir;

if (isset($context['topblock']) && $context['topblock'] != '')
echo ' <div class="board_rules tborder windowbg2">', $context['topblock'], '</div>';

	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
	<div class="tborder" style="margin-bottom: 3ex; ', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? ' width: 100%;' : '', '">
		<table border="0" width="100%" cellspacing="1" cellpadding="5" class="bordercolor">
			<tr>
				<td colspan="4" class="catbg">', $txt['parent_boards'], '</td>
			</tr>';

		foreach ($context['boards'] as $board)
		{
			echo '
			<tr>
				<td ' , !empty($board['children']) ? 'rowspan="2"' : '' , ' class="windowbg" width="6%" align="center" valign="top"><a href="', $scripturl, '?action=unread;board=', $board['id'], '.0">';

			// If the board is new, show a strong indicator.
			if ($board['new'])
				echo '<img src="', $settings['images_url'], '/on.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
			// This board doesn't have new posts, but its children do.
			elseif ($board['children_new'])
				echo '<img src="', $settings['images_url'], '/on2.gif" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
			// No new posts at all! The agony!!
			else
				echo '<img src="', $settings['images_url'], '/off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

			echo '</a>
				</td>
				<td class="windowbg2">
					<b><a href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></b><br />
					', $board['description'];

			// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
			if (!empty($board['moderators']))
				echo '
					<div style="padding-top: 1px;"><small><i>', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</i></small></div>';


			// Show some basic information about the number of posts, etc.
			echo '
				</td>
				<td class="windowbg" valign="middle" align="center" style="width: 12ex;"><small>
					', $board['posts'], ' ', $txt['posts'], ' <br />
					', $board['topics'],' ', $txt['board_topics'], '</small>
				</td>
				<td class="windowbg2" valign="middle" width="22%"><small>';

			/* The board's and children's 'last_post's have:
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
				link, href, subject, start (where they should go for the first unread post.),
				and member. (which has id, name, link, href, username in it.) */
			if (!empty($board['last_post']['id']))
				echo '
					<b>', $txt['last_post'], '</b> ', $txt['by'], ' ', $board['last_post']['member']['link'] , '<br />
					', $txt['in'], ' ', $board['last_post']['link'], '<br />
					', $txt['on'], ' ', $board['last_post']['time'];

				echo '</small>
				</td>
			</tr>';

			// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
			if (!empty($board['children']))
			{
				// Sort the links into an array with new boards bold so it can be imploded.
				$children = array();
				/* Each child in each board's children has:
					id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
				foreach ($board['children'] as $child)
				{
					$child['link'] = '<a href="' . $child['href'] . '" title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . $child['topics'] . ', ' . $txt['posts'] . ': ' . $child['posts'] . ')">' . $child['name'] . '</a>';
					$children[] = $child['new'] ? '<b>' . $child['link'] . '</b>' : $child['link'];
				}

				echo '
			<tr>
				<td colspan="3" class="windowbg', !empty($settings['seperate_sticky_lock']) ? '3' : '', '">
					<small><b>', $txt['parent_boards'], '</b>: ', implode(', ', $children), '</small>
				</td>
			</tr>';
			}
		}

		echo '
		</table>
	</div>';
	}


	if (!empty($options['show_board_desc']) && $context['description'] != '')
	{
		echo '
		<table width="100%" cellpadding="6" cellspacing="1" border="0" class="tborder" style="padding: 0; margin-bottom: 2ex;">
			<tr>
				<td class="titlebg2" width="100%" height="24" style="border-top: 0;">
					<small>', $context['description'], '</small>
				</td>
			</tr>
		</table>';
	}

	// Create the button set...
	$normal_buttons = array(
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;sesc=' . $context['session_id']),
		'notify' => array('test' => 'can_mark_notify', 'text' => 'notify', 'image' => 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';sesc=' . $context['session_id']),
		'new_topic' => array('test' => 'can_post_new', 'text' => 'new_topic', 'image' => 'new_topic.png', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0'),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'new_poll', 'image' => 'new_poll.png', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	if (!$context['no_topic_listing'])
	{
	  //I personally think this'd look better somewhere else, but meh. -Josh
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="middletext">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#bot"><b>' . $txt['go_down'] . '</b></a>' : '', '</td>
				<td align="right" style="padding-right: 1ex;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							', theme_show_buttons(), '
						</tr>
					</table>
				</td>
			</tr>
		</table>';

		// If Quick Moderation is enabled start the form.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;">';
	  
	  //Somebody please fucking center this
		echo '
			<div class="tborder" ', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? 'style="width: 100%;"' : '', '>
				<table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor">
					<tr class="catbg3" height="24">
					  <td colspan="6" style="height:24px; padding:0px 2px 6px 6px; vertical-align:center;">
					    <div class="smalltext" style="padding:0; height:12px; vertical-align:center;">
					      ',theme_linktree(),'
					    </div>
					  </td>
					</tr>
					<tr>';

		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
						<td width="5%" class="descbg"></td>

						<td class="descbg"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="descbg" width="11%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['started_by'], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="descbg" width="'.(($context['sort_by'] == 'replies')?'9':'4').'%" align="center"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="descbg" width="'.(($context['sort_by'] == 'views')?'9':'4').'%" align="center"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['views'], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="descbg" width="22%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>';

			// Show a "select all" box for quick moderation?
			if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1)
				echo '
						<td class="catbg3" width="24" valign="middle" align="center">
							<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="check" />
						</td>';
			// If it's on in "image" mode, don't show anything but the column.
			elseif (!empty($options['display_quick_mod']))
				echo '
						<td class="catbg3" width="4%" valign="middle" align="center"></td>';
		}
		// No topics.... just say, "sorry bub".
		else
			echo '
						<td class="catbg3" width="100%" colspan="7"><b>', $txt['msg_alert_none'], '</b></td>';

		echo '
					</tr>';

		foreach ($context['topics'] as $topic)
		{
			// Do we want to seperate the sticky and lock status out?
			if (!empty($settings['seperate_sticky_lock']) && strpos($topic['class'], 'sticky') !== false)
				$topic['class'] = substr($topic['class'], 0, strrpos($topic['class'], '_sticky'));
			if (!empty($settings['seperate_sticky_lock']) && strpos($topic['class'], 'locked') !== false)
				$topic['class'] = substr($topic['class'], 0, strrpos($topic['class'], '_locked'));
	
                        // Okay, making the oncomer think everything's not new just because they're not logged in is dumb. -Josh
			echo '
					<tr>
						<td class="windowbg2" valign="middle" align="center" width="5%">
							<img src="', $settings['images_url'], '/topic/', $topic['class'], (($topic['is_locked'])?'_locked':(($topic['new'] || !$context['user']['is_logged']) ? "_new" : "_nonew")), '.gif" alt="" />
						</td>
						<!--<td class="windowbg2" valign="middle" align="center" width="4%">
							<img src="', $topic['first_post']['icon_url'], '" alt="" />
						</td>-->
						<td class="windowbg' , !empty($settings['seperate_sticky_lock']) && $topic['is_sticky'] ? '3' : '' , '" valign="middle" ', (!empty($topic['quick_mod']['remove']) ? 'id="topic_' . $topic['first_post']['id'] . '" onmouseout="mouse_on_div = 0;" onmouseover="mouse_on_div = 1;" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\', \'' . $context['session_id'] . '\');"' : ''), '>';

			if (!empty($settings['seperate_sticky_lock']))
				echo '
							' /*, $topic['is_locked'] ? '<img src="' . $settings['images_url'] . '/icons/quick_lock.gif" align="right" alt="" id="lockicon' . $topic['first_post']['id'] . '" style="margin: 0;" />' : '' , '
							' */, $topic['is_sticky'] ? '<img src="' . $settings['images_url'] . '/icons/show_sticky.gif" align="right" alt="" id="stickyicon' . $topic['first_post']['id'] . '" style="margin: 0;" />' : '';

			echo '
							', $topic['is_sticky'] ? '<b>' : '' , '<span id="msg_' . $topic['first_post']['id'] . '">', $topic['first_post']['link'], '</span>', $topic['is_sticky'] ? '</b>' : '';

			//This is stupid, not to mention ...fuck ugly. -Josh
			// Is this topic new? (assuming they are logged in!)
			//if ($topic['new'] && $context['user']['is_logged'])
			//		echo '
			//				<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['images_url'], '/', $context['user']['language'], '/new.gif" alt="', $txt['new'], '" /></a>';

			echo '
							<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
						</td>
						<td class="windowbg2" valign="middle" width="14%">
							', $topic['first_post']['member']['link'], '
						</td>
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" width="4%" align="center">
							', $topic['replies'], '
						</td>
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" width="4%" align="center">
							', $topic['views'], '
						</td>
						<td class="windowbg2" valign="middle" width="22%">
							'/*<a href="', $topic['last_post']['href'], '"><img src="', $settings['images_url'], '/icons/last_post.gif" alt="', $txt['last_post'], '" title="', $txt['last_post'], '" style="float: right;" /></a>*/
							.'<span class="smalltext">
								<a href="', $topic['last_post']['href'], '">', $topic['last_post']['time'], '</a><br />
								', $txt['by'], ' ', $topic['last_post']['member']['link'], '
							</span>
						</td>';

			// Show the quick moderation options?
			if (!empty($options['display_quick_mod']))
			{
				echo '
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" align="center" width="4%">';
				if ($options['display_quick_mod'] == 1)
					echo '
								<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="check" />';
				else
				{
					// Check permissions on each and show only the ones they are allowed to use.
					if ($topic['quick_mod']['remove'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=remove;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_remove.gif" width="16" alt="', $txt['remove_topic'], '" title="', $txt['remove_topic'], '" /></a>';

					if ($topic['quick_mod']['lock'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=lock;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_lock.gif" width="16" alt="', $txt['set_lock'], '" title="', $txt['set_lock'], '" /></a>';

					if ($topic['quick_mod']['lock'] || $topic['quick_mod']['remove'])
						echo '<br />';

					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" width="16" alt="', $txt['set_sticky'], '" title="', $txt['set_sticky'], '" /></a>';
						
					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" width="16" alt="', $txt['move_topic'], '" title="', $txt['move_topic'], '" /></a>';
				}
				echo '</td>';
			}
			echo '
					</tr>';
		}

		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{
			echo '
					<tr class="catbg">
						<td colspan="8" align="right">
					<select name="qaction"', $context['can_move'] ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
								<option value="">--------</option>
								', $context['can_remove'] ? '<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', '
								', $context['can_lock'] ? '<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', '
								', $context['can_sticky'] ? '<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '', '
								', $context['can_move'] ? '<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', '
								', $context['can_merge'] ? '<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', '
								<option value="markread">', $txt['quick_mod_markread'], '</option>
							</select>';

			if ($context['can_move'])
			{
					/*echo '
							<select id="moveItTo" name="move_to" disabled="disabled">';

					foreach ($context['jump_to'] as $category)
							foreach ($category['boards'] as $board)
							{
								if (!$board['is_current'])
									echo '
												<option value="', $board['id'], '"', !empty($board['selected']) ? ' selected="selected"' : '', '>', str_repeat('-', $board['child_level'] + 1), ' ', $board['name'], '</option>';
							}
					echo '
							</select>';*/
				echo 'this is ghey <p class="align_right" id="message_index_jump_to">&nbsp;</p>';
			}
			echo '
							<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" />
						</td>
					</tr>';
		}

		echo '
				</table>
			</div>
			<a name="bot"></a>';

			// Finish off the form - again.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
				echo '
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
	</form>';

//Joshedit: Link tree and page numbers
		echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
	    <td class="middletext">', theme_linktree(), '</td>
			<td align="right" style="padding-right: 1ex;">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#top"><b>' . $txt['go_up'] . '</b></a>' : '', '</td>
		</tr>
	</table>';
	}
	

	echo '
	<div class="tborder">
	<div class="windowbg2">

		<table class="ipbtable" cellspacing="0">

			<tr>';

	if (!$context['no_topic_listing'])
			echo '
				<td style="padding-top: 2ex;" class="smalltext">
					<img src="' . $settings['images_url'] . '/topic/normal_post_new.gif" alt="" align="middle" /> ' . $txt['normal_topic'] . '<br />
					<img src="' . $settings['images_url'] . '/topic/hot_post_new.gif" alt="" align="middle" /> ' . sprintf($txt['hot_topics'], $modSettings['hotTopicPosts']) . '<br />
					<img src="' . $settings['images_url'] . '/topic/veryhot_post_new.gif" alt="" align="middle" /> ' . sprintf($txt['very_hot_topics'], $modSettings['hotTopicVeryPosts']) . '
				</td>
				<td valign="top" style="padding-top: 2ex;" class="smalltext">', !empty($modSettings['enableParticipation']) ? '
					<img src="' . $settings['images_url'] . '/topic/my_normal_post_new.gif" alt="" align="middle" /> ' . $txt['participation_caption'] . '<br />' : '', ($modSettings['pollMode'] == '1' ? '
					<img src="' . $settings['images_url'] . '/topic/normal_poll_new.gif" alt="" align="middle" /> ' . $txt['poll'] : '') . '
				</td>
				<td valign="top" style="padding-top: 2ex;" class="smalltext">' . ($modSettings['enableStickyTopics'] == '1' ? '
					<img src="' . $settings['images_url'] . '/icons/quick_sticky.gif" alt="" align="middle" /> ' . $txt['sticky_topic'] . '<br />' : '') . '
					<img src="' . $settings['images_url'] . '/icons/quick_lock.gif" alt="" align="middle" /> ' . $txt['locked_topic'] . '
				</td>';
				

	echo '
				<td align="', !$context['right_to_left'] ? 'right' : 'left', '" valign="middle">'
				. (($context['can_post_new']) ?
		      '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0">
		       <img src="' . $settings['images_url'] . '/topic/new_topic_light.gif" alt="New Topic" align="middle" /></a>&nbsp;'
		     :'<img src="' . $settings['images_url'] . '/topic/new_topic_lighx.gif" alt="New Topic" align="middle" />&nbsp;')
		    . (($context['can_post_poll']) ?
		      '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll">
				   <img src="' . $settings['images_url'] . '/topic/new_poll_light.gif" alt="New Poll" align="middle" /></a> <br /><br />'
				 :'<img src="' . $settings['images_url'] . '/topic/new_poll_lighx.gif" alt="New Poll" align="middle" /><br /><br />')
				
				.'<form action="', $scripturl, '" method="get" accept-charset="', $context['character_set'], '" name="jumptoForm">
						<span class="smalltext"><label for="jumpto">' . $txt['jump_to'] . '</label>:</span>
					<select name="jumpto" id="jumpto" onchange="if (this.selectedIndex > 0 &amp;&amp; this.options[this.selectedIndex].value) window.location.href = smf_scripturl + this.options[this.selectedIndex].value.substr(smf_scripturl.indexOf(\'?\') == -1 || this.options[this.selectedIndex].value.substr(0, 1) != \'?\' ? 0 : 1);">
								<option value="">' . $txt['select_destination'] . ':</option>';

	// Show each category - they all have an id, name, and the boards in them.
	foreach ($context['jump_to'] as $category)
	{
		// Show the category name with a link to the category. (index.php#id)
		echo '
								<option value="" disabled="disabled">-----------------------------</option>
								<option value="#', $category['id'], '">', $category['name'], '</option>
								<option value="" disabled="disabled">-----------------------------</option>';

		/* Now go through each board - they all have:
				id, name, child_level (how many parents they have, basically...), and is_current. (is this the current board?) */
		foreach ($category['boards'] as $board)
		{
			// Show some more =='s if this is a child, so as to make it look nice.
			echo '
								<option value="?board=', $board['id'], '.0"', $board['is_current'] ? ' selected="selected"' : '', '> ', str_repeat('==', $board['child_level']), '=> ', $board['name'], '</option>';
		}
	}

	echo '
						</select>&nbsp;
					<input type="button" value="', $txt['go'], '" onclick="if (this.form.jumpto.options[this.form.jumpto.selectedIndex].value) window.location.href = \'', $scripturl, '\' + this.form.jumpto.options[this.form.jumpto.selectedIndex].value;" />
					</form>
				</td>
			</tr>
		</table>
	</div></div>
	<br />';

// nonsense
if ($context['current_board'] == '22.0')
{
$nsfiles = '/forums/Themes/enigma_v4/nonsense/';
echo '
<iframe width="425" height="25" scrolling="no" style="border:0px !important;" src="' . $nsfiles . '/spin.htm"></iframe>
<style type="text/css">
#header_wrapper { background: #000 url("' . $nsfiles . '/4_colorful_bears.gif") !important; }
#header_wrapper a { display: block; background: url("' . $nsfiles . '/logo.gif"); width: 317px; height: 70px; }
#header_wrapper img { display: none; }
</style>
';
}
	
	
	
	//Show who's reading
	if (!empty($settings['display_who_viewing']))
	{
		echo '
  <div class="tborder" style="padding:1px;">
    <table border="0" width="100%" class="windowbg2">
      <tr style="background: #DDE1E6;">
        <td>
          <div style="width:100%; padding: 2px;">Currently, ',count($context['view_members']), ' ', ((count($context['view_members']) == 1) ? $txt['who_member'] : $txt['members']), ' and ', $txt['who_and'], $context['view_num_guests'], ' ', (($context['view_num_guests']==1) ? $txt['guest']:$txt['guests']),' are viewing this board</div>
        </td>
      </tr>
      <tr>
        <td colspan="3" class="smalltext">
          <div style="width:100%">';
            // Show just numbers...?
            if ($settings['display_who_viewing'] !== 1)
              echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
            echo '
          </div>
        </td>
      </tr>
    </table>
  </div>';
	}
	
	// Javascript for Jump to:
	echo '
<script type="text/javascript"><!-- // --><![CDATA[
	if (typeof(window.XMLHttpRequest) != "undefined")
		aJumpTo[aJumpTo.length] = new JumpTo({
			sContainerId: "message_index_jump_to",
			sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
			iCurBoardId: ', $context['current_board'], ',
			iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
			sCurBoardName: "', $context['jump_to']['board_name'], '",
			sBoardChildLevelIndicator: "==",
			sBoardPrefix: "=> ",
			sCatSeparator: "-----------------------------",
			sCatPrefix: "",
			sGoButtonLabel: "', $txt['quick_mod_go'], '"
		});
// ]]></script>';

	// Javascript for inline editing.
	echo '
<script language="JavaScript" type="text/javascript" src="' . $settings['default_theme_url'] . '/xml_board.js"></script>
<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[

	// Hide certain bits during topic edit.
	hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

	// Use it to detect when we\'ve stopped editing.
	document.onclick = modify_topic_click;

	var mouse_on_div;
	function modify_topic_click()
	{
		if (in_edit_mode == 1 && mouse_on_div == 0)
			modify_topic_save("', $context['session_id'], '");
	}

	function modify_topic_keypress(oEvent)
	{
		if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
		{
			modify_topic_save("', $context['session_id'], '");
			if (typeof(oEvent.preventDefault) == "undefined")
				oEvent.returnValue = false;
			else
				oEvent.preventDefault();
		}
	}

	// For templating, shown when an inline edit is made.
	function modify_topic_show_edit(subject)
	{
		// Just template the subject.
		setInnerHTML(cur_subject_div, \'<input type="text" name="subject" value="\' + subject + \'" size="60" style="width: 99%;"  maxlength="80" onkeypress="modify_topic_keypress(event)" /><input type="hidden" name="topic" value="\' + cur_topic_id + \'" /><input type="hidden" name="msg" value="\' + cur_msg_id.substr(4) + \'" />\');
	}

	// And the reverse for hiding it.
	function modify_topic_hide_edit(subject)
	{
		// Re-template the subject!
		setInnerHTML(cur_subject_div, \'<a href="', $scripturl, '?topic=\' + cur_topic_id + \'.0">\' + subject + \'</a>\');
	}

// ]]></script>';
}

function theme_show_buttons()
{
	global $context, $settings, $options, $txt, $scripturl;

	$buttonArray = array();

	// Are they allowed to post new topics?
	if ($context['can_post_new'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0"><img src="'. $settings['images_url']. '/english/new_topic.png" alt="New Topic" /></a>';

	// How about new polls, can the user post those?
	if ($context['can_post_poll'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll"><img src="'. $settings['images_url']. '/english/new_poll.png" alt="New Poll" /></a>';

	return implode(' &nbsp; ', $buttonArray);
}

?>
