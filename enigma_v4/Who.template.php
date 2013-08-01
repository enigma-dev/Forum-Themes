<?php
// If you wanna translate this file or ad more spiders, you can find instructions in this post
// http://www.simplemachines.org/community/index.php?topic=19243.msg156339#msg156339
// The only template in the file.

function template_main()

{

	global $context, $settings, $options, $scripturl, $txt;

	// Display the table header and linktree.

	echo '

	<div style="padding: 3px;">', theme_linktree(), '</div>';
	
	$brokendown = array (

		'Members' => array(),
		'Guests' => array(),
		'Spiders' => array(),
	);

	foreach($context['members'] AS $key => $member)
	{
		$spider = getAgent($member['query']['USER_AGENT'], $context['members'][$key]['name'], $agent, $member['id'] == 0);
		$context['members'][$key]['agent'] = $agent;
		$member['query']['USER_AGENT'] = isset($member['query']['USER_AGENT']) ? $member['query']['USER_AGENT'] : '';
		
		if ( $member['id'] != 0 )
			$brokendown['Members'][] = &$context['members'][$key];
		else if ( $spider )
			$brokendown['Spiders'][] = &$context['members'][$key];
		else
			$brokendown['Guests'][] = &$context['members'][$key];
	}


//	echo '<pre>'; print_r($brokendown['Spiders']); echo '</pre>'; return;
	foreach($brokendown AS $group => $members)
	{
		echo '
	<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder">
		<tr>
			<td class="catbg">', $group, '</td>
		</tr>
	</table>
	<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder">
		<tr class="titlebg">
			<td width="30%"><a href="' . $scripturl . '?action=who;sort=user', $context['sort_direction'] == 'down' && $context['sort_by'] == 'user' ? ';asc' : '', '">', $txt['who_user'], ' ', $context['sort_by'] == 'user' ? '<img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" border="0" />' : '', '</a></td>
			<td style="width: 14ex;"><a href="' . $scripturl . '?action=who;sort=time', $context['sort_direction'] == 'down' && $context['sort_by'] == 'time' ? ';asc' : '', '">', $txt['who_time'], ' ', $context['sort_by'] == 'time' ? '<img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" border="0" />' : '', '</a></td>
			<td>', $txt['who_action'], '</td>
		</tr>';

		// This is used to alternate the color of the background.
		$alternate = true;

		// For every member display their name, time and action (and more for admin).
		foreach ($members as $member)
		{
		// $alternate will either be true or false.  If it's true, use "windowbg2" and otherwise use "windowbg".
			echo '
			<tr class="windowbg', $alternate ? '2' : '', '">
				<td>';
			// Guests don't have information like icq, msn, y!, and aim... and they can't be messaged.
			if (!$member['is_guest'])
			{
				echo '
					<div style="float: right; width: 14ex;">
						', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['label'] . '">' : '', $settings['use_image_buttons'] ? '<img src="' . $member['online']['image_href'] . '" alt="' . $member['online']['text'] . '" border="0" align="middle" />' : $member['online']['text'], $context['can_send_pm'] ? '</a>' : '', '
						', $member['icq']['link'], ' ', $member['msn']['link'], ' ', $member['yim']['link'], ' ', $member['aim']['link'], '
					</div>';
		}
		echo '
				<span', $member['is_hidden'] ? ' style="font-style: italic;"' : '', '>', $member['is_guest'] ? $member['name'] : '<a href="' . $member['href'] . '" title="' . $txt['profile_of'] . ' ' . $member['name'] . '"' . (empty($member['color']) ? '' : ' style="color: ' . $member['color'] . '"') . '>' . $member['name'] . '</a>', '</span>';
		if ( !empty($member['ip']) )
			echo ' <br /> <span class="smalltext">(<b><a href="' . $scripturl . '?action=trackip;searchip=' . $member['ip'] . '" target="_blank" title="' . $member['ip'] . '" class="smalltext2">' . $member['ip'] . '</a></b>, <acronym title="' . $member['query']['USER_AGENT'] . '">' . $member['agent'] . '</acronym>)</span>';	
		echo '
			</td>
			<td nowrap="nowrap">', $member['time'], '</td>
			<td>', $member['action'], '</td>
		</tr>
		';

		// Switch alternate to whatever it wasn't this time. (true -> false -> true -> false, etc.)
		$alternate = !$alternate;
	}
		echo '</table><br/>';
	}
	echo '
	<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder"><tr><td class="titlebg">

		<b>', $txt['pages'], ':</b> ', $context['page_index'], '
	</td></tr></table>';
}

function getAgent( &$user_agent, &$user_name, &$result, $guest )

{

 $known_agents = array (

 //Search Spiders

		array (

			'agent' => 'sogou spider',

			'spidername' => 'Sogou spider',

			'spider' => true,

		 ),

		array (

			'agent' => 'Twiceler',

			'spidername' => 'Twiceler spider',

			'spider' => true,

		 ),

                 array (

			'agent' => 'www.fi crawler',

			'spidername' => 'www.fi spider',

			'spider' => true,

		 ),

		array (

                        'agent' => 'WISENutbot',

                        'spidername' => 'Looksmart spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'MSNBot',

                        'spidername' => 'MSN spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'W3C_Validator',

                        'spidername' => 'W3C Validator',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Googlebot-Image',

                        'spidername' => 'Google-Image spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Googlebot',

                        'spidername' => 'Google spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'appie',

                        'spidername' => 'Walhello spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Mediapartners-Google',

                        'spidername' => 'Google AdSense spider',

                        'spider' => true,

                ),



                array (

                        'agent' => 'Scooter',

                        'spidername' => 'Altavista spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'FAST-WebCrawler',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Wget',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Ask Jeeves', 

                        'spider' => true,



                ),

                array (

                        'agent' => 'Speedy Spider',

                        'spider' => true,



                ),

                array (

                        'agent' => 'SurveyBot',

                        'spider' => true,



                ),

                array (

                        'agent' => 'IBM_Planetwide',

                       'spider' => true,

                ),

                array (

                        'agent' => 'GigaBot',

                        'spider' => true,

                ),

                array (

                        'agent' => 'ia_archiver',

                        'spider' => true,

                ),

                array (

                        'agent' => 'FAST-WebCrawler',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Yahoo! Slurp', 

                        'spidername' => 'Yahoo spider',

                        'spider' => true,

                ),

                array (

                        'agent' => 'Inktomi Slurp',

                        'spider' => true,

                ),

		array (

			'agent' => 'appie',

			'spidername' => 'Walhello spider',

		),

		array (

            'agent' => 'FeedBurner/1.0',

            'spidername' => 'Feedburner',

		'spider' => true,

        ),

        array (

            'agent' => 'Feedfetcher-Google',

'spidername' => 'Googlen Feedfetcher',

'spider' => true,

        ),

        array (

            'agent' => 'OmniExplorer_Bot/6.68',

            'spidername' => 'OmniExplorer Bot',

'spider' => true,

        ),

        array (

            'agent' => 'http://www.relevantnoise.com',

            'spidername' => 'relevantNOISE',

'spider' => true,

        ),

        array (

            'agent' => 'NewsGatorOnline/2.0',

            'spidername' => 'NewsGatorOnline',

'spider' => true,

        ),

        array (

            'agent' => 'ping.blo.gs/2.0',

'spider' => true,

        ),

        array (

            'agent' => 'Jakarta Commons-HttpClient/3.0.1',

            'spidername' => 'Amazon',

'spider' => true,

        ),

          array (

            'agent' => 'Jakarta Commons-HttpClient/3.0-rc2',

            'spidername' => 'Amazon',

'spider' => true,

        ),		

    //phones



                array (

                        'agent' => 'Nokia', 

                ),

                array (

                        'agent' => 'Samsung',

                ),

                array (

                        'agent' => 'Ericsson',

                ),

                  array (

                        'agent' => 'Siemens',

                ),

                 array (

                        'agent' => 'Motorola',

                ),

    //browsers

                array (

                        'agent' => 'Opera',

                 ),

                array (

                        'agent' => 'Firefox',

                ),

                array (

                        'agent' => 'Firebird',

                ),

                array (

                        'agent' => 'Safari', 

                ),

		 array (

			'agent' => 'Google Desktop',

		 ),

                array (

                        'agent' => 'Netscape', 

                ),

                array (

                        'agent' => 'MyIE2', 

                ),

                

                array (

                        'agent' => 'Konqueror', 

                ),

                array (

                        'agent' => 'Galeon', 

                ),

                array (

                        'agent' => 'KMeleon',

                ),



                array (

                        'agent' => 'NG/2.0',

                ),



                array (

                        'agent' => 'Gecko',

                        'name' => 'Mozilla',

                ),

                array (

                        'agent' => 'MSIE',

                          'name' => 'IE',



                ),

                

        );



foreach( $known_agents AS $poss )
		if (strpos(strtolower($user_agent), strtolower($poss['agent'])) !== false)
		{
			if ( $guest && isset($poss['spider']) && $poss['spider'] )
				$user_name = isset($poss['spidername']) ? $poss['spidername'] : (isset($poss['name']) ? $poss['name'] : $poss['agent']); 
			$result = isset($poss['name']) ? $poss['name'] : $poss['agent']; 
			return isset($poss['spider']) && $poss['spider'];
		}
	$result = $user_agent;
	return false;
}

?>





