<?php
/*
This file is part of the MyBBClanSuite.

    The MyBBClanSuite is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    The MyBBClanSuite is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with The MyBBClanSuite.  If not, see <http://www.gnu.org/licenses/>.
	*/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Add Player", "index.php?module=roster/addplayer");

if($mybb->input['save']=="save")
{
	if(empty($mybb->input['username']))
	{
		flash_message("One of the required fields was not correctly filled in", 'error');
		
		$username = $mybb->input['username'];
		$team = $mybb->input['team'];
		$position = $mybb->input['position'];
	}
	else
	{
		$query 	= $db->simple_select("users", "uid", "username = '".$mybb->input['username']."'");
		$num	= $db->num_rows($query);
		
		if($num < 1)
		{
			flash_message("Player added to the team, however he/she is not a forum member.", 'success');
		
			$insert_array = array(
					"uid"			=> null,
					"uname"			=> addslashes($mybb->input['username']),
					"team"			=> addslashes($mybb->input['team']),
					"position"		=> addslashes($mybb->input['position'])
				);
				
			$db->insert_query("rostermembers", $insert_array);
		}
		else
		{
			while($user = $db->fetch_array($query))
			{
				$userid = $user['uid'];
			}
			
			flash_message("Player added to team", 'success');
		
			$insert_array = array(
					"uid"			=> $userid,
					"uname"			=> addslashes($mybb->input['username']),
					"team"			=> addslashes($mybb->input['team']),
					"position"		=> addslashes($mybb->input['position'])
				);
				
			$db->insert_query("rostermembers", $insert_array);
		}
	}
}

// start the page
$page->output_header("Add Player");

$form = new Form("index.php?module=roster/addplayer", "post", "", 1);

$form_container = new FormContainer("Add Player");
echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";

$form_container->output_row("Username", "The username of the user you want to add to the team", $form->generate_text_box('username', $username, array('id' => 'username')), 'username');

$query = $db->simple_select("rosterteams", "*", "1=1");
while($team = $db->fetch_array($query))
{
	$teams[$team['id']] = $team['name'];
}

$form_container->output_row("Team", "Team that the user will be added to", $form->generate_select_box('team', $teams, $team, array('id' => 'team')), 'team');

$form_container->output_row("Position", "What position is this user", $form->generate_text_box('position', $position, array('id' => 'position')), 'position');

// close the form container
$form_container->end();

// create the save button
$buttons[] = $form->generate_submit_button("Save");

// wrap up the form
$form->output_submit_wrapper($buttons);
$form->end();

// end the page
$page->output_footer();

?>