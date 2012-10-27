<?php

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'roster.php');

$templatelist 	= "roster_index";
require_once "./global.php";
add_breadcrumb("Roster", "roster.php");

$teams_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "rosterteams");
$teams_num		= $db->num_rows($teams_query);

if ($teams_num > 0)
{
	while ($teams_array = $db->fetch_array($teams_query))
	{
		$team_id	= $teams_array['id'];
		$team_name	= $teams_array['name'];
		$team_image	= $teams_array['image'];
		$team_des	= $teams_array['des'];
		
		$members_query	= $db->query("SELECT * FROM " . TABLE_PREFIX . "rostermembers WHERE team = $team_id");
		$members_num	= $db->num_rows($members_query);
		
		if	($members_num > 0)
		{
			while ($members_array = $db->fetch_array($members_query))
			{
				$members_uid	= $members_array['uid'];
				$members_uname	= $members_array['uname'];
				$members_pos	= $members_array['position'];
				
				//If the username is registered, lets give it the appropriate details
				if	($members_uid != 0)
				{
					$user_query 	= $db->query("SELECT username, avatar FROM " . TABLE_PREFIX . "users WHERE uid = " . $members_uid ."");
							
					while ($user_data = $db->fetch_array($user_query))
					{
						$user_name 		= $user_data['username'];
						$user_avatar 	= $user_data['avatar'];
					}
				}
				//If not, let's give it the name stored in rostermembers
				else
				{
					$user_name	=	$members_uname;
				}
				
				eval("\$members_bit .= \"".$templates->get("roster_user")."\";");
			}
		}
		else
		{
			eval("\$members_bit .= \"".$templates->get("roster_user_none")."\";");
		}
		
		if ($team_image != null)
		{
			eval("\$teams_bit .= \"".$templates->get("roster_team")."\";");
		}
		else
		{
			eval("\$teams_bit .= \"".$templates->get("roster_team_imgnull")."\";");
		}
		
		$members_bit = "";
	}
}
else
{
	eval("\$teams_bit = \"".$templates->get("roster_team_none")."\";");
}

eval("\$roster = \"".$templates->get("roster_index")."\";");
output_page($roster);

?>