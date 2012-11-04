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

$page->add_breadcrumb_item("Manage News", "index.php?module=clansuite/news/manage");

if($mybb->input['action']=="" || $mybb->input['action']=="delete")
{
	if($mybb->input['action']=="delete")
	{	
		$id = $mybb->input['id'];
		$db->delete_query("news","id='$id'");
	}
	
	// start the page
	$page->output_header("News Manager");	
	
	$table = new Table;
	$table->construct_header("Title");
	$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
	
	$query = $db->simple_select("news", "id, title", "id + 0 <> 0", array("order_by" => 'date_posted', "order_dir" => 'DESC'));

	while($item = $db->fetch_array($query))
	{
		$title = $item['title'];
		$id = $item['id'];
		
		// create the "Edit/Delete" popup menu
		$popup = new PopupMenu("project_$id", $lang->options);
		
		// Add the items
		$popup->add_item("Edit", "index.php?module=news/manage&amp;action=edit&amp;id=$id");
		$popup->add_item("Remove", "index.php?module=news/manage&amp;action=delete&amp;id=$id");
		
		// create the info cell
		// construct_cell(content, array(html modifiers))
		$table->construct_cell("<a href=\"index.php?module=news/manage&amp;action=edit&amp;id=$id\"><strong>".$title."</strong></a>");
		// create the menu cell
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		
		// output the row
		$table->construct_row();
	}
	
	// display the table with our title
	$table->output("Manage News Posts");
	
	// end the page
	$page->output_footer();	
}
else if($mybb->input['action']=="edit")
{	
	$id = $mybb->input['id'];
		
	if($mybb->input['save']=="save")
	{
		if(empty($mybb->input['title']) || empty($mybb->input['newscontent']))
		{
			flash_message("One of the required fields was not correctly filled in", 'error');
		
			$item['title'] = $mybb->input['title'];
			$item['image'] = $mybb->input['image'];
			$item['content'] = $mybb->input['newscontent'];
		}
		else
		{
			$update_array = array(
					"title"			=> addslashes($mybb->input['title']),
					"uid"			=> $mybb->user['uid'],
					"content"		=> $db->escape_string($mybb->input['newscontent']),
					"image"			=> addslashes($mybb->input['image']),
			);
			
			$db->update_query("news", $update_array, "id='$id'");			
			
			flash_message("Your news post has been updated", 'success');		
		}	
	}
	
	if($mybb->input['deletecomment'])
	{
		$cid = $mybb->input['deletecomment'];
		$db->delete_query("newscomments","id='$cid'");
		
		flash_message("The news comment has been deleted", 'success');			
	}
	
	$page->add_breadcrumb_item("Edit Post", "index.php?module=news/manage&amp;action=edit&amp;id=$id");
	
	// start the page
	$page->output_header("News Manager");		
	
	$form = new Form("index.php?module=news/manage&amp;action=edit&amp;id=$id", "post", "", 1);
	
	$query = $db->simple_select("news", "*", "id = $id");
	$item = $db->fetch_array($query);
	
	// if the user tried to save, don't wipe all of the entered fields in case of error
	if($mybb->input['save']=="save")
	{
		$item['title'] = $mybb->input['title'];
		$item['image'] = $mybb->input['image'];
		$item['content'] = $mybb->input['newscontent'];
	}
	
	// create a standard form container
	$form_container = new FormContainer("Edit Post");
	
	// create the save flag
	echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";
	
	$form_container->output_row("Title", "The title of your news post", $form->generate_text_box('title', $item['title'], array('id' => 'title')), 'title');
	$form_container->output_row("Image", "The image that will appear in the slider", $form->generate_text_box('image', $item['image'], array('id' => 'image')), 'image');
	$form_container->output_row("Content", "The text that will make up the content of your post", $form->generate_text_area('newscontent', $item['content'], array('id' => 'newscontent', 'style' =>'width: 100%;', 'rows' => '25')), 'newscontent');
	
	// end the container
	$form_container->end();
	
	// add the save button
	$buttons[] = $form->generate_submit_button("Save Changes");
	
	// display and end
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	echo "<br />";
	
	$table = new Table;
	$table->construct_header("Comment");
	$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));	
	
	$query = $db->simple_select("newscomments", "id, uid, content", "nid = $id", array("order_by" => 'date_posted', "order_dir" => 'DESC'));

	while($item = $db->fetch_array($query))
	{
		$content = $item['content'];
		$cid = $item['id'];
		$uid = $item['uid'];
		
		$userquery = $db->simple_select("users", "username", "uid = $uid");
		while($useritem = $db->fetch_array($userquery))
		{
			$username = $useritem['username'];
		}
		
		// create the "Edit/Delete" popup menu
		$popup = new PopupMenu("project_$cid", $lang->options);
		
		// Add the items
		$popup->add_item("Remove", "index.php?module=news/manage&amp;action=edit&amp;id=$id&amp;deletecomment=$cid");
		
		// create the info cell
		// construct_cell(content, array(html modifiers))
		$table->construct_cell("<strong>".$username."</strong><br /><span class='smalltext'>".$content."</span>");
		// create the menu cell
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		
		// output the row
		$table->construct_row();
	}
	
	// display the table with our title
	$table->output("Manage News Comments");	
	
	// end the page
	$page->output_footer();			
}

?>