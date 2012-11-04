<?php
/**
 * 
 */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Add Server", "index.php?module=servers/addserver");

if($mybb->input['save']=="save")
{
	if(empty($mybb->input['name']) || empty($mybb->input['image']))
	{
		flash_message("One of the required fields was not correctly filled in", 'error');
		
		$title = $mybb->input['name'];
		$image = $mybb->input['image'];
	}
	else
	{
		flash_message("Your server has been added", 'success');
		
		$insert_array = array(
					"name"			=> addslashes($mybb->input['name']),
					"image"			=> addslashes($mybb->input['image']),
				);
				
		$db->insert_query("servers", $insert_array);
	}
}

// start the page
$page->output_header("Add Server");

$form = new Form("index.php?module=servers/addserver", "post", "", 1);

$form_container = new FormContainer("Add Server");
echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";

$form_container->output_row("Name", "The Server Name/Nickname(Most will not see this)", $form->generate_text_box('name', $name, array('id' => 'name')), 'name');
$form_container->output_row("Image", "This is designed for HTML code to support server monitoring. If you just have an image, use HTML to display it.", $form->generate_text_box('image', $image, array('id' => 'image')), 'image');

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