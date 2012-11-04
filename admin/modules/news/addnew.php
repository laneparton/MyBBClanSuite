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

$page->add_breadcrumb_item("Add News", "index.php?module=clansuite/news/addnew");

if($mybb->input['save']=="save")
{
	if(empty($mybb->input['title']) || empty($mybb->input['newscontent']))
	{
		flash_message("One of the required fields was not correctly filled in", 'error');
		
		$title = $mybb->input['title'];
		$image = $mybb->input['image'];
		$newscontent = $mybb->input['newscontent'];
	}
	else
	{
		flash_message("Your news post has been published", 'success');
		
		$insert_array = array(
					"title"			=> addslashes($mybb->input['title']),
					"uid"			=> $mybb->user['uid'],
					"content"		=> $db->escape_string($mybb->input['newscontent']),
					"image"			=> addslashes($mybb->input['image']),
				);
				
		$db->insert_query("news", $insert_array);
	}
}

// start the page
$page->output_header("News Manager");

$form = new Form("index.php?module=news/addnew", "post", "", 1);

$form_container = new FormContainer("Create News Post");
echo $form->generate_hidden_field("save", "save", array('id' => "save"))."\n";

$form_container->output_row("Title", "The title of your news post", $form->generate_text_box('title', $title, array('id' => 'title')), 'title');
$form_container->output_row("Image", "The image that will appear in the slider", $form->generate_text_box('image', $image, array('id' => 'image')), 'image');
$form_container->output_row("Content", "The text that will make up the content of your post", $form->generate_text_area('newscontent', $newscontent, array('id' => 'newscontent', 'rows' => '15', 'cols' => '55', 'style' => 'width: 100%;')), 'newscontent');

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