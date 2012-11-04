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

$page->add_breadcrumb_item("Add Match Report", "index.php?module=matches/addnew");

if($mybb->input['save']=="save")
{
	if(empty($mybb->input['opponent_name']) || empty($mybb->input['competition']))
	{
		flash_message("One of the required fields was not correctly filled in", 'error');
		
		$date_day = $mybb->input['date_day'];
		$date_month = $mybb->input['date_month'];
		$date_year = $mybb->input['date_year'];
	
		$opponent_name = $mybb->input['opponent_name'];
		$opponent_website = $mybb->input['opponent_website'];
		$competition = $mybb->input['competition'];
		
		$opponent_teamline = $mybb->input['opponent_teamline'];
		$team_teamline = $mybb->input['team_teamline'];
		
		$team_score = $mybb->input['team_score'];
		$opponent_score = $mybb->input['opponent_score'];
		
		$report = $mybb->input['report'];
	}
	else
	{
		flash_message("The match has been added to the matches page", 'success');
		
		$date_day = $mybb->input['date_day'];
		$date_month = $mybb->input['date_month'];
		$date_year = $mybb->input['date_year'];		
		
		$date = $date_day."/".$date_month."/".$date_year;
		
		$insert_array = array(
					"opponent_name"			=> addslashes($mybb->input['opponent_name']),
					"opponent_website"		=> addslashes($mybb->input['opponent_website']),
					"opponent_score"		=> addslashes($mybb->input['opponent_score']),
					"team_id"				=> addslashes($mybb->input['team_id']),
					"team_score"			=> addslashes($mybb->input['team_score']),
					"competition"			=> addslashes($mybb->input['competition']),
					"date"					=> addslashes($date),
					"opponent_teamline"		=> addslashes($mybb->input['opponent_teamline']),
					"team_teamline"			=> addslashes($mybb->input['team_teamline']),
					"report"				=> $db->escape_string($mybb->input['report'])
				);
				
		$db->insert_query("matches", $insert_array);
	}
}

// start the page
$page->output_header("Match Manager");

$form = new Form("index.php?module=matches/addnew", "post", "", 1);

$form_container = new FormContainer("Create Match Report");
echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";

$form_container->output_row("Opponent Name", "Your opponents clan name", $form->generate_text_box('opponent_name', $opponent_name, array('id' => 'opponent_name')), 'opponent_name');
$form_container->output_row("Opponent Website", "Your opponents website", $form->generate_text_box('opponent_website', $opponent_website, array('id' => 'opponent_website')), 'opponent_website');
$form_container->output_row("Competition", "Gamebattles, Decerto, Scrim, Tournament, etc", $form->generate_text_box('competition', $competition, array('id' => 'competition')), 'competition');

$query = $db->simple_select("rosterteams", "*", "1=1");
while($team = $db->fetch_array($query))
{
	$teams[$team['id']] = $team['name'];
}

$form_container->output_row("Team", "The team this result applies to", $form->generate_select_box('team_id', $teams, $team_id, array('id' => 'team')), 'team');

$form_container->output_row("Team Score", "The score of team", $form->generate_text_box('team_score', $team_score, array('id' => 'team_score')), 'team_score');

$form_container->output_row("Opponent Score", "The score of opponent", $form->generate_text_box('opponent_score', $opponent_score, array('id' => 'opponent_score')), 'opponent_score');

$form_container->output_row("Team Line Up", "Your team line up", $form->generate_text_box('team_teamline', $team_teamline, array('id' => 'team_teamline')), 'team_teamline');

$form_container->output_row("Opponent's Line Up", "The opponents line up", $form->generate_text_box('opponent_teamline', $opponent_teamline, array('id' => 'opponent_teamline')), 'opponent_teamline');

$form_container->output_row("Date", "The date this match was played on", $form->generate_date_select('date'), 'date');


$form_container->output_row("Match Report", "A box that allows for you to explain what happened, post a video of the match, or post a screenshot. <br />HTML is <strong> Enabled </strong>", $form->generate_text_area('report', $report, array('id' => 'report', 'rows' => '15', 'cols' => '55', 'style' => 'width: 100%;')), 'report');

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