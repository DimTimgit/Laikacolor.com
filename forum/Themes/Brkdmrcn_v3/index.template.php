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

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

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
<body>';
echo'
	<div class="ust"></div>
	<div class="orta"></div>

		';
echo'
<table class="header" width="100%" border="0">
  <tr><td>';

echo'
			<a href="', $scripturl, '"><img src="' . $settings['images_url'] . '/brkdmrcn/logo.png" align="left" alt="" /></a>
			</td>
			
			<td>
	<div style="float:right;
	margin: 8px 20px 0 0;
	font-size: x-small;
	font-family: verdana, sans-serif;">
	
	<form action="', $scripturl, '?action=search2" method="post">
							<input name="search" type="text" onfocus="if(this.value==\'', $txt[182], '\')value=\'\';" onblur="if(this.value==\'\')value=\'', $txt[182], '\';" value="', $txt[182], '" />
					<input type="submit" value="', $txt[182], '" />';
		
							// Search within current topic?
							if (!empty($context['current_topic']))
								echo '
								<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
						
								// If we're on a certain board, limit it to this board ;).
							elseif (!empty($context['current_board']))
								echo '
								<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
						
						echo '
						</form><br />';
	if($context['user']['is_logged'])
		        {

				
				echo'
				<b>', $context['user']['name'] , ', ', $txt['welcome_member'], '</b>';
		        
				// Only tell them about their messages if they can read their messages!
				if ($context['allow_pm'])
					echo '<br /> <a href="', $scripturl, '?action=pm">', $txt['member_personal_message'], '</a> ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'], '.';
				echo'
				<br />
				<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
					<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>
				
				';
		        }
				
		
		        else
		        {
				echo '
					<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>
						<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>           
								<input name="user" type="text" onfocus="if(this.value==\'', $txt[35], '\')value=\'\';" onblur="if(this.value==\'\')value=\'', $txt[35], '\';" value="', $txt[35], '" />
								<input name="passwrd" type="password" onfocus="if(this.value==\'', $txt[36], '\')value=\'\';" onblur="if(this.value==\'\')value=\'', $txt[36], '\';" value="', $txt[36], '" />
								<input type="submit" value="', $txt[34], '" />
								<input type="hidden" name="cookielength" value="-1" />
						</form> 	';
		        }

				  echo'
				  </div>
			</td>
			</tr>
			</table>
			
	';
	
	echo'  ', template_menu(), '  ';
      


	// The main content should go here.
	echo '
	<div id="bodyarea" style="padding: 1ex 0px 2ex 0px;">';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	</div>
';


// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

	<div class="alt"></div>


	<div id="footerarea" style="text-align: center; padding-bottom: 1ex;', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? ' width: 100%;' : '', '">

		<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%">
			<tr>
				<td class="cp" width="50%" valign="middle" align="left">
					', theme_copyright(), '<br /><font color="#a61e1e">Brkdmrcn v3 </font><a href="http://www.smfciyiz.biz">Brkdmrcn</a>				
				</td>
		
				<td width="50%" valign="middle" align="right">
					<a href="http://www.mysql.com/" target="_blank"><img src="', $settings['images_url'], '/mysql.gif" alt="', $txt['powered_by_mysql'], '" width="40" height="20" /></a>
					<a href="http://www.php.net/" target="_blank"><img src="', $settings['images_url'], '/php.gif" alt="', $txt['powered_by_php'], '" width="40" height="20" /></a>
					<a href="http://validator.w3.org/check/referer" target="_blank"><img src="', $settings['images_url'], '/xhtml.gif" alt="', $txt['valid_xhtml'], '" width="40" height="20" /></a>
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img src="', $settings['images_url'], '/css.gif" alt="', $txt['valid_css'], '" width="40" height="20" /></a>
				</td>
			</tr>
		</table>';
	
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

	echo '
	</div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree (vB Clone).  This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{

	return;

}

// Show a linktree (vB Clone).  This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree1()
{
        global $context, $settings, $options;

        // Folder style or inline?  Inline has a smaller font.
        echo '<div class="navbar" style="font-size:10pt">';
		
		echo '<img class="inlineimg" src="' . $settings['images_url'] . '/icons/folder_open.gif" alt="+" border="0" />&nbsp;';

        // Each tree item has a URL and name.  Some may have extra_before and extra_after.
        foreach ($context['linktree'] as $link_num => $tree)
        {
                // Show something before the link?
                if (isset($tree['extra_before']))
                        echo $tree['extra_before'];

                if (($link_num == count($context['linktree']) - 1) && count($context['linktree'])>1)
                        echo '<br />
						<img class="inlineimg" src="' . $settings['images_url'] . '/icons/linktree_side.gif" alt="" border="0" />&nbsp;';

                // Show the link, including a URL if it should have one.
                echo '<b>', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'], '</b>';

                // Show something after the link...?
                if (isset($tree['extra_after']))
                        echo $tree['extra_after'];

                // Don't show a separator for the last one.
                if ($link_num < count($context['linktree']) - 2)
                        echo '&nbsp;&raquo;&nbsp;';

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
// Show the start of the tab section.
echo '

<div class="indentmenu">
<ul>


';

// Show the default [home] button.
echo '<li><a href="', $scripturl, '"><b>' , $txt[103] , '</b></a></li>';

// Show the [help] button.
echo '<li><a href="', $scripturl, '?action=help"><b>' , $txt[119] , '</b></a></li>';

// How about the [search] button?
if ($context['allow_search'])
echo '<li><a href="', $scripturl, '?action=search"><b>' , $txt[182] , '</b></a></li>';

// Is the user allowed to administrate at all? ([admin])
if ($context['allow_admin'])
echo '<li><a href="', $scripturl, '?action=admin"><b>' , $txt[2] , '</b></a></li>';

// Edit Profile... [profile]
if ($context['allow_edit_profile'])
echo '<li><a href="', $scripturl, '?action=profile"><b>' , $txt[79] , '</b></a></li>';

// Go to PM center... [pm]
if ($context['user']['is_logged'] && $context['allow_pm'])
echo '<li><a href="', $scripturl, '?action=pm"><b>' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</b></a></li>';


// The [calendar]!
if ($context['allow_calendar'])
echo '<li><a href="', $scripturl, '?action=calendar"><b>' , $txt['calendar24'] , '</b></a></li>';

// the [member] list button
if ($context['allow_memberlist'])
echo '<li><a href="', $scripturl, '?action=mlist"><b>' , $txt[331] , '</b></a></li>';


// If the user is a guest, show [login] button.
if ($context['user']['is_guest'])
echo '<li><a href="', $scripturl, '?action=login"><b>' , $txt[34] , '</b></a></li>';


// If the user is a guest, also show [register] button.
if ($context['user']['is_guest'])
echo '<li><a href="', $scripturl, '?action=register"><b>' , $txt[97] , '</b></a></li>';


// Otherwise, they might want to [logout]...
if ($context['user']['is_logged'])
echo '<li><a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '"><b>' , $txt[108] , '</b></a></li>';
echo '</ul></div>'; 

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
