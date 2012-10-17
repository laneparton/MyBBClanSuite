<?php
/**
 * 
 */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Create Team", "index.php?module=roster/addteam");

if($mybb->input['save']=="save")
{
	if(empty($mybb->input['name']) || empty($mybb->input['tag']) || empty($mybb->input['des']))
	{
		flash_message("One of the required fields was not correctly filled in", 'error');
		
		$title = $mybb->input['name'];
		$tag = $mybb->input['tag'];
		$image = $mybb->input['image'];
		$des = $mybb->input['des'];
	}
	else
	{
		flash_message("Your team has been created", 'success');
		
		$insert_array = array(
					"name"			=> addslashes($mybb->input['name']),
					"image"			=> addslashes($mybb->input['image']),
					"des"			=> $db->escape_string($mybb->input['des']),
					"tag"			=> addslashes($mybb->input['tag']),
				);
				
		$db->insert_query("rosterteams", $insert_array);
	}
}

// start the page
$page->output_header("Create Team");

$form = new Form("index.php?module=roster/addteam", "post", "", 1);

$form_container = new FormContainer("Create Team");
echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";

$form_container->output_row("Name", "The teams name", $form->generate_text_box('name', $name, array('id' => 'name')), 'name');
$form_container->output_row("Image", "The image that will appear on the roster page", $form->generate_text_box('image', $image, array('id' => 'image')), 'image');
$form_container->output_row("Description", "A brief description of the team", $form->generate_text_box('des', $des, array('id' => 'des')), 'des');
$form_container->output_row("Tag", "Abbreviation for the team (3 - 4 characters)", $form->generate_text_box('tag', $image, array('id' => 'tag')), 'tag');

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