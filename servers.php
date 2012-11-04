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

$templatelist 	= "servers_index";
require_once "./global.php";
add_breadcrumb("Servers", "servers.php");

$servers_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "servers");
$servers_num		= $db->num_rows($servers_query);

if ($servers_num > 0)
{
	while ($servers_array = $db->fetch_array($servers_query))
	{
		$server_id	= $servers_array['id'];
		$server_name	= $servers_array['name'];
		$server_image	= $servers_array['image'];
		
		eval("\$servers_bit .= \"".$templates->get("servers_list")."\";");
	}
}
else
{
	eval("\$servers_bit = \"".$templates->get("servers_list_none")."\";");
}

eval("\$servers = \"".$templates->get("servers_index")."\";");
output_page($servers);

?>