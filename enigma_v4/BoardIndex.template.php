<?php
// Version: 1.1; BoardIndex

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

/*echo '<table cellspacing="0" class="newslink">
	<tr>
		<td><b>Welcome back; your last visit was: <span>'.($context['user']['is_logged'] ? $context['member']['last_login'] : 'Now').'</span></b>
		<br /><b>', $context['forum_name'], ' latest news: </b> <i><span id="includeone"></span></i>
			<script type="text/javascript"><!-- // --><![CDATA[
				clientSideInclude(\'includeone\', \'latestnews.php\');
			// ]]></script>
		</td>

		<td align="right" valign="middle">
			';
			if (!$context['user']['is_logged']) { echo '
								<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
									<input type="text" name="user" size="20" /> <input type="password" name="passwrd" size="20" />
									<span style="display:none;"><select name="cookielength">
										<option value="60">', $txt['one_hour'], '</option>
										<option value="1440" selected="selected">', $txt['one_day'], '</option>
										<option value="10080">', $txt['one_week'], '</option>
										<option value="43200">', $txt['one_month'], '</option>
										<option value="-1">', $txt['forever'], '</option>
									</select></span>
									<input class="button" type="image" src="', $settings['images_url'], '/login-button.gif" /><br />
									<input type="hidden" name="hash_passwrd" value="" />
								</form>'; }
		
		echo '</td>
	</tr>
</table><br />';*/

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	$first = true;
	foreach ($context['categories'] as $category)
	{
		echo '
	<div class="tborder" style="margin-top: ' , $first ? '0;' : '1ex;' , '' , $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? 'width: 100%;' : '', '">
		<div class="catbg', $category['new'] ? '2' : '', '" style="padding: 5px 5px 5px 10px;">';
		$first = false;



		echo '
				<div style="float:left;">', $category['link'].'</div>';
						// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
				<div style="float:right;"><a href="', $category['collapse_href'], '">', $category['collapse_image'], '</a></div>';
		echo '<div style="clear:both;"></div></div>';

		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			echo '
		<table border="0" width="100%" cellspacing="1" cellpadding="5" class="bordercolor" style="margin-top: 1px;">';

			/* Each board in each category's boards has:
			new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
			children (see below.), link_children (easier to use.), children_new (are they new?),
			topics (# of), posts (# of), link, href, and last_post. (see below.) */
			foreach ($category['boards'] as $board)
			{
				if ($board['name']!=="ENIGMA Progress") {
				echo '
			<tr>
				<td ' , !empty($board['children']) ? 'rowspan="2"' : '' , ' class="windowbg" width="20" align="center" valign="top">
					<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

				// If the board or children is new, show an indicator.
				if ($board['new'] || $board['children_new'] || !$context['user']['is_logged'])
					echo '
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'on', ($board['new'] || !$context['user']['is_logged']) ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
				// Is it a redirection board?
				elseif ($board['is_redirect'])
					echo '
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'redirect.png" alt="*" title="*" />';
				// No new posts at all! The agony!!
				else
					echo '
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

				echo '
					</a>
				</td>
				<td class="windowbg2">
					<b><a href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></b><br />
						<span class="boarddesc">', $board['description'], '</span>';

				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
				if (!empty($board['moderators']))
					echo '
					<div style="padding-top: 1px;" class="smalltext"><i>', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</i></div>';

				// Show some basic information about the number of posts, etc.
					echo '
				</td>
				<td class="stats windowbg" valign="middle" align="center" style="width: 12ex;">
					<span class="smalltext">
						', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
						', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
					</span>
				</td>
				<td class="lastpost windowbg2" valign="middle" width="22%">
					<span class="smalltext">';

				/* The board's and children's 'last_post's have:
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
				link, href, subject, start (where they should go for the first unread post.),
				and member. (which has id, name, link, href, username in it.) */
				if (!empty($board['last_post']['id']))
					echo '
						<strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , '<br />
						', $txt['in'], ' ', $board['last_post']['link'], '<br />
						', $txt['on'], ' ', $board['last_post']['time'],'
						';
				echo '
					</span>
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
					<span class="smalltext"><b>', $txt['parent_boards'], '</b>: ', implode(', ', $children), '</span>
				</td>
			</tr>';
				}
				}
			}
			echo '
		</table>';
		}
		echo '
	</div>';
	}

	if ($context['user']['is_logged'])
	{
		echo '
	<table border="0" width="100%" cellspacing="0" cellpadding="5">
		<tr>

			<td align="', !$context['right_to_left'] ? 'right' : 'left', '">';

		// Mark read button.
		$mark_read_button = array('markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;sesc=' . $context['session_id']));

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
				echo '
				<table cellpadding="0" cellspacing="0" border="0" style="position: relative; top: -5px;">
					<tr>
							 ', template_button_strip($mark_read_button, 'top'), '
					</tr>
				</table>';
		echo '
			</td>
		</tr>
	</table>';
	}
	
	template_info_center();
}

function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	
	// Here's where the "Info Center" starts...
	echo '<br />
	<div class="tborder" ', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? 'style="width: 100%;"' : '', '>
		<div class="catbg" style="padding: 6px; vertical-align: middle;">
			<a href="#" onclick="shrinkHeaderIC(!current_header_ic); return false;"><img id="upshrink_ic" src="', $settings['images_url'], '/', empty($options['collapse_header_ic']) ? 'collapse.gif' : 'expand.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 2ex;" align="right" /></a>
			Board Statistics
		</div>
		<div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>
			<table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor formtable">';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['recent_posts'], '</td>
				</tr>
				<tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						<a href="', $scripturl, '?action=recent"><img src="', $settings['images_url'], '/post/xx.gif" alt="', $txt['recent_posts'], '" /></a>
					</td>
					<td class="windowbg2">';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
						<b><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></b>
						<div class="smalltext">
								', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
						</div>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
						<table cellpadding="0" cellspacing="0" width="100%" border="0">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
							<tr>
								<td class="middletext" valign="top"><b>', $post['link'], '</b> ', $txt['by'], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</td>
								<td class="middletext" align="right" valign="top" nowrap="nowrap">', $post['time'], '</td>
							</tr>';
			echo '
						</table>';
		}
		echo '
					</td>
				</tr>';
	}
	
	
	
	// Show statistical style information... (smf2)
	if ($settings['show_stats_index'])
	{
		echo '
				<tr>
					<td class="formsubtitle" colspan="2">
						', $txt['forum_stats'], '
					</td>
				</tr>
				<tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						<a href="', $scripturl, '?action=stats"><img class="icon" src="', $settings['images_url'], '/icons/info.gif" alt="', $txt['forum_stats'], '" /></a>
					</td>
					<td class="windowbg2" width="100%">
						', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' <a href="', $scripturl, '?action=mlist">', $context['common_stats']['total_members'], ' ', $txt['members'], '</a>. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '<br />
						', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' )<br />' : ''), '
						<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? '<br />
						<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
					</td>
				</tr>';
	}


$wut=$context['num_guests']+$context['num_users_online'].' '.sprintf($txt['users_active'], $modSettings['lastActive']);
	
	// "Users online" - in order of activity.
	echo '
				<tr>
					<td class="formsubtitle" colspan="2">', $wut, '</td>
				</tr>
				<tr>
					<td rowspan="2" class="windowbg" width="20" valign="middle" align="center">
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<img src="', $settings['images_url'], '/icons/online.gif" alt="', $txt['online_users'], '" />', $context['show_who'] ? '</a>' : '', '
					</td>
					<td class="windowbg2" width="100%">';

	echo '
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', $context['num_guests'], ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . $context['num_users_online'], ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	if (!empty($context['num_users_hidden']) || ($context['show_buddies'] && !empty($context['show_buddies'])))
	{
		echo ' (';

		// Show the number of buddies online?
		if ($context['show_buddies'])
			echo $context['num_buddies'], ' ', $context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies'];

		// How about hidden users?
		if (!empty($context['num_users_hidden']))
			echo $context['show_buddies'] ? ', ' : '', $context['num_users_hidden'] . ' ' . $txt['hidden'];

		echo ')';
	}

	echo $context['show_who'] ? '</a>' : '', '
						<div class="smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
		echo '
							<div class="thin">', implode(', ', $context['list_users_online']), '</div>';

	echo '
							
							', $context['show_stats'] && !$settings['show_stats_index'] ? '<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
												<span class="middletext">
							', $txt['most_online_today'], ': <b>', $modSettings['mostOnlineToday'], '</b>.
							', $txt['most_online_ever'], ': ', $modSettings['mostOnline'], ' (' , timeformat($modSettings['mostDate']), ')
						</span>
						</div>
					</td>
				</tr>
				<tr>
					<td style="background-color: #EFF1F3; display:none;">

					</td>
				</tr>';

	// If they are logged in, but statistical information is off... show a personal message bar.
	if ($context['user']['is_logged'] && !$settings['show_stats_index'])
	{
		 echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['personal_message'], '</td>
				</tr><tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img src="', $settings['images_url'], '/message_sm.gif" alt="', $txt['personal_message'], '" />', $context['allow_pm'] ? '</a>' : '', '
					</td>
					<td class="windowbg2" valign="top">
						<b><a href="', $scripturl, '?action=pm">', $txt['personal_message'], '</a></b>
						<div class="smalltext">
							', $txt['you_have'], ' ', $context['user']['messages'], ' ', $context['user']['messages'] == 1 ? $txt['message_lowercase'] : $txt['msg_alert_messages'];
             if(intval($context['users']['messages'])==0) { } else {
             echo '.... ', $txt['click'], ' <a href="', $scripturl, '?action=pm">', $txt['here'], '</a> ', $txt['to_view'];
             } 
             echo '
						</div>
					</td>
				</tr>';
	}

	// Show the login bar. (it's only true if they are logged out anyway.)
	if ($context['show_login_bar'])
	{
		echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['login'], ' <a href="', $scripturl, '?action=reminder" class="smalltext">(' . $txt['forgot_your_password'] . ')</a></td>
				</tr>
				<tr>
					<td class="windowbg" width="20" align="center">
						<a href="', $scripturl, '?action=login"><img src="', $settings['images_url'], '/icons/login.gif" alt="', $txt['login'], '" /></a>
					</td>
					<td class="windowbg2" valign="middle">
						<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
							<table border="0" cellpadding="2" cellspacing="0" align="center" width="100%"><tr>
								<td valign="middle" align="left">
									<label for="user"><b>', $txt['username'], ':</b><br />
									<input type="text" name="user" id="user" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="passwrd"><b>', $txt['password'], ':</b><br />
									<input type="password" name="passwrd" id="passwrd" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookielength"><b>', $txt['mins_logged_in'], ':</b><br />
									<input type="text" name="cookielength" id="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookieneverexp"><b>', $txt['always_logged_in'], ':</b><br />
									<input type="checkbox" name="cookieneverexp" id="cookieneverexp" checked="checked" class="check" /></label>
								</td>
								<td valign="middle" align="left">
									<input type="submit" value="', $txt['login'], '" />
								</td>
							</tr></table>
						</form>
					</td>
				</tr>';
	}

	echo '
			</table>
		</div>
	</div>';
}

function template_info_center_old()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	
	// Here's where the "Info Center" starts...
	echo '<br />
	<div class="tborder" ', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? 'style="width: 100%;"' : '', '>
		<div class="catbg" style="padding: 6px; vertical-align: middle;">
			<a href="#" onclick="shrinkHeaderIC(!current_header_ic); return false;"><img id="upshrink_ic" src="', $settings['images_url'], '/', empty($options['collapse_header_ic']) ? 'collapse.gif' : 'expand.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 2ex;" align="right" /></a>
			Board Statistics
		</div>
		<div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>
			<table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor formtable">';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['recent_posts'], '</td>
				</tr>
				<tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						<a href="', $scripturl, '?action=recent"><img src="', $settings['images_url'], '/post/xx.gif" alt="', $txt['recent_posts'], '" /></a>
					</td>
					<td class="windowbg2">';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
						<b><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></b>
						<div class="smalltext">
								', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
						</div>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
						<table cellpadding="0" cellspacing="0" width="100%" border="0">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
							<tr>
								<td class="middletext" valign="top"><b>', $post['link'], '</b> ', $txt['by'], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</td>
								<td class="middletext" align="right" valign="top" nowrap="nowrap">', $post['time'], '</td>
							</tr>';
			echo '
						</table>';
		}
		echo '
					</td>
				</tr>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
				<tr>
					<td class="titlebg" colspan="2">', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '</td>
				</tr><tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						<a href="', $scripturl, '?action=calendar"><img src="', $settings['images_url'], '/icons/calendar.gif" alt="', $txt['calendar'], '" /></a>
					</td>
					<td class="windowbg2" width="100%">
						<span class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
							<span style="color: #', $modSettings['cal_holidaycolor'], ';">', $txt['calendar_prompt'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
				echo '
							<span style="color: #', $modSettings['cal_bdaycolor'], ';">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span> ';
		/* Each member in calendar_birthdays has:
				id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
		foreach ($context['calendar_birthdays'] as $member)
				echo '
							<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<b>' : '', $member['name'], $member['is_today'] ? '</b>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
							<span style="color: #', $modSettings['cal_eventcolor'], ';">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';
			/* Each event in calendar_events should have:
					title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				echo '
							', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" style="color: #FF0000;">*</a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<b>' . $event['title'] . '</b>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';

			// Show a little help text to help them along ;).
			if ($context['calendar_can_edit'])
				echo '
							(<a href="', $scripturl, '?action=helpadmin;help=calendar_how_edit" onclick="return reqWin(this.href);">', $txt['calendar_how_edit'], '</a>)';
		}
		echo '
						</span>
					</td>
				</tr>';
	}



$wut=$context['num_guests']+$context['num_users_online'].' '.sprintf($txt['users_active'], $modSettings['lastActive']);
	
	// "Users online" - in order of activity.
	echo '
				<tr>
					<td class="formsubtitle" colspan="2">', $wut, '</td>
				</tr><tr>
					<td rowspan="2" class="windowbg" width="20" valign="middle" align="center">
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<img src="', $settings['images_url'], '/icons/online.gif" alt="', $txt['online_users'], '" />', $context['show_who'] ? '</a>' : '', '
					</td>
					<td class="windowbg2" width="100%">';

	echo '
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', $context['num_guests'], ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . $context['num_users_online'], ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	if (!empty($context['num_users_hidden']) || ($context['show_buddies'] && !empty($context['show_buddies'])))
	{
		echo ' (';

		// Show the number of buddies online?
		if ($context['show_buddies'])
			echo $context['num_buddies'], ' ', $context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies'];

		// How about hidden users?
		if (!empty($context['num_users_hidden']))
			echo $context['show_buddies'] ? ', ' : '', $context['num_users_hidden'] . ' ' . $txt['hidden'];

		echo ')';
	}

	echo $context['show_who'] ? '</a>' : '', '
						<div class="smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
		echo '
							<div class="thin">', implode(', ', $context['list_users_online']), '</div>';

	echo '
							
							', $context['show_stats'] && !$settings['show_sp1_info'] ? '<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
												<span class="middletext">
							', $txt['most_online_today'], ': <b>', $modSettings['mostOnlineToday'], '</b>.
							', $txt['most_online_ever'], ': ', $modSettings['mostOnline'], ' (' , timeformat($modSettings['mostDate']), ')
						</span>
						</div>
					</td>
				</tr>
				<tr>
					<td style="background-color: #EFF1F3; display:none;">

					</td>
				</tr>';
				
	// Show YaBB SP1 style information...
	if ($settings['show_sp1_info'])
	{
		echo '
				<tr>
					<td class="formsubtitle" colspan="2">Board Statistics</td>
				</tr>
				<tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						<a href="', $scripturl, '?action=stats"><img src="', $settings['images_url'], '/icons/info.gif" alt="', $txt['forum_stats'], '" /></a>
					</td>
					<td class="windowbg2" width="100%">
						<span class="middletext">
							', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', $txt['latest_member'], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
							<br /> ' . $txt['latest_post'] . ': <b>&quot;' . $context['latest_post']['link'] . '&quot;</b>  ( ' . $context['latest_post']['time'] . ' )<br />
							<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? '<br />
							<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
						</span>
					</td>
				</tr>';
	}

	// If they are logged in, but SP1 style information is off... show a personal message bar.
	if ($context['user']['is_logged'] && !$settings['show_sp1_info'])
	{
		 echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['personal_message'], '</td>
				</tr><tr>
					<td class="windowbg" width="20" valign="middle" align="center">
						', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img src="', $settings['images_url'], '/message_sm.gif" alt="', $txt['personal_message'], '" />', $context['allow_pm'] ? '</a>' : '', '
					</td>
					<td class="windowbg2" valign="top">
						<b><a href="', $scripturl, '?action=pm">', $txt['personal_message'], '</a></b>
						<div class="smalltext">
							', $txt['you_have'], ' ', $context['user']['messages'], ' ', $context['user']['messages'] == 1 ? $txt['message_lowercase'] : $txt['msg_alert_messages'], '.... ', $txt['click'], ' <a href="', $scripturl, '?action=pm">', $txt['here'], '</a> ', $txt['to_view'], '
						</div>
					</td>
				</tr>';
	}

	// Show the login bar. (it's only true if they are logged out anyway.)
	if ($context['show_login_bar'])
	{
		echo '
				<tr>
					<td class="titlebg" colspan="2">', $txt['login'], ' <a href="', $scripturl, '?action=reminder" class="smalltext">(' . $txt['forgot_your_password'] . ')</a></td>
				</tr>
				<tr>
					<td class="windowbg" width="20" align="center">
						<a href="', $scripturl, '?action=login"><img src="', $settings['images_url'], '/icons/login.gif" alt="', $txt['login'], '" /></a>
					</td>
					<td class="windowbg2" valign="middle">
						<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
							<table border="0" cellpadding="2" cellspacing="0" align="center" width="100%"><tr>
								<td valign="middle" align="left">
									<label for="user"><b>', $txt['username'], ':</b><br />
									<input type="text" name="user" id="user" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="passwrd"><b>', $txt['password'], ':</b><br />
									<input type="password" name="passwrd" id="passwrd" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookielength"><b>', $txt['mins_logged_in'], ':</b><br />
									<input type="text" name="cookielength" id="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookieneverexp"><b>', $txt['always_logged_in'], ':</b><br />
									<input type="checkbox" name="cookieneverexp" id="cookieneverexp" checked="checked" class="check" /></label>
								</td>
								<td valign="middle" align="left">
									<input type="submit" value="', $txt['login'], '" />
								</td>
							</tr></table>
						</form>
					</td>
				</tr>';
	}

	echo '
			</table>
		</div>
	</div>';
}

?>
