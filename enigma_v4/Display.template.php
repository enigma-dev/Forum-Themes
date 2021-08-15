<?php
// Version: 1.1; Display

function template_main()
{
 	global $context, $settings, $options, $txt, $scripturl, $modSettings, $sourcedir;
	// Show the anchor for the top and for the first message. If the first message is new, say so.
	echo '
<a name="top"></a>
<a name="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a name="new"></a>' : '';

		// Show the linktree
	echo '
<div>', theme_linktree(), '</div>';

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		echo '
<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder" style="padding-top: 0; margin-bottom: 2ex;">
	<tr>
		<td class="titlebg" colspan="2" valign="middle" style="padding-left: 6px;">
			<img src="', $settings['images_url'], '/topic/', $context['poll']['is_locked'] ? 'normal_poll_locked' : 'normal_poll', '.gif" alt="" align="bottom" /> ', $txt['poll'], '
		</td>
	</tr>
	<tr>
		<td width="5%" valign="top" class="windowbg"><b>', $txt['poll_question'], ':</b></td>
		<td class="windowbg">
			', $context['poll']['question'];
		if (!empty($context['poll']['expire_time']))
			echo '
					&nbsp;(', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ': ', $context['poll']['expire_time'], ')';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
			<table>
				<tr>
					<td style="padding-top: 2ex;">
						<table border="0" cellpadding="0" cellspacing="0">';

				// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
				echo '
							<tr>
								<td style="padding-right: 2ex;', $option['voted_this'] ? 'font-weight: bold;' : '', '">', $option['option'], '</td>', $context['allow_poll_view'] ? '
								<td nowrap="nowrap">' . $option['bar'] . ' ' . $option['votes'] . ' (' . $option['percent'] . '%)</td>' : '', '
							</tr>';

			echo '
						</table>
					</td>
					<td valign="bottom" style="padding-left: 15px;">';

			// If they are allowed to revote - show them a link!
			if ($context['allow_change_vote'])
				echo '
					<a href="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], ';sesc=', $context['session_id'], '">', $txt['poll_change_vote'], '</a><br />';

			// If we're viewing the results... maybe we want to go back and vote?
			if ($context['poll']['show_results'] && $context['allow_vote'])
				echo '
						<a href="', $scripturl, '?topic=', $context['current_topic'], '.', $context['start'], '">', $txt['poll_return_vote'], '</a><br />';

			// If they're allowed to lock the poll, show a link!
			if ($context['poll']['lock'])
				echo '
						<a href="', $scripturl, '?action=lockVoting;topic=', $context['current_topic'], '.', $context['start'], ';sesc=', $context['session_id'], '">', !$context['poll']['is_locked'] ? $txt['poll_lock'] : $txt['poll_unlock'], '</a><br />';

			// If they're allowed to edit the poll... guess what... show a link!
			if ($context['poll']['edit'])
				echo '
						<a href="', $scripturl, '?action=editpoll;topic=', $context['current_topic'], '.', $context['start'], '">', $txt['poll_edit'], '</a>';

			echo '
					</td>
				</tr>', $context['allow_poll_view'] ? '
				<tr>
					<td colspan="2"><b>' . $txt['poll_total_voters'] . ': ' . $context['poll']['total_votes'] . '</b></td>
				</tr>' : '', '
			</table><br />';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
			<form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '" style="margin: 0px;">
				<table>
					<tr>
						<td colspan="2">';

			// Show a warning if they are allowed more than one option.
			if ($context['poll']['allowed_warning'])
				echo '
							', $context['poll']['allowed_warning'], '
						</td>
					</tr><tr>
						<td>';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
							', $option['vote_button'], ' ', $option['option'], '<br />';

			echo '
						</td>
						<td valign="bottom" style="padding-left: 15px;">';

			// Allowed to view the results? (without voting!)
			if ($context['allow_poll_view'])
				echo '
							<a href="', $scripturl, '?topic=', $context['current_topic'], '.', $context['start'], ';viewResults">', $txt['poll_results'], '</a><br />';

			// Show a link for locking the poll as well...
			if ($context['poll']['lock'])
				echo '
							<a href="', $scripturl, '?action=lockVoting;topic=', $context['current_topic'], '.', $context['start'], ';sesc=', $context['session_id'], '">', (!$context['poll']['is_locked'] ? $txt['poll_lock'] : $txt['poll_unlock']), '</a><br />';

			// Want to edit it? Click right here......
			if ($context['poll']['edit'])
				echo '
							<a href="', $scripturl, '?action=editpoll;topic=', $context['current_topic'], '.', $context['start'], '">', $txt['poll_edit'], '</a>';

				echo '
						</td>
					</tr><tr>
						<td colspan="2"><input type="submit" value="', $txt['poll_vote'], '" /></td>
					</tr>
				</table>
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>';
		}

		echo '
		</td>
	</tr>
</table>';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder" style="padding-top: 0; margin-bottom: 3ex;">
		<tr>
				<td class="titlebg" valign="middle" align="left" style="padding-left: 6px;">
						', $txt['calendar_linked_events'], '
				</td>
		</tr>
		<tr>
				<td width="5%" valign="top" class="windowbg">
						<ul>';
		foreach ($context['linked_calendar_events'] as $event)
			echo '
								<li>
									', ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '" style="color: red;">*</a> ' : ''), '<b>', $event['title'], '</b>: ', $event['start_date'], ($event['start_date'] != $event['end_date'] ? ' - ' . $event['end_date'] : ''), '
								</li>';
		echo '
						</ul>
				</td>
		</tr>
</table>';
	}

	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies']),
		'notify' => array('test' => 'can_mark_notify', 'text' => 'notify', 'image' => 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\');"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']),
		'custom' => array(),
		'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=sendtopic;topic=' . $context['current_topic'] . '.0'),
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'target="_blank"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);

	// Special case for the custom one.
	if ($context['user']['is_logged'] && $settings['show_mark_read'])
		$normal_buttons['custom'] = array('text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);
	elseif ($context['can_add_poll'])
		$normal_buttons['custom'] = array('text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);
	else
		unset($normal_buttons['custom']);

	// Show the page index... "Pages: [1]".
	echo '
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="middletext" valign="bottom" style="padding-bottom: 4px;">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><b>' . $txt['go_down'] . '</b></a>' : '', '</td>
		<td align="right" style="padding-right: 1ex;">
			<div class="nav" style="margin-bottom: 2px;"> ', $context['previous_next'], '</div>
			<table cellpadding="0" cellspacing="0">
				<tr>
					', template_button_strip($normal_buttons, 'bottom'), '
				</tr>
			</table>
		</td>
	</tr>
</table>';

	// Show the topic information - icon, subject, etc.
	echo '
<table width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="border-bottom: 0;">
		<tr class="catbg3">
				<td valign="middle" width="2%" style="padding-left: 6px;">
						<img src="', $settings['images_url'], '/topic/', $context['class'], '.gif" align="bottom" alt="" />
				</td>
				<td width="13%"> ', $txt['author'], '</td>
				<td valign="middle" width="85%" style="padding-left: 6px;" id="top_subject">
						', $txt['topic'], ': ', $context['subject'], ' &nbsp;(', $txt['read'], ' ', $context['num_views'], ' ', $txt['times'], ')
				</td>
		</tr>';

	echo '
</table>';

	echo '
<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return in_edit_mode == 1 ? modify_save(\'' . $context['session_id'] . '\') : confirm(\'' . $txt['quickmod_confirm'] . '\');">';

	// These are some cache image buttons we may want.
	$reply_button = create_button('quote.gif', 'reply_quote', 'quote', 'align="middle"');
	$modify_button = create_button('modify.gif', 'modify_msg', 'modify', 'align="middle"');
	$remove_button = create_button('delete.gif', 'remove_message', 'remove', 'align="middle"');
	$split_button = create_button('split.gif', 'split', 'split', 'align="middle"');

// Time to display all the posts
	echo '
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="bordercolor">';

	$ignoredMsgs = array();
	$removableMessageIDs = array();
	$alternate = false;

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		$ignoring = false;
		$alternate = !$alternate;
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];

		// Are we ignoring this message?
		if (!empty($options['posts_apply_ignore_list']) && in_array($message['member']['id'], $context['user']['ignoreusers']))
		{
			$ignoring = true;
			$ignoredMsgs[] = $message['id'];
		}

// echo '<!-- JOSHINFO: '; var_dump($message['member']); echo '-->';		
		if ($message['member']['username'] == 'Fede-lasse' && !$context['user']['is_admin'])
		{
			echo '
       	<tr><td class="windowbg2">
          <div style="text-align: center; background: transparent repeating-linear-gradient(-45deg, rgba(200, 200, 200, 0) 0px, rgba(200, 200, 200, 0) 3px, rgb(200, 200, 200) 4px, rgba(200, 200, 200, 0) 5px); padding: 8px;">
            <span style="font-style: italic; background-color: #DDD; border-radius: 4px; padding: 4px 12px; display: inline-block;">';
			echo "Post made " . $message['time'] . " was deleted at the author's request.";
			echo '</span>
          </div>
       </td></tr>';
			continue;
		}

		echo '
	<tr><td>';

		// Show the message anchor and a "new" anchor if this message is new.
		if ($message['id'] != $context['first_message'])
			echo '
		<a name="msg', $message['id'], '"></a>', $message['first_new'] ? '<a name="new"></a>' : '';

		echo '
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
			<tr><td class="', $message['alternate'] == 0 ? 'windowbg' : 'windowbg2', '">';
		
		
		/////Post table with author name
		////////////////////////////////
		
		$gender = strtolower($message['member']['gender']['name']);
		if ($gender == "") { $gender = "unknown"; }

		switch ($gender)
		{
			case "male": $genderecho = "Male"; break;
			case "female": $genderecho = "Female"; break;
			case "unknown": $genderecho = "Unknown gender"; break;
		}

		// Show information about the poster of this message.
		echo '
				<table width="100%" cellpadding="5" cellspacing="0" style="table-layout: fixed;" border="0">
					<tr>
						<td valign="top" width="16%" style="overflow: hidden;" class="postquad1">
							<b>' . ($message['member']['online']['is_online'] ? '<img src="'.$settings['images_url'].'/onoff/online_'.$gender.'.png" alt="Online  ('.$genderecho.')" title="Online  ('.$genderecho.')" />' : '<img src="'.$settings['images_url'].'/onoff/offline_'.$gender.'.png" alt="Offline ('.$genderecho.')" title="Offline ('.$genderecho.')" />') . ' ', $message['member']['link'], '</b>
						</td>';
		
		
		///////Bloated-to-hell options
		//////////////////////////////
		
		// Done with the information about the poster... on to the post itself.
		echo '
						<td valign="top" width="85%" class="postquad2">
						  <div style="visibility:hidden;height:0px !important;width:0px !important;overflow:hidden;" id="subject_' . $message['id'] . '"></div>
							<table width="100%" border="0"><tr>
								<td valign="middle"><a href="', $message['href'], '"><img src="', $settings['theme_url'], '/images/xx.gif" alt="" border="0" /></a></td>
								<td valign="middle">'/*.
									((empty($message['counter'])) ? ('<div style="font-weight: bold;" id="subject_' . $message['id'] . '">
										<a href="' . $message['href'] . '">' . $message['subject'] . '</a>
									</div>') : '')*/;
		

		// If this is the first post, (#0) just say when it was posted - otherwise give the reply #.
		echo '
									<div class="smalltext"><b>', !empty($message['counter']) ? $txt['reply'] . ' #' . $message['counter'] : '', ' ', "Posted " . $txt['on'], ':</b> ', $message['time'], '</div></td>
								<td align="', !$context['right_to_left'] ? 'right' : 'left', '" valign="bottom" height="20" style="font-size: smaller;">';

		// Can they reply? Have they turned on quick reply?
		if ($context['can_reply'] && !empty($options['display_quick_reply']))
			echo '
					<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';num_replies=', $context['num_replies'], ';sesc=', $context['session_id'], '" onclick="doQuote(', $message['id'], ', \'', $context['session_id'], '\'); return false;">', $reply_button, '</a>';

		// So... quick reply is off, but they *can* reply?
		elseif ($context['can_reply'])
			echo '
					<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';num_replies=', $context['num_replies'], ';sesc=', $context['session_id'], '">', $reply_button, '</a>';

		// Normal modify button
		if ($message['can_modify'])
			echo '
					<a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';sesc=', $context['session_id'], '"><img src="' . $settings['images_url'] . '/buttons/modify.gif" alt="Edit" /><b>Edit</b></a>';
    
		// Quick modify
		if ($message['can_modify']) 
			echo '
					<a href="javascript:;" onclick="oQuickModify.modifyMsg(\'' . $message['id'] . '\', \'' . $context['session_id'] . '\');"><img src="' . $settings['images_url'] . '/buttons/modify_quick.png" alt="" id="modify_button_' . $message['id'] . '" style="cursor: pointer;" /><b>QuickEdit</b></a>';
						
		// quick ban
		if ($message['can_modify'] && $context['user']['is_admin'])
			echo '
				<a href="javascript:;" onclick="oQuickBan.show(\'' . $message['id'] . '\', ' . $message['member']['id'] . ', \'' . $context['session_id'] . '\');"><img src="' . $settings['images_url'] . '/buttons/ban.png" alt="Ban" /><b>Ban</b></a>';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
									<a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '"><img src="' . $settings['images_url'] . '/buttons/report.png" alt="Report" /><b>Report</b></a>';

		// How about... even... remove it entirely?!
		if ($message['can_remove'])
			echo '
					<a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');">', $remove_button, '</a>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'])
			echo '
					<a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">', $split_button, '</a>';
		
		// Show a checkbox for quick moderation?
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '
									<span class="inline_mod_check" style="display: none;" id="in_topic_mod_check_', $message['id'], '"></span>';

		// Show a checkbox for quick moderation? OLD SMF 1.1.x
		/*if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '
									<input type="checkbox" name="msgs[]" value="', $message['id'], '" class="check" ', empty($settings['use_tabs']) ? 'onclick="document.getElementById(\'quickmodSubmit\').style.display = \'\';"' : '', ' />';*/
		
		
		echo '
		            </td>
		          </tr></table>';
		echo '
		        </td>
		      </tr>';
		
		/////End horrendously bloated options
		/////////////////////////////////////
		
		
		
		
		
		//////Information other than username
		/////////////////////////////////////
		echo '
		      <tr>
		        <td rowspan="2" class="postquad3" valign="top">';
		
		// Show avatars, images, etc.?
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']))
		{
			if ($message['member']['is_banned'] && !empty($modSettings['enable_banned_membav']))
			{
				// show banned avatar
				echo '
					<div style="overflow: auto; width: 100%;"><img src="', $settings['theme_url'], '/images/avatar_banned.png" alt="" /></div><br />';
			}
			elseif (!empty($message['member']['avatar']['image']))
			//else (do this for forcing avatars on everyone)
			{
				// the user isnt banned so show his/her avatar
				echo '
					<div style="overflow: auto; width: 100%;">', $message['member']['avatar']['image'], '</div><br />';
				
				// april fool's day 2010
				/*echo '
					<!-- happy april fool\'s! -a2h -->
					<div style="overflow: auto; width: 100%;"><img src="http://unicornify.appspot.com/avatar/', md5($message['member']['email']), '?s=128" alt="" title="Chaaaaarlie! Chaaaaaaaaaaaarlie!" width="96" height="96" /></div><br />';*/
			}
		}


		// Show the member's custom title, if they have one.
		if (isset($message['member']['title']) && $message['member']['title'] != '')
			echo '
								', $message['member']['title'], '<br />';

		// Show the member's primary group (like 'Administrator') if they have one.
		if (isset($message['member']['group']) && $message['member']['group'] != '') {
			echo '
								';
			switch ($message['member']['group']) {
			case "Dev Team": case "Developers": //echo '<object type="image/svg+xml" data="membericons/developer.svg">Developer</object>'; break;
				echo '<img src="membericons/developer.svg.png" alt="Developer" />'; break;
			case "LGM Developers": //echo '<object type="image/svg+xml" data="membericons/lgm_developer.svg">LGM Developer</object>'; break;
				echo '<img src="membericons/lgm_developer.svg.png" alt="LGM Developer" />'; break;
			case "Moderator":
			case "Contributor": //echo '<object type="image/svg+xml" data="membericons/contributor.svg">Contributor</object>'; break;
				echo '<img src="membericons/contributor.svg.png" alt="Contributor" />'; break;
			case "Member": //echo '<object type="image/svg+xml" data="membericons/member.svg">Member</object>'; break;
				echo '<img src="membericons/member.svg.png" alt="Member" />'; break;
			case "Moron": //echo '<object type="image/svg+xml" data="membericons/moron.svg">Moron</object>'; break;
				echo '<img src="membericons/moron.svg.png" alt="Moron" />'; break;
			case "Fede": //echo '<object type="image/svg+xml" data="membericons/moron.svg">Moron</object>'; break;
				echo '<img src="membericons/fede.svg.png" alt="Fede" />'; break;
			case "Resident Troll": //echo '<object type="image/svg+xml" data="membericons/moron.svg">Moron</object>'; break;
				echo '<img src="membericons/troll.svg.png" alt="Resident Troll" />'; break;
			default: echo '"' . $message['member']['group'] . '"'; // print_r($message['member']);
			}
			echo '<br />';
		}
    else
    	echo '
                                                                <img src="membericons/member.svg.png" alt="Member" /><br/>';
	//echo '
	//							Member<br />';
    
		// Don't show these things for guests.
		if (!$message['member']['is_guest'])
		{ //I hate this. -Josh

			//Show there location
			if (!empty($modSettings['enable_member_location_post']) && !empty($message['member']['location']))
			echo $txt['location'], ': ', $message['member']['location'], '<br />';
			
			// Is karma display enabled?  Total or +/-?
			if ($modSettings['karmaMode'] == '1')
				echo '
								<br />
								', $modSettings['karmaLabel'], ' ', $message['member']['karma']['good'] - $message['member']['karma']['bad'], '<br />';
			elseif ($modSettings['karmaMode'] == '2')
				echo '
								<br />
								', $modSettings['karmaLabel'], ' +', $message['member']['karma']['good'], '/-', $message['member']['karma']['bad'], '<br />';

			// Is this user allowed to modify this member's karma?
			if ($message['member']['karma']['allow'])
				echo '
								<a href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.' . $context['start'], ';m=', $message['id'], ';sesc=', $context['session_id'], '">', $modSettings['karmaApplaudLabel'], '</a>
								<a href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';m=', $message['id'], ';sesc=', $context['session_id'], '">', $modSettings['karmaSmiteLabel'], '</a><br />';

			// Show online and offline buttons?
			if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'])
				echo '
								', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '" title="' . $message['member']['online']['label'] . '">' : '', $settings['use_image_buttons'] ? '<img src="' . $message['member']['online']['image_href'] . '" alt="' . $message['member']['online']['text'] . '" border="0" style="margin-top: 2px;" />' : $message['member']['online']['text'], $context['can_send_pm'] ? '</a>' : '', $settings['use_image_buttons'] ? '<span class="smalltext"> ' . $message['member']['online']['text'] . '</span>' : '', '<br /><br />';

			// Show the member's gender icon?
			if (!empty($settings['show_gender']) && $message['member']['gender']['image'] != '')
				echo '
								', $txt['gender'], ': ', $message['member']['gender']['image'], '<br />';

			//Show date registered
			if (!empty($modSettings['enable_join_date_post']))
			  echo  'Joined:  ', timeformat($message['member']['registered_timestamp'] ,'%b %Y'), '<br />';
			  
			// Show how many posts they have made.
			echo '
								', $txt['member_postcount'], ': ', /* <3 u fede */ $message['member']['id'] == 124 ? '-2342534e-10' : $message['member']['posts'], '<br />';
			
			// Are we showing the warning status?
			/*if ($message['member']['can_see_warning'])
				echo '
								', $context['can_issue_warning'] ? '<a href="' . $scripturl . '?action=profile;area=issuewarning;u=' . $message['member']['id'] . '">' : '', '<img src="', $settings['images_url'], '/warning_', $message['member']['warning_status'], '.gif" alt="', $txt['user_warn_' . $message['member']['warning_status']], '" />', $context['can_issue_warning'] ? '</a>' : '', ' <span class="warn_', $message['member']['warning_status'], '">', $txt['warn_' . $message['member']['warning_status']], '</span><br />
								<br />';*/
			
			if ($message['member']['can_see_warning'])
				echo '
								Warn: ' , $message['member']['warning'] , '% (' , strtolower($txt['warn_' . $message['member']['warning_status']]) , ')<br />
								<br />';

			/* Show their personal text?
			if (!empty($settings['show_blurb']) && $message['member']['blurb'] != '')
				echo '
								', $message['member']['blurb'], '<br />
								<br />';*/

			// This shows the popular messaging icons.
			echo '
								', $message['member']['icq']['link'], '
								', $message['member']['msn']['link'], '
								', $message['member']['aim']['link'], '
								', $message['member']['yim']['link'], '<br />';

			// Show the profile, website, email address, and personal message buttons.
			if ($settings['show_profile_buttons'])
			{
				// Don't show the profile button if you're not allowed to view the profile.
				if ($message['member']['can_view_profile'])
					echo '
								<a href="', $message['member']['href'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/icons/profile_sm.gif" alt="' . $txt['view_profile'] . '" title="' . $txt['view_profile'] . '" border="0" />' : $txt['view_profile']), '</a>';

				// Don't show an icon if they haven't specified a website.
				if ($message['member']['website']['url'] != '' && !isset($context['disabled_fields']['website']))
					echo '
								<a href="', $message['member']['website']['url'], '" title="' . $message['member']['website']['title'] . '" target="_blank">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/www_sm.gif" alt="' . $txt['www'] . '" border="0" />' : $txt['www']), '</a>';

				// Don't show the email address if they want it hidden.
				if (in_array($message['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
					echo '
								<a href="mailto:', $message['member']['email'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" border="0" />' : $txt['email']), '</a>';

				// Since we know this person isn't a guest, you *can* message them.
				if ($context['can_send_pm'])
					echo '
								<a href="', $scripturl, '?action=pm;sa=send;u=', $message['member']['id'], '" title="', $message['member']['online']['label'], '">', $settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/im_' . ($message['member']['online']['is_online'] ? 'on' : 'off') . '.gif" alt="' . $message['member']['online']['label'] . '" border="0" />' : $message['member']['online']['label'], '</a>';
			}
		}
		// Otherwise, show the guest's email.
		elseif (empty($message['member']['hide_email']))
			echo '
								<br />
								<br />
								<a href="mailto:', $message['member']['email'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" border="0" />' : $txt['email']), '</a>';
    
    
    
    echo '
		        </td>';
		
		//////End of all that nasty info
		////////////////////////////////
		
		
		
		// Show the post itself, finally!
		echo '
		        <td class="postquad4" valign="top">
                ';
                $row = mysqli_fetch_assoc(mysqli_query("SELECT * FROM edc_messages WHERE id_msg=".$message['id']));
                
                $which = (isset($_GET["bbparser"]))?$_GET["bbparser"]:"no";
                $body = (strtolower($which)=="josh")?bbparse($row["body"]):$message["body"];
                echo '
							<div class="post"', $message['can_modify'] ? ' id="msg_' . $message['id'] . '"' : '', '>', $body, '</div>
						</td>
					</tr>';

		// Now for the attachments, signature, ip logged, etc...
		echo '
					<tr>
						<td valign="bottom" class="smalltext" width="85%" colspan="2">
							<table width="100%" border="0" style="table-layout: fixed;"><tr>
								<td colspan="2" class="smalltext" width="100%">';

		// Assuming there are attachments...
		if (!empty($message['attachment']))
		{
			echo '
									<hr width="100%" size="1" class="hrcolor" />
									<div style="overflow: auto; width: 100%;">';
			foreach ($message['attachment'] as $attachment)
			{
				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
									<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" border="0" /></a><br />';
					else
						echo '
									<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '" border="0" /><br />';
				}
				echo '
										<a href="' . $attachment['href'] . '"><img src="' . $settings['images_url'] . '/icons/clip.gif" align="middle" alt="*" border="0" />&nbsp;' . $attachment['name'] . '</a> (', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
			}

			echo '
									</div>';
		}

		echo '
								</td>
							</tr><tr>
								<td valign="bottom" class="smalltext" id="modified_', $message['id'], '">';

		// Show "\AB Last Edit: Time by Person \BB" if this post was edited.
		if ($settings['show_modify'] && !empty($message['modified']['name']))
			echo '
									&#171; <i>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</i> &#187;';

		echo '
								</td>
								<td align="', !$context['right_to_left'] ? 'right' : 'left', '" valign="bottom" class="smalltext">';

		
		echo '
									<img src="', $settings['images_url'], '/ip.gif" alt="" border="0" />';

		// Show the IP to this user for this post - because you can moderate?
		if ($context['can_moderate_forum'] && !empty($message['member']['ip']))
			echo '
									<a href="', $scripturl, '?action=trackip;searchip=', $message['member']['ip'], '">', $message['member']['ip'], '</a> <a href="', $scripturl, '?action=helpadmin;help=see_admin_ip" onclick="return reqWin(this.href);" class="help">(?)</a>';
		// Or, should we show it because this is you?
		elseif ($message['can_see_ip'])
			echo '
									<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $message['member']['ip'], '</a>';
		// Okay, are you at least logged in?  Then we can show something about why IPs are logged...
		elseif (!$context['user']['is_guest'])
			echo '
									<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $txt['logged'], '</a>';
		// Otherwise, you see NOTHING!
		else
			echo '
									', $txt['logged'];

		echo '
								</td>
							</tr></table>';

		// Show the member's signature?
		if (!empty($message['member']['signature']) && empty($options['show_no_signatures']))
			echo '
							<!--<hr width="100%" size="1" class="hrcolor" />-->
							<div class="signature">', $message['member']['signature'], '</div>';

		echo '
						</td>
					</tr>
				</table>
			</td></tr>
		</table>
	</td></tr>';
	}
	echo '
	<tr><td style="padding: 0 0 1px 0;"></td></tr>
</table>
<a name="lastPost"></a>';

	// As before, build the custom button right.
	if ($context['can_add_poll'])
		$normal_buttons['custom'] = array('text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);
	elseif ($context['user']['is_logged'] && $settings['show_mark_read'])
		$normal_buttons['custom'] = array('text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);

	echo '
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="middletext">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top"><b>' . $txt['go_up'] . '</b></a>' : '', '</td>
		<td align="right" style="padding-right: 1ex;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					', template_button_strip($normal_buttons, 'top', true), '
				</tr>
			</table>
		</td>
	</tr>
</table>';

echo '
<table border="0" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 1ex;">
		<tr>';
	if ($settings['linktree_inline'])
			echo '
				<td valign="top">', theme_linktree(), '</td> ';
	echo '
				<td valign="top" align="', !$context['right_to_left'] ? 'right' : 'left', '" class="nav"> ', $context['previous_next'], '</td>
		</tr>
</table>';

	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
	);

	// Restore topic. eh?  No monkey business.
	if ($context['can_restore_topic'])
		$mod_buttons[] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);

	echo '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 1ex;">
		<tr>
			', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '
		</tr>
	</table>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
		echo '
	<input type="hidden" name="sc" value="', $context['session_id'], '" />';

	if (empty($settings['use_tabs']))
		echo '
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		document.getElementById("quickmodSubmit").style.display = "none";
	// ]]></script>';

	echo '
</form>';

	echo '
<div class="tborder"><div class="titlebg2" style="padding: 4px;" align="', !$context['right_to_left'] ? 'right' : 'left', '">';
	// Show the jumpto box, or actually...let Javascript do it.
	echo '
			<div class="plainbox" style="text-align: ', !$context['right_to_left'] ? 'right' : 'left', ';" id="display_jump_to">&nbsp;</div>';
			/*echo '
	<form action="', $scripturl, '" method="get" accept-charset="', $context['character_set'], '" style="padding:0; margin: 0;">
		<span class="smalltext">' . $txt['jump_to'] . ':</span>
		<select name="jumpto" id="jumpto" onchange="if (this.selectedIndex > 0 &amp;&amp; this.options[this.selectedIndex].value) window.location.href = smf_scripturl + this.options[this.selectedIndex].value.substr(smf_scripturl.indexOf(\'?\') == -1 || this.options[this.selectedIndex].value.substr(0, 1) != \'?\' ? 0 : 1);">
			<option value="">' . $txt['select_destination'] . ':</option>';
	foreach ($context['jump_to'] as $category)
	{
		echo '
			<option value="" disabled="disabled">-----------------------------</option>
			<option value="#', $category['id'], '">', $category['name'], '</option>
			<option value="" disabled="disabled">-----------------------------</option>';
		foreach ($category['boards'] as $board)
			echo '
			<option value="?board=', $board['id'], '.0"', $board['is_current'] ? ' selected="selected"' : '', '> ' . str_repeat('==', $board['child_level']) . '=> ' . $board['name'] . '</option>';
	}
	echo '
		</select>&nbsp;
		<input type="button" value="', $txt['go'], '" onclick="if (this.form.jumpto.options[this.form.jumpto.selectedIndex].value) window.location.href = \'', $scripturl, '\' + this.form.jumpto.options[this.form.jumpto.selectedIndex].value;" />
	</form>*/ echo '
</div></div>';

	echo '<br />';

	
	// the quick reply box
	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '
			<a id="quickreply"></a>
			<div class="tborder" id="quickreplybox">
				<h3 class="catbg"><span class="left"></span>
					<a href="javascript:oQuickReply.swap();">
						<img src="', $settings['images_url'], '/', $options['display_quick_reply'] == 2 ? 'collapse' : 'expand', '.gif" alt="+" id="quickReplyExpand" class="icon" />
					</a>
					<a href="javascript:oQuickReply.swap();">', $txt['quick_reply'], '</a>
				</h3>
				<div id="quickReplyOptions"', $options['display_quick_reply'] == 2 ? '' : ' style="display: none"', '>
					<span class="upperframe"><span></span></span>
					<div class="roundframe">
						<p class="smalltext">', $txt['quick_reply_desc'], '</p>
						', $context['is_locked'] ? '<p class="alert smalltext">' . $txt['quick_reply_warning'] . '</p>' : '',
						$context['oldTopicError'] ? '<p class="alert smalltext">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</p>' : '', '
						<div id="quickReplyContent">', $context['can_reply_approved'] ? '' : '<em>' . $txt['wait_for_approval'] . '</em>', '
							', !$context['can_reply_approved'] && $context['require_verification'] ? '<br />' : '', '
							<form action="', $scripturl, '?action=post2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);" style="margin: 0;">
								<input type="hidden" name="topic" value="', $context['current_topic'], '" />
								<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '" />
								<input type="hidden" name="icon" value="xx" />
								<input type="hidden" name="from_qr" value="1" />
								<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '" />
								<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '" />
								<input type="hidden" name="goback" value="', empty($options['return_to_post']) ? '0' : '1', '" />
								<input type="hidden" name="num_replies" value="', $context['num_replies'], '" />';

			// Guests just need more.
			if ($context['user']['is_guest'])
				echo '
								<strong>', $txt['name'], ':</strong> <input type="text" name="guestname" value="', $context['name'], '" size="25" class="input_text" />
								<strong>', $txt['email'], ':</strong> <input type="text" name="email" value="', $context['email'], '" size="25" class="input_text" /><br />';

			// Is visual verification enabled?
			if ($context['require_verification'])
				echo '
								<strong>', $txt['verification'], ':</strong>', template_control_verification($context['visual_verification_id'], 'quick_reply'), '<br />';

			echo '
								<textarea cols="75" rows="7" style="', $context['browser']['is_ie8'] ? 'max-width: 95%; min-width: 95%' : 'width: 95%', '; height: 100px;" name="message" tabindex="1"></textarea><br />
								<input type="submit" name="post" value="', $txt['post'], '" onclick="return submitThisOnce(this);" accesskey="s" tabindex="2" class="button_submit" />
								<input type="submit" name="preview" value="', $txt['preview'], '" onclick="return submitThisOnce(this);" accesskey="p" tabindex="4" class="button_submit" />';

			if ($context['show_spellchecking'])
				echo '
								<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'postmodify\', \'message\');" tabindex="5" class="button_submit" />';

			echo '
								<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
								<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
							</form>
						</div>
					</div>
					<span class="lowerframe"><span></span></span>
				</div>
			</div>';
	}
	
//Show who's reading
	if (!empty($settings['display_who_viewing']))
	{
		echo '
  <div class="tborder" style="padding:1px;">
    <table border="0" width="100%" class="windowbg2">
      <tr style="background: #DDE1E6;">
        <td>
          <div style="width:100%; padding: 2px;">Currently, ',count($context['view_members']), ' ', ((count($context['view_members']) == 1) ? $txt['who_member'] : $txt['members']), ' and ', $txt['who_and'], $context['view_num_guests'], ' ', (($context['view_num_guests']==1) ? $txt['guest']:$txt['guests']),' are viewing this topic</div>
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
	
	
	if ($context['show_spellchecking'])
		echo '
<script language="JavaScript" type="text/javascript" src="' . $settings['default_theme_url'] . '/spellcheck.js"></script>';

echo '<div id="banuseridhold" style="visibility:hidden;">0</div>';

// QUICK MODIFY FROM SMF 2.0 RC2 DEFAULT THEME
	if ($context['show_spellchecking'])
		echo '
			<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>
				<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/spellcheck.js"></script>';
				
	echo '
				<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/topic.js"></script>
				<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/quickban.js"></script>
				<script type="text/javascript"><!-- // --><![CDATA[';

	if (!empty($options['display_quick_reply']))
		echo '
					var oQuickReply = new QuickReply({
						bDefaultCollapsed: ', !empty($options['display_quick_reply']) && $options['display_quick_reply'] == 2 ? 'false' : 'true', ',
						iTopicId: ', $context['current_topic'], ',
						iStart: ', $context['start'], ',
						sScriptUrl: smf_scripturl,
						sImagesUrl: "', $settings['images_url'], '",
						sContainerId: "quickReplyOptions",
						sImageId: "quickReplyExpand",
						sImageCollapsed: "collapse.gif",
						sImageExpanded: "expand.gif",
						sJumpAnchor: "quickreply"
					});';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $removableMessageIDs), '\'],
						sSessionId: \'', $context['session_id'], '\',
						sSessionVar: \'', $context['session_var'], '\',
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.gif\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.gif\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';

	echo '
					if (typeof(window.XMLHttpRequest) != "undefined")
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" style="padding: 4px;" class="error"></div>
									<textarea class="editor" name="message" rows="12" style="' . ($context['browser']['is_ie8'] ? 'max-width: 94%; min-width: 94%' : 'width: 94%') . '; margin-bottom: 10px;" tabindex="7">%body%</textarea><br />
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
									<input type="hidden" name="msg" value="%msg_id%" />
									<div class="centertext">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="8" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit" />&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="9" onclick="spellCheck(\'quickModForm\' . \'message\');" class="button_submit" />&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="9" onclick="return oQuickModify.modifyCancel();" class="button_submit" />
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" style="width: 90%;" name="subject" value="%subject%" size="80" maxlength="80" tabindex="6" class="input_text" />'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: "' . $txt['topic'] . ': %subject% &nbsp;(' . $txt['read'] . ' ' . $context['num_views'] . ' ' . $txt['times'] . ')",
							sErrorBorderStyle: "1px solid red"
						});
						
						var oQuickBan = new QuickBan({
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							bPublic: ', $modSettings['enable_quickban_public'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sBannerName: "', $context['user']['name'], '",
							sTemplateBodyBan: ', JavaScriptEscape('
								<div id="quickban" class="centertext" style="width:500px;margin-top:16px;padding:8px;background:#ffd;border:1px solid #bb9;">
									<div><img src="' . $settings['images_url'] . '/admin/ban.gif" alt="Ban" /> <b>Ban this user</b></div>
									Reason: <input type="text" name="reason"  />
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
									<input type="hidden" name="msg" value="%msg_id%" />
									<input type="button" value="Continue" onclick="return oQuickBan.banSave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" />
									<input type="button" value="Cancel" onclick="return oQuickBan.banCancel();" />
								</div>'), ',
							sErrorBorderStyle: "1px solid red"
						});

						aJumpTo[aJumpTo.length] = new JumpTo({
							sContainerId: "display_jump_to",
							sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
							iCurBoardId: ', $context['current_board'], ',
							iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
							sCurBoardName: "', $context['jump_to']['board_name'], '",
							sBoardChildLevelIndicator: "==",
							sBoardPrefix: "=> ",
							sCatSeparator: "-----------------------------",
							sCatPrefix: "",
							sGoButtonLabel: "', $txt['go'], '"
						});

						aIconLists[aIconLists.length] = new IconList({
							sBackReference: "aIconLists[" + aIconLists.length + "]",
							sIconIdPrefix: "msg_icon_",
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iBoardId: ', $context['current_board'], ',
							iTopicId: ', $context['current_topic'], ',
							sSessionId: "', $context['session_id'], '",
							sSessionVar: "', $context['session_var'], '",
							sLabelIconList: "', $txt['message_icon'], '",
							sBoxBackground: "transparent",
							sBoxBackgroundHover: "#ffffff",
							iBoxBorderWidthHover: 1,
							sBoxBorderColorHover: "#adadad" ,
							sContainerBackground: "#ffffff",
							sContainerBorder: "1px solid #adadad",
							sItemBorder: "1px solid #ffffff",
							sItemBorderHover: "1px dotted gray",
							sItemBackground: "transparent",
							sItemBackgroundHover: "#e0e0f0"
						});
					}';

	if (!empty($ignoredMsgs))
	{
		echo '
					var aIgnoreToggles = new Array();';

		foreach ($ignoredMsgs as $msgid)
		{
			echo '
					aIgnoreToggles[', $msgid, '] = new smc_Toggle({
						bToggleEnabled: true,
						bCurrentlyCollapsed: true,
						aSwappableContainers: [
							\'msg_', $msgid, '_extra_info\',
							\'msg_', $msgid, '\',
							\'msg_', $msgid, '_footer\',
							\'msg_', $msgid, '_quick_mod\',
							\'modify_button_', $msgid, '\',
							\'msg_', $msgid, '_signature\'

						],
						aSwapLinks: [
							{
								sId: \'msg_', $msgid, '_ignored_link\',
								msgExpanded: \'\',
								msgCollapsed: ', JavaScriptEscape($txt['show_ignore_user_post']), '
							}
						]
					});';
		}
	}

	echo '
				// ]]></script>';
				
// END QUICK MODIFY

}





?>
