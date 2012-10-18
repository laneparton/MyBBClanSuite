<?php
/**
 * 
 */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Manage Matches", "index.php?module=matches/manage");

if($mybb->input['action']=="" || $mybb->input['action']=="delete")
{
	if($mybb->input['action']=="delete")
	{	
		$id = $mybb->input['id'];
		$db->delete_query("matches","id='$id'");
	}
	
	// start the page
	$page->output_header("Match Manager");
	
	$table = new Table;
	$table->construct_header("Match Details");
	$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
	
	$query = $db->simple_select("matches", "*", "id + 0 <> 0", array("order_by" => 'date', "order_dir" => 'DESC'));

	while($item = $db->fetch_array($query))
	{
		$opponent_name = $item['opponent_name'];
		$team_id = $item['team_id'];
		$date = $item['date'];
		$id = $item['id'];
		
		$team_query = $db->simple_select("rosterteams", "*", "id = $team_id");
		
		while($team_item = $db->fetch_array($team_query))
		{
			$team_name = $team_item['name'];
		}	
		
		// create the "Edit/Delete" popup menu
		$popup = new PopupMenu("project_$id", $lang->options);
		
		// Add the items
		$popup->add_item("Edit", "index.php?module=matches/manage&amp;action=edit&amp;id=$id");
		$popup->add_item("Remove", "index.php?module=matches/manage&amp;action=delete&amp;id=$id");
		
		// create the info cell
		// construct_cell(content, array(html modifiers))
		$table->construct_cell("<a href=\"index.php?module=matches/manage&amp;action=edit&amp;id=$id\"><strong>".$opponent_name."</strong></a><br />vs ".$team_name." on ".$date);
		// create the menu cell
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		
		// output the row
		$table->construct_row();
	}
	
	// display the table with our title
	$table->output("Manage Match Reports");	
	
	// end the page
	$page->output_footer();	
}
else if($mybb->input['action']=="edit")
{
	$id = $mybb->input['id'];
		
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
			
			$team_id = $mybb->input['team_id'];
		
			$team_score = $mybb->input['team_score'];
			$opponent_score = $mybb->input['opponent_score'];
			$video = $mybb->input['video'];
			$report = $mybb->input['report'];
		}
		else
		{
			$date_day = $mybb->input['date_day'];
			$date_month = $mybb->input['date_month'];
			$date_year = $mybb->input['date_year'];		
		
			$date = $date_day."/".$date_month."/".$date_year;		
		
			$update_array = array(
					"opponent_name"			=> addslashes($mybb->input['opponent_name']),
					"opponent_website"		=> addslashes($mybb->input['opponent_website']),
					"opponent_score"		=> addslashes($mybb->input['opponent_score']),
					"team_id"				=> addslashes($mybb->input['team_id']),
					"team_score"			=> addslashes($mybb->input['team_score']),
					"competition"			=> addslashes($mybb->input['competition']),
					"date"					=> addslashes($date),
					"video"					=> addslashes($mybb->input['video']),
					"opponent_teamline"		=> addslashes($mybb->input['opponent_teamline']),
					"team_teamline"			=> addslashes($mybb->input['team_teamline']),					
					"report"				=> $db->escape_string($mybb->input['report'])
			);
			
			$db->update_query("matches", $update_array, "id='$id'");			
			
			flash_message("Your match has been updated", 'success');		
		}	
	}

	$page->add_breadcrumb_item("Edit Match", "index.php?module=news/matches&amp;action=edit&amp;id=$id");
	
	// start the page
	$page->output_header("Match Manager");
	
	$form = new Form("index.php?module=matches/manage&amp;action=edit&amp;id=$id", "post", "", 1);
	
	$query = $db->simple_select("matches", "*", "id = $id");
	$item = $db->fetch_array($query);
	
	// if the user tried to save, don't wipe all of the entered fields in case of error
	if($mybb->input['save']=="save")
	{
		$item['date_day'] = $mybb->input['date_day'];
		$item['date_month'] = $mybb->input['date_month'];
		$item['date_year'] = $mybb->input['date_year'];
	
		$item['opponent_name'] = $mybb->input['opponent_name'];
		$item['opponent_website'] = $mybb->input['opponent_website'];
		$item['competition'] = $mybb->input['competition'];
		
		$item['team_id'] = $mybb->input['team_id'];
		
		$item['team_teamline'] = $mybb->input['team_teamline'];
		$item['opponent_teamline'] = $mybb->input['opponent_teamline'];
		
		$item['team_score'] = $mybb->input['team_score'];
		$item['opponent_score'] = $mybb->input['opponent_score'];
		$item['video'] = $mybb->input['video'];
		$item['report'] = $mybb->input['report'];
	}
	
	// create a standard form container
	$form_container = new FormContainer("Edit Match");
	
$form_container->output_row("Opponent Name", "Your opponents clan name", $form->generate_text_box('opponent_name', $item['opponent_name'], array('id' => 'opponent_name')), 'opponent_name');

$form_container->output_row("Opponent Website", "Your opponents website", $form->generate_text_box('opponent_website', $item['opponent_website'], array('id' => 'opponent_website')), 'opponent_website');

$form_container->output_row("Competition", "Gamebattles, Decerto, Scrim, Tournament, etc", $form->generate_text_box('competition', $item['competition'], array('id' => 'competition')), 'competition');

$query = $db->simple_select("rosterteams", "*", "1=1");
while($team = $db->fetch_array($query))
{
	$teams[$team['id']] = $team['name'];
}

$form_container->output_row("Team", "The team this result applies to", $form->generate_select_box('team_id', $teams, $item['team_id'], array('id' => 'team')), 'team');

$form_container->output_row("Team Score", "The score of team", $form->generate_text_box('team_score', $item['team_score'], array('id' => 'team_score')), 'team_score');

$form_container->output_row("Opponent Score", "The score of opponent", $form->generate_text_box('opponent_score', $item['opponent_score'], array('id' => 'opponent_score')), 'opponent_score');

$form_container->output_row("Team's Line Up", "The squads line up", $form->generate_text_box('team_teamline', $item['team_teamline'], array('id' => 'team_teamline')), 'team_teamline');

$form_container->output_row("Opponent's Line Up", "The opponents line up", $form->generate_text_box('opponent_teamline', $item['opponent_teamline'], array('id' => 'opponent_teamline')), 'opponent_teamline');

$form_container->output_row("Date", "The date this match was played on", $form->generate_date_select('date'), 'date');

$form_container->output_row("Match Report", "A box that allows for you to explain what happened, post a video of the match, or post a screenshot. <br />HTML is <strong> Enabled </strong>", $form->generate_text_area('report', $item['report'], array('id' => 'report', 'rows' => '15', 'cols' => '55', 'style' => 'width: 100%;')), 'report');	
	
	// create the save flag
	echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";
	
	// end the container
	$form_container->end();
	
	// add the save button
	$buttons[] = $form->generate_submit_button("Save Changes");
	
	// display and end
	$form->output_submit_wrapper($buttons);
	$form->end();
		
	
	// end the page
	$page->output_footer();	
}

?>