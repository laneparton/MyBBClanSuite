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

$page->add_breadcrumb_item("Manage Teams", "index.php?module=roster/manage");

if($mybb->input['action']=="" || $mybb->input['action']=="delete" || $mybb->input['action']=="deleteuser")
{
	if($mybb->input['action']=="delete")
	{	
		$id = $mybb->input['id'];
		$db->delete_query("rosterteams","id='$id'");
		$db->delete_query("rostermembers","team='$id'");
		$db->delete_query("matches","team_id='$id'");
	}
	
	if($mybb->input['action']=="deleteuser")
	{	
		$id = $mybb->input['id'];
		$db->delete_query("rostermembers","id='$id'");
	}	
	
	// start the page
	$page->output_header("Team Management");

	$query 	= $db->simple_select("rosterteams", "*");
	while($teams = $db->fetch_array($query))
	{
		$table = new Table;
		$table->construct_header("Members");
		$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
		
		$players = $db->simple_select("rostermembers", "*", "team = ".$teams['id']);
		while($item = $db->fetch_array($players))
		{	
			// create the "Edit/Delete" popup menu
			$popup = new PopupMenu("project_".$item['id'], $lang->options);
			
			// Add the items
			$popup->add_item("Remove", "index.php?module=roster/manage&amp;action=deleteuser&amp;id=".$item['id']);
		
			//Since we have no reason to pull the username's details right now, let's just load it from the rostermembers table.
			$username = $item['uname'];
				
			// create the info cell
			// construct_cell(content, array(html modifiers))
			$table->construct_cell($username);
			// create the menu cell
			$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		
			// output the row
			$table->construct_row();
		}
		
		// display the table with our title
		$table->output($teams['name']);	
		
		print "<a href=index.php?module=roster/manage&amp;action=delete&amp;id=".$teams['id'].">Delete ".$teams['name']."</a> | <a href=index.php?module=roster/manage&amp;action=edit&amp;id=".$teams['id'].">Edit ".$teams['name']."</a><br /><br />";
	}	
	
	// end the page
	$page->output_footer();	
}
else if($mybb->input['action']=="edit")
{
	$id = $mybb->input['id'];
		
	if($mybb->input['save']=="save")
	{
		if(empty($mybb->input['name']) || empty($mybb->input['image']) || empty($mybb->input['des']) || empty($mybb->input['tag']))
		{
			flash_message("One of the required fields was not correctly filled in", 'error');
		
			$item['name'] = $mybb->input['name'];
			$item['image'] = $mybb->input['image'];
			$item['des'] = $mybb->input['des'];
			$item['tag'] = $mybb->input['tag'];
		}
		else
		{
			$update_array = array(
					"name"			=> addslashes($mybb->input['name']),
					"image"			=> addslashes($mybb->input['image']),
					"des"			=> addslashes($mybb->input['des']),
					"tag"			=> addslashes($mybb->input['tag'])
			);
			
			$db->update_query("rosterteams", $update_array, "id='$id'");			
			
			flash_message("Your team has been updated", 'success');		
		}	
	}

	$page->add_breadcrumb_item("Edit Team", "index.php?module=roster/manage&amp;action=edit&amp;id=$id");
	
	// start the page
	$page->output_header("Edit Team Details");
	
	$form = new Form("index.php?module=roster/manage&amp;action=edit&amp;id=$id", "post", "", 1);
	
	$query = $db->simple_select("rosterteams", "*", "id = $id");
	$item = $db->fetch_array($query);
	
	// if the user tried to save, don't wipe all of the entered fields in case of error
	if($mybb->input['save']=="save")
	{
		$item['name'] = $mybb->input['name'];
		$item['image'] = $mybb->input['image'];
		$item['des'] = $mybb->input['des'];
		$item['tag'] = $mybb->input['tag'];
	}
	
	// create a standard form container
	$form_container = new FormContainer("Edit Team");
	
	$form_container->output_row("Name", "The teams name", $form->generate_text_box('name', $item['name'], array('id' => 'name')), 'name');
	$form_container->output_row("Image", "The image that will appear on the roster page", $form->generate_text_box('image', $item['image'], array('id' => 'image')), 'image');
	$form_container->output_row("Description", "A brief description of the team", $form->generate_text_box('des', $item['des'], array('id' => 'des')), 'des');
	$form_container->output_row("Tag", "Abbreviation for the team (3 - 4 characters)", $form->generate_text_box('tag', $item['tag'], array('id' => 'tag')), 'tag');

	
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