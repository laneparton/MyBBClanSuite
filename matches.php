<?php

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'roster.php');

$templatelist = "matches_index";
require_once "./global.php";
add_breadcrumb("Match Results", "matches.php");

$page = $_GET['page'];
$id = $_GET['id'];

if (!isset($id))
{
	if(($page < 1) || (!($page)) || !(is_numeric($page)))
	{
		$page = 1;
	}  
		
	$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "matches ORDER BY date DESC");	
	$lines	= $db->num_rows($query);
	
	$max = 25;
	$num = ($page - 1) * $max;
	$totalpage = ceil($lines/$max) + 1; 
	
	$query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "matches ORDER BY date DESC LIMIT $num, $max");
	$list 	= '';
	$limit 	= 550;
	
	while($match = $db->fetch_array($query))
	{
		$team_id = $match['team_id'];
		$team = $db->simple_select("rosterteams", "*", "id = $team_id");
		
		while ($info = $db->fetch_array($team))
		{
			$team_tag = $info['tag'];
			$team_name = $info['name'];
		}	
		
		if ($match['team_score'] > $match['opponent_score'])
		{
			$result = "<span style='color: green'>".$match['team_score']." - ".$match['opponent_score']."</span>";
		}
		elseif ($match['opponent_score'] > $match['team_score'])
		{
			$result = "<span style='color: red'>".$match['team_score']." - ".$match['opponent_score']."</span>";
		}
		else
		{
			$result="<span style='color: grey'>".$match['team_score']."-".$match['opponent_score']."</span>";
		}
		
		if(!$match['video'] == null)
		{
			$video = "";
		}
		
		$matches_row .= eval("\$matches_row .= \"".$templates->get("matches_row")."\";");
		
		$video = "";
		$result = "";
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
	
	eval("\$matches = \"".$templates->get("matches_index")."\";");
	output_page($matches);		
}
else
{
	$match_select = $db->simple_select("matches", "*", "id = $id");
	$match_num = $db->num_rows($match_select);
	
	if ($match_num > 0)
	{
		while ($match = $db->fetch_array($match_select))
		{
			add_breadcrumb($match['opponent_name']);

			$team_id = $match['team_id'];
			$team = $db->simple_select("rosterteams", "*", "id = $team_id");
			
			while ($info = $db->fetch_array($team))
			{
				$team_tag = $info['tag'];
				$team_name = $info['name'];
			}	
			
			if ($match['team_score'] > $match['opponent_score'])
			{
				$result = "<span style='color: green'>".$match['team_score']." - ".$match['opponent_score']."</span>";
			}
			elseif ($match['opponent_score'] > $match['team_score'])
			{
				$result = "<span style='color: red'>".$match['team_score']." - ".$match['opponent_score']."</span>";
			}
			else
			{
				$result="<span style='color: grey'>".$match['team_score']."-".$match['opponent_score']."</span>";
			}			
			
			eval("\$match = \"".$templates->get("matches_view")."\";");
			output_page($match);
		}
	}
	else
	{
		$title = "Invaild ID";
		$error = "This match ID does not exist, this could be due to several reasons such as the match being deleted or a mis-typed URL.";
		
		eval("\$error = \"".$templates->get("error")."\";");
		output_page($error);		
	}
}

?>