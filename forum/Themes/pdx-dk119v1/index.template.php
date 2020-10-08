<?php
// Version: 1.1.5; index

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;
	
	if(!$context['user']['is_guest'] && isset($_POST['options']['theme_color']))
	{
   	include_once($GLOBALS['sourcedir'] . '/Profile.php');
   	makeThemeChanges($context['user']['id'], $settings['theme_id']);
   	$options['theme_color'] = $_POST['options']['theme_color'];
	}

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
	$settings['theme_version'] = '1.1.5';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
}

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $forum_version, $language, $modSettings, $colorpath, $buttonpath;

	$csect_cookie = 'SMF_user_' . $context['user']['id'] . '_CSect';
	
	if(isset($_COOKIE[$csect_cookie]) && !empty($_COOKIE[$csect_cookie]))
	   $settings['collapsed_sects'] = explode("\n", $_COOKIE[$csect_cookie]);
		$settings['csect_cookie'] = $csect_cookie;

	if (isset($options['theme_color']))
   	$mycolor = $options['theme_color'];
	else
	{
   	// Defaults.
   	$options['theme_color'] = isset($settings['theme_default_color']) ? $settings['theme_default_color'] : 'default';
   	$mycolor=$options['theme_color'];
	}
	if(isset($settings['allow_color_change']) && $settings['allow_color_change'] == 'no')
	{
   	// Set user back to default theme if "personal theme option" is disabled
   	$options['theme_color'] = isset($settings['theme_default_color']) ? $settings['theme_default_color'] : 'default';
   	$mycolor=$options['theme_color'];
	}
	
	//Set path for the color related images
	if ($mycolor == 'default' || $mycolor == 'blue_dark' || $mycolor == 'green_dark' || $mycolor == 'red_dark')
	{
		$colorpath = "dark";
		$buttonpath = "/";
	}
	else if ($mycolor == 'yellow_light' || $mycolor == 'blue_light' || $mycolor == 'green_light' || $mycolor == 'red_light')
	{
		$colorpath = "light";
		$buttonpath = "/light/";
	}
	
	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?fin11"></script>
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";
	// ]]></script>
	<title>', $context['page_title'], '</title>';

	// The ?fin11 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/color_css/'.$mycolor.'.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />';

	/* Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are big...)
		Thus, in Internet Explorer 4, 5, and Opera 6 this will show fonts one size smaller than usual.
		Note that this is affected by whether IE 6 is in standards compliance mode.. if not, it will also be big.
		Standards compliance mode happens when you use xhtml... */
	if ($context['browser']['needs_size_fix'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/fonts-compat.css" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" target="_blank" />
	<link rel="search" href="' . $scripturl . '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name'], ' - RSS" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="' . $scripturl . '?board=' . $context['current_board'] . '.0" />';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
	{
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);
		$options['collapse_header_ic'] = !empty($_COOKIE['upshrinkIC']);
	}

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'], '

	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var current_header = ', empty($options['collapse_header']) ? 'false' : 'true', ';

		function shrinkHeader(mode)
		{';

	// Guests don't have theme options!!
	if ($context['user']['is_guest'])
		echo '
			document.cookie = "upshrink=" + (mode ? 1 : 0);';
	else
		echo '
			smf_setThemeOption("collapse_header", mode ? 1 : 0, null, "', $context['session_id'], '");';

	echo '
			document.getElementById("upshrink").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

			document.getElementById("upshrinkHeader").style.display = mode ? "none" : "";
			document.getElementById("upshrinkHeader2").style.display = mode ? "none" : "";

			current_header = mode;
		}
	// ]]></script>';

	// the routine for the info center upshrink
	echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			var current_header_ic = ', empty($options['collapse_header_ic']) ? 'false' : 'true', ';

			function shrinkHeaderIC(mode)
			{';

	if ($context['user']['is_guest'])
		echo '
				document.cookie = "upshrinkIC=" + (mode ? 1 : 0);';
	else
		echo '
				smf_setThemeOption("collapse_header_ic", mode ? 1 : 0, null, "', $context['session_id'], '");';

	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
</head>
<body>
	<div id="wrapper">
	<div id="page_bg">
		<div id="header">';
			if (empty($settings['header_logo_url']))
				echo '
					<div id="logo">&nbsp;</div>
						<div style="font-family: Georgia, sans-serif; font-size: 30px; padding: 5px 10px 12px 10px; white-space: nowrap; float: left;">', $context['forum_name'], '</div>';
				else
					echo '
						<div style="float: left;"><img src="', $settings['header_logo_url'], '" alt="', $context['forum_name'], '" border="0" /></div>';
				echo '
				<div id="header_r"> ';
				if (!empty($context['user']['avatar']))
				{
					if(empty($settings['top_avatar_resize']))
					{
						$context['user']['avatar']['image'] = strtr($context['user']['avatar']['image'], array("class=\"avatar\"" => "class=\"avatar_t\""));
					}
					echo '<div style="padding: 0 10px 0 10px; 	float: left;">', $context['user']['avatar']['image'], '<div class="avatar_frame"></div></div>';
				}	
				// If the user is logged in, display stuff like their name, new messages, etc.
				if ($context['user']['is_logged'])
				{
					echo '
					<div class="usercenter">
						', $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';

					// Only tell them about their messages if they can read their messages!
					if ($context['allow_pm'])
					echo ', 
					', $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
					echo '.<br />';

					// Is the forum in maintenance mode?
					if ($context['in_maintenance'] && $context['user']['is_admin'])
					echo '
					<b>', $txt[616], '</b><br />';

					// Are there any members waiting for approval?
					if (!empty($context['unapproved_members']))
					echo '
					', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

					// Show the total time logged in?
					if (!empty($context['user']['total_time_logged_in']))
					{
					echo '
							', $txt['totalTimeLogged1'];

					// If days is just zero, don't bother to show it.
					if ($context['user']['total_time_logged_in']['days'] > 0)
						echo $context['user']['total_time_logged_in']['days'] . $txt['totalTimeLogged2'];

					// Same with hours - only show it if it's above zero.
					if ($context['user']['total_time_logged_in']['hours'] > 0)
						echo $context['user']['total_time_logged_in']['hours'] . $txt['totalTimeLogged3'];

					// But, let's always show minutes - Time wasted here: 0 minutes ;).
					echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'], '<br />';
				}

				echo '
							<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
							<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />
				</div>';
							
			}
			// Otherwise they're a guest - so politely ask them to register or login.
			else
			{
				echo '				
							', $txt['welcome_guest'], '<br />
							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>

							<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 3px 1ex 1px 0; text-align: left;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
								<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
								<select name="cookielength">
									<option value="60">', $txt['smf53'], '</option>
									<option value="1440">', $txt['smf47'], '</option>
									<option value="10080">', $txt['smf48'], '</option>
									<option value="43200">', $txt['smf49'], '</option>
									<option value="-1" selected="selected">', $txt['smf50'], '</option>
								</select>
								<input type="submit" value="', $txt[34], '" /><br />
								', $txt['smf52'], '
								<input type="hidden" name="hash_passwrd" value="" />
							</form>';
			}
			echo '
		</div>
	</div>';
	
// clear floating
echo'
<div class="clr"></div>';
		
		// Show the menu here, according to the menu sub template.
		echo'
		<div id="menu">';
			template_menu();
	 	echo '
		</div>';
		
		// News bar
		echo'
		<div id="newsbar">';

			// Show a random news item? (or you could pick one from news_lines...)
			if (!empty($settings['enable_news']))
			{
				echo '
				<div id="currenttime">', $context['current_time'] , '</div>
				<div id="news"><strong>News</strong>: ', $context['random_news_line'], ' </div>';
			}
			else 
			{
				echo '<div class="currenttime">', $context['current_time'] , '</div>';
			}
		echo '
		</div>
<div class="clr"></div>';

// The main content should go here.
echo'
<div id="content">';
	if ($context['user']['is_admin'])
	{
		if (empty($txt['necessary_to_translate']))
		{
			echo '
			<div style="margin: 0 2ex 2ex 2ex; padding: 2ex; border: 2px dashed #cc3344; color: black; background-color: #e7e7f7;">
				<div style="float: left; width: 2ex; font-size: 2em; color: red;">!!</div>
				<b style="text-decoration: underline;">Attention of pdx-dk theme:</b><br />
				<div style="padding-left: 6ex;">
					You have to create &quot;<b>Modifications.<span style="color: red;"><acronym title="Name of language that you are using">' , $language , '</acronym></span>.php</b>&quot; in the &quot;<b>languages</b>&quot; directory ( <i>', $settings['theme_url'], '/languages/</i> ) of your pdx-dk theme.<br /><br />
		Duplicate the &quot;<i>Modifications.english.php</i>&quot; of the &quot;languages&quot; directory of pdx-dk. Next, rename it according to the language for which you use the file. 
		</div>
			</div>';
		}
	}
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

echo '
</div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			function smfFooterHighlight(element, value)
			{
				element.src = smf_images_url + "/" + (value ? "h_" : "") + element.id + ".gif";
			}
		// ]]></script>
	<div id="footer">
		<div id="footer_l">', theme_copyright(), '</div>
		<div id="footer_r">
			<a href="http://themes.simplemachines.org/" title="theme title" target="_blank">PDX-DK</a> Theme by <a href="http://padexx.de/" title="Author of the theme" target="_blank">padexx</a> |
			<a href="http://www.mysql.com/" target="_blank" title="', $txt['powered_by_mysql'], '">mySQL</a> |
			<a href="http://www.php.net/" target="_blank" title="', $txt['powered_by_php'], '">PHP</a> |
			<a href="http://validator.w3.org/check/referer" target="_blank" title="', $txt['valid_xhtml'], '">xhtml</a> |
			<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank" title="', $txt['valid_css'], '">CSS</a>
		</div>
	</div>
</div>
</div>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
		<span class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</span>';

	// This is an interesting bug in Internet Explorer AND Safari. Rather annoying, it makes overflows just not tall enough.
	if (($context['browser']['is_ie'] && !$context['browser']['is_ie4']) || $context['browser']['is_mac_ie'] || $context['browser']['is_safari'] || $context['browser']['is_firefox'])
	{
		// The purpose of this code is to fix the height of overflow: auto div blocks, because IE can't figure it out for itself.
		echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

		// Unfortunately, Safari does not have a "getComputedStyle" implementation yet, so we have to just do it to code...
		if ($context['browser']['is_safari'])
			echo '
			window.addEventListener("load", smf_codeFix, false);

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if ((codeFix[i].className == "code" || codeFix[i].className == "post" || codeFix[i].className == "signature") && codeFix[i].offsetHeight < 20)
						codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + "px";
				}
			}';
		elseif ($context['browser']['is_firefox'])
			echo '
			window.addEventListener("load", smf_codeFix, false);
			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if (codeFix[i].className == "code" && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
						codeFix[i].style.overflow = "scroll";
				}
			}';			
		else
			echo '
			var window_oldOnload = window.onload;
			window.onload = smf_codeFix;

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = codeFix.length - 1; i > 0; i--)
				{
					if (codeFix[i].currentStyle.overflow == "auto" && (codeFix[i].currentStyle.height == "" || codeFix[i].currentStyle.height == "auto") && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0 || codeFix[i].className == "code"))
						codeFix[i].style.height = (codeFix[i].offsetHeight + 36) + "px";
				}

				if (window_oldOnload)
				{
					window_oldOnload();
					window_oldOnload = null;
				}
			}';

		echo '
		// ]]></script>';
	}

	// The following will be used to let the user know that some AJAX process is running
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options, $scripturl, $txt;

// Do not display the linktree on search page
if (!$context['current_action'] == 'search2')
{
echo'
<div class="top_area">
	<div style="width: 200px; float: right;">
		<a href="', $scripturl, '?action=search;advanced" class="searchb" title="', $txt['smf298'], '"></a>
		<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
			<input type="text" name="search" value="', $txt[182], '..." onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\'', $txt[182], '...\';" style="width: 150px;" /> ';
	
			// Search within current topic?
			if (!empty($context['current_topic']))
				echo '
				<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

			// If we're on a certain board, limit it to this board ;).
			elseif (!empty($context['current_board']))
				echo '
				<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
		echo '
		</form>
	</div>';
		
	// Folder style or inline?  Inline has a smaller font.
	echo '<div style="padding-bottom: 10px;" class="nav"', $settings['linktree_inline'] ? ' style="font-size: smaller;"' : '', '>';

	// Each tree item has a URL and name.  Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
	// Show the | | |-[] Folders.
		if (!$settings['linktree_inline'])
		{
			if ($link_num > 0)
				echo str_repeat('<img src="' . $settings['images_url'] . '/icons/linktree_main.gif" alt="| " border="0" />', $link_num - 1), '<img src="' . $settings['images_url'] . '/icons/linktree_side.gif" alt="|-" border="0" />';
			echo '<img src="' . $settings['images_url'] . '/icons/folder_open.gif" alt="+" border="0" />&nbsp; ';
		}

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
			echo $settings['linktree_inline'] ? ' &nbsp;|&nbsp; ' : '<br />';
	}

	echo'
	</div>
</div>';
}
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

// Start of the tab section.
	echo ' <div class="seperator"><!--no content--></div>';
// Show the [home] button.
	if (!empty($settings['pdx_forum_button']))
	echo '
		<div class="maintab_back">
		<a href="', $settings['pdx_forum_button'], '">' , $txt[103] , '</a>
		</div>
	<div class="seperator"><!--no content--></div>';

	if (!empty($settings['pdx_forum_button']))
	{
// Show the [home] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt['pdx_forum_button_forum'] , '</a>
				</div>' , $current_action == 'home' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';
	}
	else
	{
// Show the [forum] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt[103] , '</a>
				</div>' , $current_action == 'home' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

	}

// Show the [help] button.
	echo ($current_action == 'help' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'help' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=help">' , $txt[119] , '</a>
				</div>' , $current_action == 'help' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// How about the [search] button?
	if ($context['allow_search'])
		echo ($current_action == 'search' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'search' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=search">' , $txt[182] , '</a>
				</div>' , $current_action == 'search' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo ($current_action == 'admin' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'admin' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=admin">' , $txt[2] , '</a>
				</div>' , $current_action == 'admin' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo ($current_action == 'profile' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'profile' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=profile">' , $txt[79] , '</a>
				</div>' , $current_action == 'profile' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo ($current_action == 'pm' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'pm' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</div>' , $current_action == 'pm' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// The [calendar]!
	if ($context['allow_calendar'])
		echo ($current_action == 'calendar' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'calendar' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a>
				</div>' , $current_action == 'calendar' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// the [member] list button
	if ($context['allow_memberlist'])
		echo ($current_action == 'mlist' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'mlist' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt[331] , '</a>
				</div>' , $current_action == 'mlist' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';


// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'login' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'login' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=login">' , $txt[34] , '</a>
				</div>' , $current_action == 'login' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';


// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'register' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'register' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=register">' , $txt[97] , '</a>
				</div>' , $current_action == 'register' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';


// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo ($current_action == 'logout' || $context['browser']['is_ie4']) ? '' : '' , '
				<div class="maintab_' , $current_action == 'logout' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt[108] , '</a>
				</div>' , $current_action == 'logout' ? '<div class="seperator"><!--no content--></div>' : '<div class="seperator"><!--no content--></div>';

// The end of tab section.
}

// Generate a strip of buttons, out of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl, $buttonpath;

	if (empty($button_strip))
		return '';

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '' . $buttonpath . '' . ($value['lang'] ? $context['user']['language'] . '/' : '') . $value['image'] . '" alt="' . $txt[$value['text']] . '" border="0" />' : $txt[$value['text']]) . '</a>';

		$button_strip[$key] = $buttons[$key];
	}

	echo '
		<td ', $custom_td, '>', implode($context['menu_separator'], $button_strip) , '</td>';
}

?>