<?php
/**
 * 
 */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Manage Servers", "index.php?module=servers/manage");

if($mybb->input['action']=="" || $mybb->input['action']=="delete")
{
	if($mybb->input['action']=="delete")
	{	
		$id = $mybb->input['id'];
		$db->delete_query("servers","id='$id'");
	}
	
	// start the page
	$page->output_header("Server Management");
	$table = new Table;
	$table->construct_header("Server");
	$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
	$servers_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "servers");
	while($item = $db->fetch_array($servers_query))
	{
		$server = $item['name'];

			// create the "Edit/Delete" popup menu
			$popup = new PopupMenu("project_".$item['id'], $lang->options);
			
			// Add the items
			$popup->add_item("Edit", "index.php?module=servers/manage&amp;action=edit&amp;id=".$item['id']);
			$popup->add_item("Remove", "index.php?module=servers/manage&amp;action=delete&amp;id=".$item['id']);
		
			// create the info cell
			// construct_cell(content, array(html modifiers))
			$table->construct_cell($server);
			// create the menu cell
			$table->construct_cell($popup->fetch(), array("class" => "align_center"));
			// output the row
			$table->construct_row();	
	}	
	
	$table->output("Servers");
	
	// end the page
	$page->output_footer();	
}
else if($mybb->input['action']=="edit")
{
	$id = $mybb->input['id'];
		
	if($mybb->input['save']=="save")
	{
		if(empty($mybb->input['name']) || empty($mybb->input['image']))
		{
			flash_message("One of the required fields was not correctly filled in", 'error');
		
			$item['name'] = $mybb->input['name'];
			$item['image'] = $mybb->input['image'];
		}
		else
		{
			$update_array = array(
					"name"			=> addslashes($mybb->input['name']),
					"image"			=> addslashes($mybb->input['image']),
			);
			
			$db->update_query("servers", $update_array, "id='$id'");			
			
			flash_message("Your Server has been updated", 'success');		
		}	
	}

	$page->add_breadcrumb_item("Edit Server", "index.php?module=servers/manage&amp;action=edit&amp;id=$id");
	
	// start the page
	$page->output_header("Edit Server Details");
	
	$form = new Form("index.php?module=servers/manage&amp;action=edit&amp;id=$id", "post", "", 1);
	
	$query = $db->simple_select("servers", "*", "id = $id");
	$item = $db->fetch_array($query);
	
	// if the user tried to save, don't wipe all of the entered fields in case of error
	if($mybb->input['save']=="save")
	{
		$item['name'] = $mybb->input['name'];
		$item['image'] = $mybb->input['image'];
	}
	
	// create a standard form container
	$form_container = new FormContainer("Edit Server Details");
	
	$form_container->output_row("Name", "The Server Name/Nickname(Most will not see this)", $form->generate_text_box('name', $item['name'], array('id' => 'name')), 'name');
	$form_container->output_row("Image", "The image that will appear on the server index.", $form->generate_text_box('image', $item['image'], array('id' => 'image')), 'image');

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