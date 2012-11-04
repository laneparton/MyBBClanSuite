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

// start the page
	$page->output_header("Clan Suite Home");
	
	$sub_tabs['cshome'] = array(
		'title' => "Clan Suite",
		'link' => "index.php?module=clansuite",
		'description' => "Welcome to the Dashboard of the Clan Suite Mod for Mybb!<br /><br /> If you experience<strong> ANY </strong>problems, please feel free to <a href=\"mailto:parton720@gmail.com\">email</a> me(Mini).<br /><br /> The Clan Suite was originally made by <a href=\"http://community.mybb.com/user-18224.html\"> Benely </a> but has recently been updated by <a href=\"http://community.mybb.com/user-28414.html\"> Mini' </a>
		<br />
		<br />
		<br />
		<br />
		<strong> Would you like to include the pages in the forum navigation? </strong>
		<br />
		<br />
		If you would like to add any of the pages to the navigation on your forums, I suggest reviewing <a href=\"http://community.mybb.com/thread-6615.html\">this thread.</a> <br />
	");
	
	$page->output_nav_tabs($sub_tabs, 'cshome');
	
	$page->output_footer();

?>