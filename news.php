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

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'roster.php');

$templatelist = "news_index";
require_once "./global.php";
add_breadcrumb("News", "news.php");

$page = $_GET['page'];
$id = $_GET['id'];

if (!isset($id))
{
	if(($page < 1) || (!($page)) || !(is_numeric($page)))
	{
		$page = 1;
	}  
		
	$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "news ORDER BY date_posted DESC");	
	$lines	= $db->num_rows($query);
	
	$max = 5;
	$num = ($page - 1) * $max;
	$totalpage = ceil($lines/$max) + 1; 
	
	$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "news ORDER BY date_posted DESC LIMIT $num, $max");
	$list 	= '';
	$limit 	= 550;

	while($fetch = $db->fetch_array($query))
	{	
		$id = $fetch['id'];
		$title = $fetch['title'];
		$uid = $fetch['uid'];
		$posted = $fetch['date_posted'];
		$content = nl2br($fetch['content']);
		$replies = $db->num_rows($db->query("SELECT id FROM " . TABLE_PREFIX . "newscomments WHERE nid = $id"));
		
		if (strlen($content) > $limit)
		{
			$content = substr($content, 0, strrpos(substr($content, 0, $limit), ' ')) . '...';
		}
		
		$user_query = $db->query("SELECT username FROM " . TABLE_PREFIX . "users WHERE uid = $uid");
		while($user_details = $db->fetch_array($user_query))
		{
			$poster = $user_details['username'];
		}
	
		$posted = date("d.m.y", strtotime($posted));
	
		$news_item .= eval("\$news_post .= \"".$templates->get("news_item")."\";");
	}
	
	$tick = $page;
	$max = $page + 2;
	
	while($tick < $totalpage && $page <= ($page + 2))
	{
		$tick = $tick + 1;
	}
	
	$start = $page;
	$min = $page - 3;
	
	while ($start > 1 || $start == $min)
	{
		$start = $start - 1;
	}
    
    for($i = $start; $i < $tick; $i++)
    {
        if ($i == $page) 
        {
            $numbers .= "<a href=\"?page=".$i."\" class=\"active\">$i</a>";
        } 
        else 
        {
            $numbers .= "<a href=\"?page=".$i."\">$i</a>";
        }
    }
	
	eval("\$news = \"".$templates->get("news_index")."\";");
	output_page($news);	
}
else
{
	$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "news WHERE id = $id");
	$num	= $db->num_rows($query);
	
	if ($num < 1)
	{
		$title = "Invalid ID";
		$error = "This news ID does not exist, this could be due to several reasons such as the news post being deleted or a mis-typed URL.";
		
		eval("\$error = \"".$templates->get("error")."\";");
		output_page($error);			
	}
	else
	{	
		if ($mybb->user['uid'] && $_POST['comment'])
		{
			if (!empty($_POST['comment']))
			{
				$insert_array = array(
					"nid"			=> $id,
					"uid"			=> $mybb->user['uid'],
					"content"		=> $db->escape_string($_POST['comment']),
				);
				
				$db->insert_query("newscomments", $insert_array);
			}
		}
		
		if(($page < 1) || (!($page)) || !(is_numeric($page)))
		{
			$page = 1;
		}  
	
		while($fetch = $db->fetch_array($query))
		{	
			$id = $fetch['id'];
			$title = $fetch['title'];
			$uid = $fetch['uid'];
			$posted = $fetch['date_posted'];
			$article = nl2br($fetch['content']);
			
			$replies = $db->num_rows($db->query("SELECT id FROM " . TABLE_PREFIX . "newscomments WHERE nid = $id"));
		
			$user_query = $db->query("SELECT username FROM " . TABLE_PREFIX . "users WHERE uid = $uid");
			while($user_details = $db->fetch_array($user_query))
			{
				$poster = $user_details['username'];
			}
	
			$posted = date("d.m.y", strtotime($posted));
		}
		
		add_breadcrumb($title, "news.php?id=".$id);
				
		global $mybb;
		
		if ($mybb->user['uid'])
		{
			eval("\$news_form = \"".$templates->get("news_view_commentsubmit")."\";");
		}
		
		$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "newscomments WHERE nid = $id ORDER BY date_posted DESC");
		$num	= $db->num_rows($query);
				
		if ($num > 0)
		{
			$max = 5;
			$tnum = ($page - 1) * $max;
			$totalpage = ceil($num/$max) + 1; 
		
			$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "newscomments WHERE nid = $id ORDER BY date_posted DESC LIMIT $tnum, $max");
			
			while ($comment = $db->fetch_array($query))
			{
				$comment_uid = $comment['uid'];
				
				$user_query = $db->query("SELECT username, avatar FROM " . TABLE_PREFIX . "users WHERE uid = $comment_uid");
				while($user_details = $db->fetch_array($user_query))
				{
					$commenter_username = $user_details['username'];
					$commenter_avatar = $user_details['avatar'];
				}
				
				$comment_posted = date("F j, Y, g:i a", strtotime($comment['date_posted']));
				$comment_content = nl2br($comment['content']);			
				
				eval("\$news_comments .= \"".$templates->get("news_view_comments")."\";");	
			}
			
			$tick = $page;
			$max = $page + 2;
	
			while($tick < $totalpage && $page <= ($page + 2))
			{
				$tick = $tick + 1;
			}
	
			$start = $page;
			$min = $page - 3;
	
			while ($start > 1 || $start == $min)
			{
				$start = $start - 1;
			}
    
    		for($i = $start; $i < $tick; $i++)
   			{
        		if ($i == $page) 
       	 		{
           			$numbers .= "<a href=\"?id=$id&page=".$i."\" class=\"active\">$i</a>";
        		} 
        		else 
        		{
           	 		$numbers .= "<a href=\"?id=$id&page=".$i."\">$i</a>";
        		}
    		}			
		}
		else
		{
			eval("\$news_comments = \"".$templates->get("news_view_nocomments")."\";");	
		}
		
		eval("\$news = \"".$templates->get("news_view")."\";");
		
		output_page($news);
	}
}

?>