<?php
/*
Plugin Name: Draupnir Ringmanager
Plugin URI: http://wordpress.org/plugins/draupnir-ringmanager/
Description: A plugin for the creation, management, and display of webrings from within Wordpress.
Version: 1.7.7
Author: Jarandhel Dreamsinger
Author URI: http://dreamhart.org
License: GPL2
*/

/*  Copyright 2014 Jarandhel Dreamsinger  (email : jarandhel@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Putting everything in its own class.
class Draupnir_Ringmanager_Plugin {

function __construct() {

	// Call installation-only routines
	register_activation_hook( __FILE__, array ( $this, 'draupnir_install') );
	register_activation_hook( __FILE__, array ( $this, 'draupnir_install_data') );

	// Register plugin shortcodes
	add_shortcode( 'draupnir_ringhub', array ( $this, 'draupnir_ringhub') );
	add_shortcode( 'draupnir_ringcodes' , array ( $this, 'draupnir_ringsdisplay') );

	// Add plugin actions
	add_action('init', array ( $this, 'draupnir_check') );
	add_action('draupnir_hourly_event_hook', array ( $this, 'draupnir_do_this_hourly') );
	add_action('draupnir_scheduled_event_hook', array ($this, 'draupnir_scheduled_tasks') );
	add_action('admin_menu', array ($this, 'draupnir_adminmenu') );

	//Add filters for appending extra links on the Plugins page.
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array ($this, 'my_plugin_action_links') );
	add_filter( 'plugin_row_meta', array ($this, 'set_plugin_meta'), 10, 2 );
	
	// Functions and filters for adding extra cron schedules to Wordpress.
	add_filter( 'cron_schedules', array ($this, 'cron_add_weekly'));
	add_filter( 'cron_schedules', array ($this, 'cron_add_fortnight'));
	add_filter( 'cron_schedules', array ($this, 'cron_add_monthly'));
	
	// Add actions necessary to load styles and scripts on Admin pages.
	add_action('admin_print_scripts', array ($this, 'draupnir_admin_scripts'));
	add_action('admin_print_styles', array ($this, 'draupnir_admin_styles'));
	
	// Register sidebar widget.
	wp_register_sidebar_widget('draupnir_widget', __('Webrings'), array ($this, 'widget_Draupnir'));
	
	// On deactivation, remove all functions from the scheduled action hook.
	register_deactivation_hook( __FILE__, array ($this, 'prefix_deactivation') );


	// Default CSS
	$stylehtml = '<style type="text/css">
	.author{
	text-decoration:none;
	}
		
	table{
	width:60%;
	border-collapse:collapse;
	table-layout:auto;
	vertical-align:top;
	margin-bottom:15px;
	border:1px solid #CCCCCC;
	}

	table thead th{
	color:#FFFFFF;
	background-color:#666666;
	border:1px solid #CCCCCC;
	border-collapse:collapse;
	text-align:center;
	table-layout:auto;
	vertical-align:middle;
	}

	table tbody td{
	vertical-align:top;
	border-collapse:collapse;
	border-left:1px solid #CCCCCC;
	border-right:1px solid #CCCCCC;
	}
	
	table thead th, table tbody td{
	padding:5px;
	border-collapse:collapse;
	}

	table tbody tr.light{
	color:#333333;
	background-color:#F7F7F7;
	}

	table tbody tr.dark{
	color:#333333;
	background-color:#E8E8E8;
	}
	
	input[type=text]{
	background: #cecdcd; /* Fallback */
	background: rgba(206, 205, 205, 0.6);
	border: 2px solid #666;
	padding: 6px 5px;
	line-height: 1em;
	-webkit-box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	-moz-box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	-webkit-border-radius: 8px !important; 
	-moz-border-radius: 8px !important;
	border-radius: 8px !important; 
	margin-bottom: 10px;
	width: 300px;
	}
	
	select{
	background: #cecdcd; /* Fallback */
	background: rgba(206, 205, 205, 0.6);
	border: 2px solid #666;
	padding: 6px 5px;
	height: 2.8em !important;
	-webkit-box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	-moz-box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	box-shadow: inset -1px 1px 1px rgba(255, 255, 255, 0.65);
	-webkit-border-radius: 8px !important; 
	-moz-border-radius: 8px !important;
	border-radius: 8px !important; 
	margin-bottom: 10px;
	width: 300px;
	text-align:center;
	}
	
	.draupnir_listing {
	border: none !important;
	table-layout: fixed; color: rgb(194, 151, 90);
	border-collapse: separate;
	border-spacing: 0px !important;
	background-color: #CDCDCD !important;
	margin: 10px 0px 15px 0px;
	font-size: 8pt;
	width: 100%;
	text-align: left;
	}
	.draupnir_listing th {
	background-color: #E6EEEE !important;
	border: 1px solid #FFFFFF !important;
	padding: 4px;
	color: #3D3D3D !important;
	}
	.draupnir_listing td {
	color: #3D3D3D;
	padding: 4px;
	border: none !important;
	background-color: #FFFFFF !important;
	vertical-align: top;
	}
	.draupnir_listing .even td {
	background-color: #RINGDESCRIP# !important;
	}
	.draupnir_listing .odd td {
	background-color: #RINGLISTING# !important;
	}
	.draupnir_listing .strange td {
	background-color: #RINGHEADER# !important;
	}
	.draupnir_listing .row-hover tr:hover td {
	background-color: #D0D0D6!important;
	}
	.draupnir_admin_header::before {
	content: "\f108" !important;
	font-family: "dashicons" !important;
	font-size: larger !important;
	vertical-align: text-bottom !important;
	}
	.draupnir_listing span {
	vertical-align: baseline; !important
	}
	.draupnir_limited {
	width:50px !important; 
	max-width:50px !important; 
	height: 20px !important; 
	max-height: 20px !important; 
	background:#FFFFFF !important;
	}

	.draupnir_listing .pagination .current {
	background: #777;
	color:#fff;
	}

	.draupnir_listing .pagination span, .draupnir_listing .pagination a {
	display:block;
	float:left;
	margin: 2px 2px 2px 0;
	padding:6px 8px 1px;
	text-decoration:none;
	width:auto;
	color:#fff;
	background: #EEE;
	-moz-transition:background .5s ease-in-out;
	-o-transition:background .5s ease-in-out;
	-webkit-transition:background .5s ease-in-out;
	transition:background .5s ease-in-out;
	}

	</style>';
	
	// Default HTML for Webring
	$defaultringcode = '<table border="0" cellpadding="2" cellspacing="0" style="border:0px hidden #a0b0c0;margin:0px 0px;font:normal 12px Arial,sans-serif;background:#d0d7df none;color:#000000; width:250px !important; border-spacing:0px !important;">
 	<tbody style="border-spacing:0px !important;">
    <tr style="text-align:center; font-size:12px;background:#c0c7cf none;">
    <td rowspan="2" style="padding:0px !important; width:70px; height:73px !important; background-image:url(\'#RINGIMAGE#\'); background-size:70px 73px;"></td>
    <td style="text-align:center;font-size:15px;background:#c0c7cf none;">#RINGNAME#</td>
   	</tr>
    <tr>
    <td style="text-align:center;background-color:#d0d7df; padding:0px !important;"><p style="font-size:12px;">
  	<a href="#RINGHUB#?do=PREV&id=#RINGID#" title="Previous"><<</a> |
  	<a href="#RINGHUB#" title="Straight Tracks">Home</a> |
  	<a href="#RINGHUB#?do=ADD&id=#RINGID#">Join</a> |
  	<a href="#RINGHUB#?do=RAND&id=#RINGID#" title="Random">?</a> |
  	<a href="#RINGHUB#?do=NEXT&id=#RINGID#" title="Next">>></a></p>
    </td>
    </tr>
  	</tbody>
	</table>';

	// Default Plugin options.
	$new_options = array (
	'rings' => '',
	'ringname' => '',
	'ringdescrip' => '',
	'ringcode' => $defaultringcode,
	'ringhome' => '',
	'css' => addslashes($stylehtml),
	'css_replaced' => '',
	'ring_image' => '',
	'ringheader_color' => '#aabadf',
	'ringlisting_color' => '#F0F0F6',
	'ringdescrip_color' => '#FFFFFF',
	'admin_emails' => get_option('admin_email'),
	'admin_email_subject' => 'New site joining #RINGNAME#!',
	'email_subject' => 'Welcome to #RINGNAME#!',
	'admin_email_text' => 'A new site has joined the ring! Check it out at #SITEURL# when you have the chance.',
	'email_text' => 'Welcome to #RINGNAME# Your site is not yet active.
Before it can be approved by the ring owner, the following html should be added to the main page or to an easily located page for webrings. 
	<!-- Begin Ring Code --> 
	#RINGCODE# 
	<!-- End Ring Code -->',
	'reorder_sched' => 'never',
	'reorder_method' => '',
	'version' => '');
	
	// Replace old-style individual options with new options array.  Load old preferences into new array.
	foreach( $new_options as $key => $value ) {
	if( $existing = get_option( 'draupnir_' . $key ) ) {
	$new_options[$key] = $existing;
	delete_option( 'draupnir_' . $key );
	}
	}

	// Add options array with default options.
	add_option( 'plugin_draupnir_settings', $new_options);

	// Load plugin options.
	$options = get_option('plugin_draupnir_settings');
	
	// Change option rand_sched to reorder_sched since its function has broadened.
	if (array_key_exists('rand_sched', $options) === TRUE) {
	$options['reorder_sched'] = $options['rand_sched'];
	unset($options['rand_sched']);
	}

	// Set current version; if stored version != current version, run install routine to update database tables.
	$draupnir_current_version = "1.7.4";
	if ($draupnir_current_version != $options['version']) {
	$this->draupnir_install();
	}
	$options['draupnir_version'] = $draupnir_current_version;

$siteaccessible = FALSE;

// Ring CSS Color Replacement Array
$replace = array (
		"#RINGHEADER#" => isset($options['ringheader_color']) ? $options['ringheader_color'] : '#aabadf',
		"#RINGLISTING#" => isset($options['ringlisting_color']) ? $options['ringlisting_color'] : '#F0F0F6',
		"#RINGDESCRIP#" => isset($options['ringdescrip_color']) ? $options['ringdescrip_color'] : '#FFFFFF'
		);
		
// Prepare Ring CSS for use in plugin.
$draupnir_css = $options['css'];
if ($draupnir_css == '') { $draupnir_css = $stylehtml; $options['css'] = $stylehtml;}
$replacedringcss = str_replace(array_keys($replace), array_values($replace), $draupnir_css);
$options['css_replaced'] = $replacedringcss;

// Update plugin options.
update_option("plugin_draupnir_settings", $options);

// Double-checking that we received $_GET properly.
if (strstr($_SERVER['REQUEST_URI'],'?') AND empty($_GET)) {
$_SERVER['QUERY_STRING'] = preg_replace('#^.*?\?#','',$_SERVER['REQUEST_URI']);
  parse_str($_SERVER['QUERY_STRING'], $_GET);
}
// End function __Construct()
}

// Function to create Admin Menu
function draupnir_adminmenu() {
	add_menu_page('Draupnir Ringmanager', 'Draupnir Ringmanager', 'activate_plugins', 'Draupnir', array ($this, 'draupnir_menudisplay'), plugins_url( 'icon.png', __FILE__ ));
} 

// Function for adding settings link to the Plugins page.
function my_plugin_action_links ( $links ) {
	$mylinks = array(
 	'<a href="' . admin_url( 'admin.php?page=Draupnir' ) . '">Settings</a>',
 	);
	return array_merge( $links, $mylinks );
}

// Function to add extra links to the meta information on the Plugins page.
function set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	$mylinks = array( '<a href="http://wordpress.org/support/plugin/draupnir-ringmanager">Plugin Support</a>', '<a href="https://www.facebook.com/DraupnirRM">Like on Facebook</a>', '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X4UZU2MRD7N4U">Donate</a>' );

	if ($file == $plugin) {return array_merge(
		$links,
		$mylinks
	);}
	return $links;
}

function cron_add_weekly( $schedules ) {
 	// Adds once weekly to the existing schedules.
 	$schedules['weekly'] = array(
 		'interval' => 604800,
 		'display' => __( 'Once Weekly' )
 	);
 	return $schedules;
}

function cron_add_fortnight( $schedules ) {
 	// Adds fortnight to the existing schedules.
 	$schedules['fortnight'] = array(
 		'interval' => 1209600,
 		'display' => __( 'Once Every Two Weeks' )
 	);
 	return $schedules;
}

function cron_add_monthly( $schedules ) {
 	// Adds monthly to the existing schedules.
 	$schedules['monthly'] = array(
 		'interval' => 2592000,
 		'display' => __( 'Once a Month' )
 	);
 	return $schedules;
}

// Load javascripts necessary for Admin pages.
function draupnir_admin_scripts() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
wp_enqueue_script(
            'iris',
            admin_url( 'js/iris.min.js' ),
            array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
            false,
            1
        );
}

// Load styles necessary for Admin pages.
function draupnir_admin_styles() {
wp_enqueue_style('thickbox');
wp_enqueue_style( 'wp-color-picker' );
}

// Function to run on plugin installation or update.  Creates & updates DB tables and schedules hourly code check for ring members.
function draupnir_install() {
global $wpdb;
$draupnir_table = $wpdb->prefix . "draupnir";
$draupnir_stats_table = $wpdb->prefix . "draupnir_stats";
$sql = "CREATE TABLE $draupnir_table (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	owner smallint(6) NOT NULL DEFAULT '0',
	ringorder smallint NOT NULL,
	uri tinytext NOT NULL,
	codeuri tinytext DEFAULT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	date date NOT NULL,
	status enum('inactive','suspended','hibernating','active') NOT NULL DEFAULT 'inactive',
	lookahead enum('yes','no') NOT NULL default 'yes',
	navbarstatus enum('unchecked','error','not found','found','override') NOT NULL DEFAULT 'unchecked',
	navbardate datetime DEFAULT NULL,
	UNIQUE KEY id (id)
);";
$stats_sql = "CREATE TABLE $draupnir_stats_table (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	clicktime datetime NOT NULL,
	clicktype enum('PREV','NEXT','RAND','GO') NOT NULL,
	clickto mediumint(9) DEFAULT NULL,
	clickfrom mediumint(9) DEFAULT NULL,
	UNIQUE KEY id (id)
);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
dbDelta( $stats_sql );
if (wp_next_scheduled( 'draupnir_hourly_event_hook') === FALSE) {wp_schedule_event( time(), 'hourly', 'draupnir_hourly_event_hook' );}
if (wp_next_scheduled( 'draupnir_scheduled_event_hook') === FALSE) {wp_schedule_event( time(), $options['reorder_sched'] , 'draupnir_scheduled_event_hook' );}
}
/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function prefix_deactivation() {
	wp_clear_scheduled_hook( 'draupnir_hourly_event_hook' );
	wp_clear_scheduled_hook( 'draupnir_scheduled_event_hook' );
}

// Populate DB table with data.
function draupnir_install_data() {
global $wpdb;
$draupnir_table = $wpdb->prefix . "draupnir";
$owner = 1;
$name = get_bloginfo( 'name' );
$ringorder = 1;
$uri = get_bloginfo( 'url' );
$description = get_bloginfo( 'description');
$status = 'active';
$lookahead = 'yes';
$date = date("Y-m-d");
$navbarstatus = 'unchecked';
$tablerows = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table");
  if (!$tablerows) {
$rows_affected = $wpdb->insert( $draupnir_table, array( 'owner' => $owner, 'uri' => $uri, 'ringorder' => $ringorder, 'title' => $name, 'description' => $description, 'date' => $date, 'status' => $status, 'lookahead' => $lookahead, 'navbarstatus' => $navbarstatus ) );
	}
}

// Function to display sidebar widget.
function widget_Draupnir($args) {
  extract($args);
  echo $before_widget;
  echo $before_title.'Webrings'.$after_title;
  echo $this->draupnir_ringsdisplay();
  echo $after_widget;
}

// Function to display ring code.
function draupnir_ringsdisplay() {
$options = get_option('plugin_draupnir_settings');
return "".stripslashes($options['rings'])."";
}

// Function to display the ring's hub.
function draupnir_ringhub() {
$options = get_option('plugin_draupnir_settings');
$ringhome = $options['ringhome'];
  if (!$ringhome) { $ringhome = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$options['ringhome'] = $ringhome;}
global $wpdb;
global $wp_query;
global $current_user;
get_currentuserinfo();
$draupnir_table = $wpdb->prefix."draupnir";
if($_GET["do"]) {
$action = $_GET["do"];
}
if($_GET["id"]) {
$id = $_GET["id"];
}
  
    
switch ($action) {

// Case for adding a new site to the webring

  	case "ADD":
  	ob_start();
  	$thecurrenturl = "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  	if (is_user_logged_in()) {
	if($_POST['Submit']){
			$tablerows = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table");
			$owner = $current_user->ID;
			$ringorder = $tablerows+1;
			$status = 'inactive';
			$lookahead = 'yes';
			$date = date("Y-m-d");
			$navbarstatus = 'unchecked';
			$name = $_POST['draupnir_sitename'];
			$uri = $_POST['draupnir_siteurl'];
			$codeuri = $_POST['draupnir_codeuri'];
			$description = $_POST['draupnir_sitedescrip'];
			$notunique = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE uri=%s", $uri);
			if (!$notunique) {
	  		$rows_affected = $wpdb->insert( $draupnir_table, array( 'owner' => $owner, 'uri' => $uri, 'codeuri' => $codeuri, 'ringorder' => $ringorder, 'title' => $name, 'description' => $description, 'date' => $date, 'status' => $status, 'lookahead' => $lookahead, 'navbarstatus' => $navbarstatus ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been added to the ring.  Please check your email for the html code.  Once it has been added, your site will be approved by the ring owner.</p></div>';
			$siteid = $this->getQuery('var', "SELECT id from $draupnir_table ORDER BY id DESC LIMIT 1" );
			$useremail = $current_user->user_email;
			$this->draupnir_sendmails($siteid, $useremail, $uri);
			$this->draupnir_check();
			} else {echo '<div id="message" class="updated"><p>'.$name.' is already in the ring!</p></div>';}
	}
	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo stripslashes($options['css_replaced']);
	echo '<div class="wrap">
	<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Join the Ring.</h2>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="75%">Site Name: </td>
	<td width="25%"><input type="text" name="draupnir_sitename" value="" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Site Url: </td>
	<td width="25%"><input type="text" name="draupnir_siteurl" value="" /></td>
	</tr>
	<tr class="light">
	<td width="75%">Code Url: </td>
	<td width="25%"><input type="text" name="draupnir_codeuri" value="" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Site Description: </td>
	<td width="25%"><textarea rows=4 cols=50 name="draupnir_sitedescrip"></textarea></td>
	</tr>
	</table>
	<input type="submit" name="Submit" class="button-primary" style="float:left" value="Add Site &raquo;" />
	</form>
	</div>';
	} else {echo '<p>Only logged-in users can add sites to the webring.  Please <a href="'.wp_login_url($thecurrenturl).'">Login</a> to continue.</p>';}
  	$output = ob_get_clean();
	return $output;
	break;
	
// Case for editing an existing site.
	
	case EDIT:
	ob_start();
  	$thecurrenturl = "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  	$ringpage = $this->getQuery('row', " SELECT * FROM $draupnir_table WHERE id = %s", $id);
  	if (!is_null($ringpage) && is_user_logged_in() && ($current_user->ID == $ringpage->owner)) {
	if($_POST['Submit']){
			$owner = $ringpage->owner;
			$status = $ringpage->status;
			$name = $_POST['draupnir_sitename'];
			$uri = $_POST['draupnir_siteurl'];
			$codeuri = $_POST['draupnir_codeuri'];
			$description = $_POST['draupnir_sitedescrip'];
	  		$rows_affected = $wpdb->update( $draupnir_table, array( 'owner' => $owner, 'uri' => $uri, 'title' => $name, 'description' => $description, 'status' => $status ), array( 'id' => $id ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been updated.</p></div>';
			$ringpage = $this->getQuery('row', " SELECT * FROM $draupnir_table WHERE id = %s", $id);
	  		$this->draupnir_check();
	}
	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo stripslashes($options['css_replaced']);
	$replace = array (
		"#RINGNAME#" => isset($options['ringname']) ? $options['ringname'] : 'Unnamed Ring',
		"#RINGHUB#" => $options['ringhome'],
		"#RINGID#" => $ringpage->id,
		"#RINGIMAGE#" => $options['ring_image']
		);
  	$replacedringcode = str_replace(array_keys($replace), array_values($replace),$options['ringcode']);
	echo '<div class="wrap">
	<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Update your site.</h2>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="25%">Site Name: </td>
	<td width="75%"><input type="text" name="draupnir_sitename" value="'.stripslashes($ringpage->title).'" /></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Url: </td>
	<td width="75%"><input type="text" name="draupnir_siteurl" value="'.$ringpage->uri.'" /></td>
	</tr>
	<tr class="light">
	<td width="25%">Code Url: </td>
	<td width="75%"><input type="text" name="draupnir_codeuri" value="'.$ringpage->codeuri.'" /></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Description: </td>
	<td width="75%"><textarea rows=4 cols=50 name="draupnir_sitedescrip">'.stripslashes($ringpage->description).'</textarea></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Status: </td>
	<td width="75%">
	<p>'.$ringpage->status.'</p></td>
	</tr>
	</td>
	</tr>
	<tr class="dark">
	<td width="25%">Code to Use:</td>
	<td width="75%"><textarea rows="50" cols="80" name="draupnir_sitehtml" style="width: 400px; height:400px !important;">'.htmlentities(stripslashes($replacedringcode)).'</textarea></td>
	</tr>
	<tr class=light">
	<td width="75%">Preview:</td>
	<td width="25%"><p>'.stripslashes($replacedringcode).'</p></td>
	</tr>
	</table>
	<input type="submit" name="Submit" class="button-primary" style="float:left" value="Update Site &raquo;" />
	<input type="button" name="Cancel" class="button-secondary" style="float:right" value="Delete Site &raquo;" onclick="window.location=\''.$options['ringhome'].'?do=DELETE&id='.$id.'\';"/>
	</form>
	</div>';
	} else {echo '<p>Only logged-in users can edit sites in the webring.  Please <a href="'.wp_login_url($thecurrenturl).'">Login</a> to continue.  If you are already logged in, you do not own this site.</p>';}
  	$output = ob_get_clean();
	return $output;
	break;
	
	//Case for deleting a site from the webring
	
	case DELETE:
	ob_start();
  	$thecurrenturl = "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  	$ringpage = $this->getQuery('row', " SELECT * FROM $draupnir_table WHERE id = %s", $id);
  	if (!is_null($ringpage) && is_user_logged_in() && ($current_user->ID == $ringpage->owner)) {
	if($_POST['Submit']){
			$name = $ringpage->title;
	  		$rows_affected = $wpdb->delete( $draupnir_table, array( 'id' => $id ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been deleted.</p></div>';
	  		$this->draupnir_check();
	}
	echo stripslashes($options['css_replaced']);
	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<div class="wrap">
	<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Delete your site.</h2>
	<p>Preparing to delete site '.$ringpage->title.'.  Warning: this cannot be undone.  Do you wish to proceed?</p>
	<input type="submit" name="Submit" class="button-primary" style="float:left" value="Delete Site &raquo;" />
	<input type="button" name="Cancel" class="button-secondary" style="float:right" value="Do Not Delete &raquo;" onclick="location.href=\''.$options['ringhome'].'\';"/>
	</form>
	</div>';
	} else {echo '<p>Only logged-in users can delete sites in the webring.  Please <a href="'.wp_login_url($thecurrenturl).'">Login</a> to continue.  If you are already logged in, you do not own this site.</p>';}
  	$output = ob_get_clean();
	return $output;
	
	case MANAGE:
	$this->draupnir_check();
	$yourringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table WHERE owner = %s", $current_user->ID);
	ob_start();
	echo stripslashes($options['css_replaced']);
	if ( $yourringpages )
	{
	  echo 	'<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Manage your sites.</h2>';
	  echo '<table class="draupnir_listing" style="width:100%;"><tbody class="row-hover">';
	  foreach ( $yourringpages as $ringpage )
	{
		echo '<tr class = "odd"><td>'.$ringpage->ringorder.'. <a href="'.$ringpage->uri.'">'.stripslashes($ringpage->title).'</a></td><td style="text-align:right;">Owned by: '.get_userdata($ringpage->owner)->display_name.'</td></tr>';
		echo '<tr class = "even"><td colspan="2">'.stripslashes($ringpage->description).'</td></tr>';
	    echo '<tr class="even"><td colspan="2"><a href="'.$options['ringhome'].'?do=EDIT&id='.$ringpage->id.'">Edit Site</a></td></tr>'; 
	}
	  echo '
	  </tbody>
	  </table>';
	} else { echo '<p>You do not own any ring pages.</p>';}
	$output = ob_get_clean();
	return $output;
	break;
	
	case STATS:
  	$this->draupnir_check();
  	ob_start();
	echo stripslashes($options['css_replaced']);
	echo 	'<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Ring Statistics.</h2>';
	$this->draupnir_ringstats();
	$output = ob_get_clean();
	return $output;
  	break;
  
	
	case GO:
	//Case for listing the webring
	case NULL:
	$this->draupnir_check();
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = isset( $_GET['limit'] ) ? absint( $_GET['limit'] ) : 10;
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE status = 'active'");
	$num_of_pages = ceil( $total / $limit );
	$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'aag' ),
    'next_text' => __( '&raquo;', 'aag' ),
    'total' => $num_of_pages,
    'current' => $pagenum,
) );
	$ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table WHERE status = 'active' ORDER BY ringorder LIMIT %d, %d", $offset, $limit);
	$yourringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table WHERE owner = %d", $current_user->ID);
	ob_start();
	echo stripslashes($options['css_replaced']);
	echo '<p>'.stripslashes($options['ringdescrip']).'</p>';
	$thisringpage = isset($id) ? $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id) : NULL;
	echo $siteaccessible == TRUE ? '<div class="updated">'.$thisringpage->title.' could not be reached. Bypass this test by clicking <a href='.$thisringpage->uri.'>here</a>.</div>' : '';
	echo current_user_can('moderate_comments') == true ? '<a href="'. admin_url( 'admin.php?page=Draupnir' ) .'&tab=management">Manage Ring</a>' : ''; 
	if ( $ringpages )
	{
	  echo '<table class="draupnir_listing" style="width:100%;"><tbody class="row-hover">
	  <tr class="strange"><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$options['ringhome'].'?do=ADD">Join the Ring</a></td><td style="text-align:center;">';
	  if ( $yourringpages ) { echo '<a href="'.$options['ringhome'].'?do=MANAGE">Manage Your Sites</a>';}
	  echo '</td><td style="text-align:right;"><a href="'.$options['ringhome'].'?do=RAND&id=1">Jump to a Random Site in the Ring</a></td></tr>';
	  echo '<tr><td class="pagination_container" colspan=3><nav class="pagination" style="display:block !important;">' . $page_links . '</nav><span style="text-align:right;"><form action="'.$thecurrenturl.'" method="get"><select class="draupnir_limited" name="limit"><option value="5" '.selected( $limit, 5, false ).'>5</option><option value="10" '.selected( $limit, 10, false ).'>10</option><option value="30" '.selected( $limit, 30, false ).'>30</option><option value="100" '.selected( $limit, 100, false ).'>100</option></select><input type="submit" value="Update"></form></span></td></tr>';
	foreach ( $ringpages as $ringpage )
	{
		echo '<tr class="odd"><td>'.$ringpage->ringorder.'. <a href="'.$options['ringhome'].'?do=GO&id='.$ringpage->id.'">'.stripslashes($ringpage->title).'</a></td><td></td><td style="text-align:right;">Owned by: '.get_userdata($ringpage->owner)->display_name.'</td></tr>';
		echo '<tr class="even"><td colspan="3">'.stripslashes($ringpage->description).'</td></tr>';
	}
	echo '
	<tr class="strange"><td><a href="'.$options['ringhome'].'?do=ADD">Join the Ring</a></td><td><a href="'.$options['ringhome'].'?do=STATS">Ring Statistics</a></td><td style="text-align:right;"><a href="'.$options['ringhome'].'?do=RAND&id=1">Jump to a Random Site in the Ring</a></td></tr>
	</tbody>
	</table>';
	}
	else
	{ echo '<h2>No Ring Members Found</h2>'; }
	$output = ob_get_clean();
	return $output;
  	break;
  	
  // Case for invalid queries
  default:
  return "<p>Invalid query.  Try again.</p>";
  break;
}
update_option('plugin_draupnir_settings', $options);
}

// Function for sending email to ring members & ring admin(s).
function draupnir_sendmails($siteid, $useremail, $siteurl){
$options = get_option('plugin_draupnir_settings');
			$replace1 = array (
			"#RINGNAME#" => isset($options['ringname']) ? $options['ringname'] : 'Unnamed Ring',
			"#RINGHUB#" => $options['ringhome'],
			"#RINGID#" => $siteid,
			"#RINGIMAGE#" => $options['image']
			);
			$replace2 = array (
			"#RINGNAME#" => isset($options['ringname']) ? $options['ringname'] : 'Unnamed Ring',
			"#RINGHUB#" => $options['ringhome'],
			"#RINGID#" => $siteid,
			"#RINGCODE#" => str_replace(array_keys($replace1), array_values($replace1),$options['ringcode']),
			"#SITEURL#" => $siteurl
			);
			$subject = str_replace(array_keys($replace2), array_values($replace2), $options['email_subject']);
			$message = str_replace(array_keys($replace2), array_values($replace2), $options['email_text']);
			wp_mail( $useremail, $subject, $message);
			$admin_email = $options['admin_emails'];
			$subject = str_replace(array_keys($replace2), array_values($replace2), $options['admin_email_subject']);
			$message = str_replace(array_keys($replace2), array_values($replace2), $options['admin_email_text']);
			wp_mail ( $admin_email, $subject, $message);		
}

// Function to make sure there are no gaps in the ring & handle redirection based on ring code.
function draupnir_check() {
$options = get_option('plugin_draupnir_settings');
global $wpdb;
global $wp_query;
$draupnir_table = $wpdb->prefix."draupnir";
$draupnir_stats_table = $wpdb->prefix."draupnir_stats";
if($_GET["do"]) {
$action = $_GET["do"];
}
if($_GET["id"]) {
$id = $_GET["id"];
}
// Make sure there are no gaps in the ring order.  Possibly move this to its own function.
$ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table WHERE status = 'active' ORDER BY ringorder");
	$neworder = 1;
	if ( $ringpages ) {
	foreach ( $ringpages as $ringpage )
	{
	$rows_affected = $wpdb->update( $draupnir_table, array( 'ringorder' => $neworder++ ), array( 'id' => $ringpage->id ) );
	}}
$ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table WHERE status = 'active' ORDER BY ringorder");
$wpdb->flush();
    
switch ($action) {

  case NEXT:
  $clickfrom = $id;
  do {
  $testsite = FALSE;
  $ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
  $numsites = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE status = 'active'" );
  $nextsite = $ringpage->ringorder < $numsites ? $ringpage->ringorder+1 : 1;
  $nextpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE (status = 'active' AND ringorder = %s)", $nextsite);
  $testsite = $this->draupnir_lookahead($nextpage->uri) == TRUE ? TRUE : FALSE;
  $id = $nextpage->id;
  } while ($testsite == FALSE);
  $clickto = $nextpage->id;
  $wpdb->insert( $draupnir_stats_table, array ( 'clickfrom' => $clickfrom, 'clickto' => $clickto, 'clicktype' => 'NEXT', 'clicktime' => date("Y-m-d H:i:s")) );
  wp_redirect( $nextpage->uri ); 
  exit;
  break;
  
  case PREV:
  $clickfrom = $id;
  do {
  $testsite = FALSE;
  $ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
  $numsites = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE status = 'active'");
  $nextsite = $ringpage->ringorder > 1 ? $ringpage->ringorder-1 : $numsites;
  $nextpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE (status = 'active' AND ringorder = %s)", $nextsite);
  $testsite = $this->draupnir_lookahead($nextpage->uri) == TRUE ? TRUE : FALSE;
  $id = $nextpage->id;
  } while ($testsite == FALSE);
  $clickto = $nextpage->id;
  $wpdb->insert( $draupnir_stats_table, array ( 'clickfrom' => $clickfrom, 'clickto' => $clickto, 'clicktype' => 'PREV', 'clicktime' => date("Y-m-d H:i:s")) );
  wp_redirect( $nextpage->uri ); 
  exit;
  break;
  
  case RAND:
  $clickfrom = $id;
  $ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
  $numsites = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE status = 'active'" );
  if ($numsites > 1) {
  do {
  	$testsite = FALSE;
  	$nextsite = rand(1,$numsites);
  	$nextpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE (status = 'active' AND ringorder = %s)", $nextsite);
  	$testsite = $this->draupnir_lookahead($nextpage->uri) == TRUE ? TRUE : FALSE;
  	} while ($nextsite==$ringpage->ringorder && $testsite == FALSE);
  	$clickto = $nextpage->id;
  	$wpdb->insert( $draupnir_stats_table, array ( 'clickfrom' => $clickfrom, 'clickto' => $clickto, 'clicktype' => 'RAND', 'clicktime' => date("Y-m-d H:i:s")) );
  	wp_redirect( $nextpage->uri );
  } else {
  $clickto = NULL;
  $wpdb->insert( $draupnir_stats, array ( 'clickfrom' => $clickfrom, 'clickto' => $clickto, 'clicktype' => 'RAND', 'clicktime' => date("Y-m-d H:i:s")) );
  wp_redirect($options['draupnir_ringhome']);}
  exit;
  break;
  
  case GO:
    $siteaccessible = FALSE;
  	$testsite = FALSE;
  	$ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
  	$testsite = $this->draupnir_lookahead($ringpage->uri) == TRUE ? TRUE : FALSE;
  	if ($testsite == TRUE) {
  	$clickfrom = NULL;
  	$clickto = $ringpage->id;
  	$wpdb->insert( $draupnir_stats_table, array ( 'clickfrom' => $clickfrom, 'clickto' => $clickto, 'clicktype' => 'GO', 'clicktime' => date("Y-m-d H:i:s")) );
  	wp_redirect( $ringpage->uri );} else { $siteaccessible = TRUE;}
  default:
  break;
}}

// Function to set Admin page tabs.
function draupnir_menu_tabs() {
		$draupnir_tabs = array( 'default' => 'General Options', 'appearance' => 'Appearance', 'emails' => 'Email Settings', 'management' => 'Ring Management', 'stats' => 'Ring Stats', 'faq' => 'FAQ' );
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';
		ob_start();
		echo '<h2 class="nav-tab-wrapper" style="padding-left: 4px;">';
		foreach ( $draupnir_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? ' nav-tab-active' : '';
			$first = $tab_key == "default" ? ' nav-tab-first' : '';
			echo '<a class="nav-tab' . $first . $active . '" href="?page=Draupnir&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X4UZU2MRD7N4U"><image src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="vertical-align: text-bottom;"></a></h2>';
		$output = ob_get_clean();
		return $output;
	}

// Function to display Admin page tabs.
function draupnir_showtabs() {
	$options = get_option('plugin_draupnir_settings');
	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';
	switch ($current_tab) {
	
	case faq:
	ob_start();
	echo '<hr />
		  <h2>Getting Started</h2>
		  <p>Setting up a webring with Draupnir is meant to be easy and intuitive. &nbsp;If you&#39;ve ever worked with a webring before, you&#39;ll find a lot of familiar features and more will be added in the future. &nbsp;</p>
		  <p>To get started, there are only four options you need to set under <a href="?page=Draupnir&tab=default">General Options</a>:</p>
		  <ol>
		  <li>Pick a name for your webring.</li>
		  <li>Write a basic description of your webring.</li>
		  <li>Select an image for your webring.</li>
		  <li>Set a homepage for your webring.</li>
		  </ol>
		  <p>Everything else will default, and you can change it to what you&#39;d like it to be later.</p>
		  <p>Next, <a href="post-new.php?post_type=page">create a page</a> for your webring. &nbsp;This page should have the same url as the homepage you selected earlier, and it&#39;s recommended that you give it a title that matches the name you have chosen for your ring. &nbsp;The only contents it needs to have is the shortcode&nbsp;[draupnir_ringhub].</p>
		  <p>Finally, to display the webrings of which you are a member on your site, go back to <a href="?page=Draupnir&tab=default">General Options</a> and fill in your site&#39;s individual ring code under Code for All Webrings. &nbsp;You can display these webrings on any post or page using the shortcode [draupnir_ringcodes] or using the provided Webrings widget.</p>
		  <p>Good luck, and I hope you enjoy using Draupnir Ringmaker as much as I&#39;ve enjoyed making it.</p>
		  <h2>How much can I customize my ring code or css?</h2>
		  <p>That\'s really up to you and your skills with html, css, javascript, and other web technologies.  On one of the webrings I manage using Draupnir, I\'ve switched from the default code to using an imagemap for all ring navigation.  As long as the links are correct, that\'s what really matters.</p>
		  <h2>What if I mess up my ring code or css?</h2>
		  <p>To reset your current ring code or css (including color choices) to the defaults, just clear the fields and click on update options.  The code for this webring, ringheader color, ringlisting color, ringdescription color, and css for this website fields will be repopulated with their defaults any time they are blank when the options are updated.</p>
		  <h2>What\'s the deal with the donation link?</h2>
		  <p>Draupnir Ringmanager is donationware.  It\'s entirely free, and always will be, and its source code is available for anyone to modify freely however they would like.</p>
		  <p>There will never be limits on its functionality, ads of any kind, or a paid version with additional features.  Instead, if you like it and have some funds to spare, please consider making a small donation to show your appreciation.  Thank you.</p>
		  <h2>Webrings? Seriously?</h2>
		  <p>I know what you\'re thinking - webrings, in this day and age?  Surely that died out back when Yahoo took over Webring.com?  And you\'re right, it is hard to find a decent webring provider these days.  But the basic problem remains -- how do you link together groups of topically related sites so that visitors can easily find them?  Link exchanges are one method, but it\'s complicated to get every site to link to every other site.  Blogrolls are another, but above a certain size they grow cumbersome and again it\'s hard to get every blog to link to every other one.  To my knowledge, there is still no better method than a webring for really connecting a community of interrelated sites.  So why not host your own, without the middle men?</p>';
	$output = ob_get_clean();
	return $output;
	break;
	
	case stats:
	ob_start();
	$this->draupnir_ringstats();
	$output = ob_get_clean();
	return $output;
	break;
	
	case emails:
	ob_start();
	if($_POST['Submit']){
        	$draupnir_admin_emails = !empty( $_POST['draupnir_admin_emails'] ) ? $_POST['draupnir_admin_emails'] : get_option('admin_email');
        	$draupnir_admin_email_subject = !empty( $_POST['draupnir_admin_email_subject'] ) ? $_POST['draupnir_admin_email_subject'] : 'New site joining #RINGNAME#!';
        	$draupnir_email_subject = !empty( $_POST['draupnir_email_subject'] ) ? $_POST['draupnir_email_subject'] : 'Welcome to #RINGNAME#!';
        	$draupnir_admin_email_text = !empty( $_POST['draupnir_admin_email_text'] ) ? $_POST['draupnir_admin_email_text'] : 'A new site has joined the ring! Check it out at #SITEURL# when you have the chance.';
        	$draupnir_email_text = !empty( $_POST['draupnir_email_text'] ) ? $_POST['draupnir_email_text'] : 'Welcome to #RINGNAME# Your site is not yet active.
Before it can be approved by the ring owner, the following html should be added to the main page or to an easily located page for webrings. 
<!-- Begin Ring Code --> 
#RINGCODE# 
<!-- End Ring Code -->';
			$options['admin_emails'] = $draupnir_admin_emails;
			$options['admin_email_subject'] = $draupnir_admin_email_subject;
			$options['email_subject'] = $draupnir_email_subject;
			$options['admin_email_text'] = $draupnir_admin_email_text;
			$options['email_text'] = $draupnir_email_text;
			update_option('plugin_draupnir_settings', $options);
			echo '<div id="message" class="updated"><p>Update Successful!</p></div>';
		}
	echo '<p>Valid shortcodes are #RINGNAME#, #RINGHUB#, #RINGID#, #RINGCODE#, and #SITEURL#</p>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="75%">Admin Emails:</td>
	<td width="25%"><input type="text" name="draupnir_admin_emails" value="'.$options['admin_emails'].'" /></td>	
	</tr>
	<tr class="dark">
	<td width="75%">Admin Email Subject:</td>
	<td width="25%"><input type="text" name="draupnir_admin_email_subject" value="'.$options['admin_email_subject'].'" /></td>	
	</tr>
	<tr class="light">
	<td width="75%">User Email Subject:</td>
	<td width="25%"><input type="text" name="draupnir_email_subject" value="'.$options['email_subject'].'" /></td>	
	</tr>
	<tr class="dark">
	<td width="75%">Admin Email Text: </td>
	<td width="25%"><textarea rows=5 cols=80 name="draupnir_admin_email_text">'.stripslashes($options['admin_email_text']).'</textarea></td>
	</tr>
	<tr class="light">
	<td width="75%">Email Text:</td>
	<td width="25%"><textarea rows=5 cols=80 name="draupnir_email_text">'.stripslashes($options['email_text']).'</textarea></td>
	</tr>
	</table>';
	$output = ob_get_clean();
	return $output;
	break;
	
	case addsite:
	global $wpdb;
	global $wp_query;
	global $current_user;
	get_currentuserinfo();
	$draupnir_table = $wpdb->prefix."draupnir";
	$this->draupnir_check();
	ob_start();
	if($_POST['Submit']){
			$tablerows = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table" );
			$ringorder = $tablerows+1;
			$owner = $_POST['draupnir_siteowner'];
			$status = $_POST['draupnir_sitestatus'];
			$lookahead = 'yes';
			$date = date("Y-m-d");
			$navbarstatus = 'unchecked';
			$name = $_POST['draupnir_sitename'];
			$uri = $_POST['draupnir_siteurl'];
			$codeuri = $_POST['draupnir_codeuri'];
			$description = $_POST['draupnir_sitedescrip'];
			$notunique = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE uri=%s", $uri);
			if (!$notunique) {
	  		$rows_affected = $wpdb->insert( $draupnir_table, array( 'owner' => $owner, 'uri' => $uri, 'codeuri' => $codeuri, 'ringorder' => $ringorder, 'title' => $name, 'description' => $description, 'date' => $date, 'status' => $status, 'lookahead' => $lookahead, 'navbarstatus' => $navbarstatus ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been added to the ring.</p></div>';
			$this->draupnir_check();
			} else {echo '<div id="message" class="updated"><p>'.$name.' is already in the ring!</p></div>';}
	}
	echo stripslashes($option['css_replaced']);
	echo '<div class="wrap">
	<h2><a href="' .$options['ringhome']. '">'.$options['ringname'].'</a>: Add Site to Ring.</h2>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="75%">Site Name: </td>
	<td width="25%"><input type="text" name="draupnir_sitename" value="" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Site Url: </td>
	<td width="25%"><input type="text" name="draupnir_siteurl" value="" /></td>
	</tr>
	<tr class="light">
	<td width="75%">Code Url: </td>
	<td width="25%"><input type="text" name="draupnir_codeuri" value="" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Site Description: </td>
	<td width="25%"><textarea rows=4 cols=50 name="draupnir_sitedescrip"></textarea></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Status: </td>
	<td width="75%">
	<select name="draupnir_sitestatus">
  		<option value="inactive" '.selected( $ringpage->status, 'inactive', false ).'>Inactive</option>
  		<option value="suspended" '.selected( $ringpage->status, 'suspended', false ).'>Suspended</option>
  		<option value="hibernating" '.selected( $ringpage->status, 'hibernating', false ).'>Hibernating</option>
  		<option value="active" '.selected( $ringpage->status, 'active', false ).'>Active</option>
	</select>
	</td>
	</tr>
	<tr class="light">
	<td width="25%">Site Owner: </td>
	<td width="75%">'.wp_dropdown_users(array('selected'=>false,'name'=>'draupnir_siteowner', 'echo'=>false)).'</td>
	</tr>
	</td>
	</tr>
	</table>
	</div>';
	$output = ob_get_clean();
	return $output;
	break;
	
		
	case appearance:
	ob_start();
	if($_POST['Submit']){
        	$draupnir_css = $_POST['draupnir_css'];
        	$draupnir_ringheader_color = !empty( $_POST['draupnir_ringheader_color'] ) ? $_POST['draupnir_ringheader_color'] : '#aabadf';
        	$draupnir_ringlisting_color = !empty( $_POST['draupnir_ringlisting_color'] ) ? $_POST['draupnir_ringlisting_color'] : '#F0F0F6';
        	$draupnir_ringdescrip_color = !empty( $_POST['draupnir_ringdescrip_color'] ) ? $_POST['draupnir_ringdescrip_color'] : '#FFFFFF';	
			$options['css'] = $draupnir_css;
			$options['ringheader_color'] = $draupnir_ringheader_color;
			$options['ringlisting_color'] = $draupnir_ringlisting_color;
			$options['ringdescrip_color'] = $draupnir_ringdescrip_color;
			update_option('plugin_draupnir_settings', $options);
			echo '<div id="message" class="updated"><p>Update Successful!</p></div>';
		}
	$replace = array (
		"#RINGHEADER#" => isset($options['ringheader_color']) ? $options['ringheader_color'] : '#aabadf',
		"#RINGLISTING#" => isset($options['ringlisting_color']) ? $options['ringlisting_color'] : '#F0F0F6',
		"#RINGDESCRIP#" => isset($options['ringdescrip_color']) ? $options['ringdescrip_color'] : '#FFFFFF'
		);
	$draupnir_css = $options['css'];
	if ($draupnir_css == '') { $draupnir_css = $stylehtml; $options['css'] = addslashes($draupnir_css); }
	$replacedringcss = str_replace(array_keys($replace), array_values($replace), $options['css']);
	$options['css_replaced'] = $replacedringcss;
	echo '
	<script type="text/javascript">
	jQuery(document).ready(function($){
    $(".color-picker").iris();
	});
	</script>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="75%">Ringheader Color:<br />#RINGHEADER# </td>
	<td width="25%"><input type="text" name="draupnir_ringheader_color" id="color" class="color-picker" value="'.$options['ringheader_color'].'" style="background-color:'.$options['ringheader_color'].';" /></td>	
	</tr>
	<tr class="dark">
	<td width="75%">Ringlisting Color:<br />#RINGLISTING# </td>
	<td width="25%"><input type="text" name="draupnir_ringlisting_color" id="color" class="color-picker" value="'.$options['ringlisting_color'].'" style="background-color:'.$options['ringlisting_color'].';" /></td>	
	</tr>
	<tr class="light">
	<td width="75%">Ringdescription Color:<br />#RINGDESCRIP# </td>
	<td width="25%"><input type="text" name="draupnir_ringdescrip_color" id="color" class="color-picker" value="'.$options['ringdescrip_color'].'" style="background-color:'.$options['ringdescrip_color'].';" /></td>	
	</tr>
	<tr class="dark">
	<td width="75%">CSS for This Webring: </td>
	<td width="25%"><textarea rows=5 cols=80 name="draupnir_css">'.stripslashes($options['css']).'</textarea></td>
	</tr>
	<tr class="light">
	<td width="75%">Amended CSS:</td>
	<td width="25%"><pre>'.htmlentities(stripslashes($options['css_replaced'])).'</pre></td>
	</tr>
	</table>';
	update_option('plugin_draupnir_settings', $options);
	$output = ob_get_clean();
	return $output;
	break;
	
	case management:
	global $wpdb;
	global $wp_query;
	global $current_user;
	get_currentuserinfo();
	$draupnir_table = $wpdb->prefix."draupnir";
	$this->draupnir_check();
	ob_start();
	if (isset($_GET['id'])) {
	$id = $_GET["id"];
	$ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
	if (isset($_GET['destroy'])) { if($_POST['Submit']){
			$name = $ringpage->title;
	  		$rows_affected = $wpdb->delete( $draupnir_table, array( 'id' => $id ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been deleted.</p></div>';
	  		$this->draupnir_check();
	}
	echo '<p>Click "Update Options" to confirm site deletion.</p>'; } else {
	if($_POST['Submit']){
			$owner = $_POST['draupnir_siteowner'];
			$status = $_POST['draupnir_sitestatus'];
			$name = $_POST['draupnir_sitename'];
			$uri = $_POST['draupnir_siteurl'];
			$codeuri = $_POST['draupnir_codeuri'];
			$description = $_POST['draupnir_sitedescrip'];
			$navbarstatus = isset($_POST['draupnir_sitenavstatus']) == TRUE ? $_POST['draupnir_sitenavstatus'] : $this->getQuery('var', "SELECT navbarstatus from $draupnir_table WHERE id = %s", $id);
			$navbarstatus = (isset($_POST['draupnir_sitenavstatus']) == FALSE && $this->getQuery('var', "SELECT navbarstatus from $draupnir_table WHERE id = %s", $id) == 'override') ? 'unchecked' : $navbarstatus;
			$navbardate = isset($_POST['draupnir_sitenavstatus']) == TRUE ? date("Y-m-d H:i:s") : $this->getQuery('var', " SELECT navbardate from $draupnir_table WHERE id = %s", $id);
	  		$rows_affected = $wpdb->update( $draupnir_table, array( 'owner' => $owner, 'uri' => $uri, 'codeuri' => $codeuri, 'navbarstatus' => $navbarstatus, 'navbardate' => $navbardate, 'title' => $name, 'description' => $description, 'status' => $status ), array( 'id' => $id ) );
			echo '<div id="message" class="updated"><p>'.$name.' has been updated.</p></div>';
			$ringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
	  		$this->draupnir_check();
	}
	$replace = array (
		"#RINGNAME#" => isset($options['ringname']) ? $options['ringname'] : 'Unnamed Ring',
		"#RINGHUB#" => $options['ringhome'],
		"#RINGID#" => $ringpage->id,
		"#RINGIMAGE#" => $options['ring_image']
		);
  	$replacedringcode = str_replace(array_keys($replace), array_values($replace),$options['ringcode']);
	echo '<div class="wrap">
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="25%">Site Name: </td>
	<td width="75%"><input type="text" name="draupnir_sitename" value="'.stripslashes($ringpage->title).'" /></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Url: </td>
	<td width="75%"><input type="text" name="draupnir_siteurl" value="'.$ringpage->uri.'" /></td>
	</tr>
	<tr class="light">
	<td width="25%">Code Url: </td>
	<td width="75%"><input type="text" name="draupnir_codeuri" value="'.$ringpage->codeuri.'" /></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Description: </td>
	<td width="75%"><textarea rows=4 cols=50 name="draupnir_sitedescrip">'.stripslashes($ringpage->description).'</textarea></td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Status: </td>
	<td width="75%">
	<select name="draupnir_sitestatus">
  		<option value="inactive" '.selected( $ringpage->status, 'inactive', false ).'>Inactive</option>
  		<option value="suspended" '.selected( $ringpage->status, 'suspended', false ).'>Suspended</option>
  		<option value="hibernating" '.selected( $ringpage->status, 'hibernating', false ).'>Hibernating</option>
  		<option value="active" '.selected( $ringpage->status, 'active', false ).'>Active</option>
	</select>
	</td>
	</tr>
	<tr class="light">
	<td width="25%">Navigation Code Check: </td>
	<td width="75%">
	<input type="checkbox" name="draupnir_sitenavstatus" value="override" '.checked($ringpage->navbarstatus, "override", false).'>Override Check<br>
	</td>
	</tr>
	<tr class="dark">
	<td width="25%">Site Owner: </td>
	<td width="75%">'.wp_dropdown_users(array('selected'=>$ringpage->owner,'name'=>'draupnir_siteowner', 'echo'=>false)).'</td>
	</tr>
	<tr class="light">
	<td width="25%">Code to Use:</td>
	<td width="75%"><textarea rows="50" cols="80" name="draupnir_sitehtml" style="width: 400px; height:400px !important;">'.htmlentities(stripslashes($replacedringcode)).'</textarea></td>
	</tr>
	<tr class="dark">
	<td width="75%">Preview:</td>
	<td width="25%"><p>'.stripslashes($replacedringcode).'</p></td>
	</tr>
	</table>
	';}
	} else {
	  if (isset($_GET['check'])) {$this->draupnir_codecheck($_GET['check']);echo '<div id="message" class="updated"><p>Ring code status updated.</p></div>';}
	  if (isset($_GET['randomize'])) {$this->draupnir_randomize();echo '<div id="message" class="updated"><p>Webring order has been randomized.</p></div>';}
	  if (isset($_GET['shuffle1']) && isset($_GET['shuffle2'])) {$this->draupnir_swap($_GET['shuffle1'], $_GET['shuffle2']);echo '<div id="message" class="updated"><p>Webring order has been updated.</p></div>';}
	  $ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table ORDER BY ringorder");
	  $ringcount = count($ringpages);
	  if ($ringpages) 
	  {
	  if($_POST['Submit']){
			$options['reorder_sched'] = isset($_POST['draupnir_schedule']) ? $_POST['draupnir_schedule'] : 'never';
			$options['reorder_method'] = isset($_POST['draupnir_reorder_method']) ? $_POST['draupnir_reorder_method'] : 'random';
			wp_clear_scheduled_hook( 'draupnir_scheduled_event_hook' );
			switch ($options['reorder_sched']) {
			
			case never:
			$when = time();
			break;
			
			case daily:
			$when = time()+86400;
			break;
			
			case weekly:
			$when = time()+604800;
			break;
			
			case fortnight:
			$when = time()+1209600;
			break;
			
			case monthly:
			$when = time()+2592000;
			break;
			}
			wp_schedule_event( $when, $options['reorder_sched'], 'draupnir_scheduled_event_hook' );
			echo '<div id="message" class="updated"><p>Schedule updated.</p></div>';
			update_option('plugin_draupnir_settings', $options);
	}
	echo '<p><a href="?page=Draupnir&tab=addsite">Add a Site</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?page=Draupnir&tab=management&randomize=yes">Shuffle Sites</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Rearrange Ring Order: <select style="height: 28px; box-shadow: inset 0 1px 2px rgba(0,0,0,.07); width:200px; max-width:200px; max-height: 28px; background-color: white; text-size: 12pt;" name="draupnir_reorder_method"><option value="random" '.selected( $options['reorder_method'], 'random', false ).'>Random</option><option value="most active" '.selected($options['reorder_method'], 'most active', false ).'>Most Active First</option><option value="least active" '.selected( $options['reorder_method'], 'least active', false ).'>Least Active First</option></select><select style="height: 28px; box-shadow: inset 0 1px 2px rgba(0,0,0,.07); width:200px; max-width:200px; max-height: 28px; background-color: white; text-size: 12pt;" name="draupnir_schedule"><option value="never" '.selected( $options['reorder_sched'], 'never', false ).'>Never</option><option value="daily" '.selected( $options['reorder_sched'], 'daily', false ).'>Once Daily</option><option value="weekly" '.selected( $options['reorder_sched'], 'weekly', false ).'>Once Weekly</option><option value="fortnight" '.selected( $options['reorder_sched'], 'fortnight', false ).'>Once Every Two Weeks</option><option value="monthly" '.selected( $options['reorder_sched'], 'monthly', false ).'>Once Monthly</option></select></p>
	<table border="0" cellspacing="0" cellpadding="6" style="width:80%;">
	<tr><th>ID</th><th>Site Name</th><th>Site Owner</th><th>Ring Order</th><th>Status</th><th>Date Joined</th><th>Edit</th><th>Delete</th><th><div class="dashicons dashicons-arrow-up"></div></th><th><div class="dashicons dashicons-arrow-down"></div></th><th>Code Status</th><th>Date Checked</th><th>Check Now</th><th>Stats</th></tr>
	';
		$currentrow = 0;
		foreach ($ringpages as $ringpage) 
		{
		  $currentrow++;
		  if ($currentrow < $ringcount) {$nextpage = next($ringpages);}
		  $currentclass = ($currentrow & 1) ? 'light' : 'dark';
		  echo '<tr class="'.$currentclass.'"><td>'.$ringpage->id.'</td><td><a href="'.$ringpage->uri.'">'.stripslashes($ringpage->title).'</a></td><td>'.get_userdata($ringpage->owner)->display_name.'</td><td>'.$ringpage->ringorder.'</td><td>'.$ringpage->status.'</td><td>'.$ringpage->date.'</td><td><a href="?page=Draupnir&tab=management&id='.$ringpage->id.'">Edit</a></td><td><a href="?page=Draupnir&tab=management&id='.$ringpage->id.'&destroy=yes">Delete</a></td><td>';
echo ($currentrow > 1) ? '<a class="dashicons dashicons-arrow-up" href="?page=Draupnir&tab=management&shuffle1='.$ringpage->id.'&shuffle2='.$previouspage->id.'"></a>' : '';
echo '</td><td>';
echo ($currentrow < $ringcount) ? '<a class="dashicons dashicons-arrow-down"href="?page=Draupnir&tab=management&shuffle1='.$ringpage->id.'&shuffle2='.$nextpage->id.'"></a>' : '';
echo '</td>';
echo $ringpage->navbarstatus == 'found' ? '<td style="background-color:green;">Passed</td>' : '';
echo $ringpage->navbarstatus == 'override' ? '<td style="background-color:grey;">Overrode</td>' : '';
echo $ringpage->navbarstatus == 'not found' ? '<td style="background-color:red;">Failed</td>' : '';
echo $ringpage->navbarstatus == 'unchecked' ? '<td style="background-color:yellow;">Unchecked</td>' : '';
echo $ringpage->navbarstatus == 'error' ? '<td style="background-color:firebrick;">Unavailable</td>' : '';
echo $ringpage->navbardate == NULL ? '<td>Not Checked</td>' : '<td>'.$ringpage->navbardate.'</td>';
echo '<td><a href="?page=Draupnir&tab=management&check='.$ringpage->id.'">Check</a></td>';
echo '<td><a href="?page=Draupnir&tab=stats&id='.$ringpage->id.'">Stats</a></td></tr>';
		$previouspage = $ringpage;}
	echo '</table>';
	  } else {echo 'No Ring Pages Found.';}}
	$output = ob_get_clean();
	return $output;
	break;
	
	default:
	ob_start();
	if($_POST['Submit']){
        	$draupnir_ringname = $_POST['draupnir_ringname'];
        	$draupnir_ringdescrip = $_POST['draupnir_ringdescrip'];
        	$draupnir_rings = $_POST['draupnir_rings'];
        	$draupnir_ringcode = !empty($_POST['draupnir_ringcode']) ? $_POST['draupnir_ringcode'] : $defaultringcode;
		  	$draupnir_ringhome = $_POST['draupnir_ringhome'];
			$draupnir_ring_image = $_POST['draupnir_ring_image'];
			$options['ringname'] = $draupnir_ringname;
			$options['ringdescrip'] = $draupnir_ringdescrip;
			$options['rings'] = $draupnir_rings;
			$options['ringcode'] = $draupnir_ringcode;
			$options['ringhome'] = $draupnir_ringhome;
			$options['ring_image'] = $draupnir_ring_image;
			update_option('plugin_draupnir_settings', $options);
			echo '<div id="message" class="updated"><p>Update Successful!</p></div>';
		}
	$replace = array (
		"#RINGNAME#" => isset($options['ringname']) ? $options['ringname'] : 'Unnamed Ring',
		"#RINGHUB#" => $options['ringhome'],
		"#RINGID#" => "1",
		"#RINGIMAGE#" => $options['ring_image']
		);
	$replacedringcode = str_replace(array_keys($replace), array_values($replace),$options['ringcode']);
	echo '
	<script language="JavaScript">
	jQuery(document).ready(function() {
	jQuery("#upload_image_button").click(function() {
	formfield = jQuery("#upload_image").attr("name");
	tb_show("", "media-upload.php?type=image&TB_iframe=true");
	return false;
	});

	window.send_to_editor = function(html) {
	imgurl = jQuery("img",html).attr("src");
	jQuery("#upload_image").val(imgurl);
	tb_remove();
	}

	});
	</script>
	<table border="0" cellspacing="0" cellpadding="6">
	<tr class="light">
	<td width="75%">Webring Name: </td>
	<td width="25%"><input type="text" name="draupnir_ringname" value="'.stripslashes($options['ringname']).'" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Webring Description: </td>
	<td width="25%"><textarea rows=4 cols=50 name="draupnir_ringdescrip">'.stripslashes($options['ringdescrip']).'</textarea></td>
	</tr>
	<tr class="light">
	<td width="75%">Code for All Webrings: </td>
	<td width="25%"><textarea rows=5 cols=80 name="draupnir_rings">'.stripslashes($options['rings']).'</textarea></td>
	</tr>
	<tr class="dark">
	<td width="75%">Code for This Webring: </td>
	<td width="25%"><textarea rows=5 cols=80 name="draupnir_ringcode">'.stripslashes($options['ringcode']).'</textarea></td>
	</tr>
	<tr class="light">
	<td width="75%">Code to Use:</td>
	<td width="25%"><pre>'.htmlentities(stripslashes($replacedringcode)).'</pre></td>
	</tr>
	<tr class="dark">
	<td width="75%">Preview:</td>
	<td width="25%"><p>'.stripslashes($replacedringcode).'</p></td>
	</tr>
	<tr class="light">
	<td width="75%">Webring Homepage: </td>
	<td width="25%"><input type="text" name="draupnir_ringhome" value="'.$options['ringhome'].'" /></td>
	</tr>
	<tr class="dark">
	<td width="75%">Ring Image:<br />#RINGIMAGE# </td>
	<td width="25%">
	<label for="upload_image">
	<input id="upload_image" type="text" size="36" name="draupnir_ring_image" value="'.$options['ring_image'].'" />
	<input id="upload_image_button" type="button" value="Upload Image" />
	</label>
	</td>	
	</tr>
	</table>';
	$output = ob_get_clean();
	return $output;
	break;
	}
update_option('plugin_draupnir_settings', $options);
}

// Function to display admin page header.
function draupnir_menudisplay(){
		$options = get_option('plugin_draupnir_settings');
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';
		$output = '' . $this->draupnir_menu_tabs() . '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">'.stripslashes($options['css_replaced']).'<div class="wrap">
	<h2><span class="draupnir_admin_header"></span> Draupnir Webring Manager Options</h2>
    Plugin by <strong><a href="http://www.dreamhart.org" target="_blank" class="author">Jarandhel Dreamsinger</a></strong> || <strong><a href="http://www.dreamhart.org" target="_blank" class="author">Visit Author\'s Home Page</a></strong>';
    $output .= $options['ringhome'] == '' ? '' : ' || <strong><a href="'.$options['ringhome'].'" class="author">Visit Ring Hub</a></strong>';
    $output .= '
	<br /> <br />
	<p>This plugin allows for the creation and management of a webring, as well as the display of webring code in posts, pages, or a designated widget.  These functions may be used together or seperately.
    </p>
    ' . $this->draupnir_showtabs() . '
	<input type="submit" name="Submit" class="button-primary" style="float:left" value="Update Options &raquo;" />
	</form>
	</div>';
        echo $output;
    }

// Function to swap the order of two sites in the ring.
function draupnir_swap($id1,$id2) {
	global $wpdb;
	global $wp_query;
	$draupnir_table = $wpdb->prefix."draupnir";
	$ringpage1 = $this->getQuery('var', "SELECT ringorder FROM $draupnir_table WHERE id = %s", $id1);
	$ringpage2 = $this->getQuery('var', "SELECT ringorder FROM $draupnir_table WHERE id = %s", $id2);
	$wpdb->update( $draupnir_table, array( 'ringorder' => $ringpage2 ), array( 'id' => $id1 ) );
	$wpdb->update( $draupnir_table, array( 'ringorder' => $ringpage1 ), array( 'id' => $id2 ) );
}

// Function to reorder the ring randomly.
function draupnir_randomize() {
	global $wpdb;
	global $wp_query;
	$draupnir_table = $wpdb->prefix."draupnir";
	$count = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_table WHERE status = 'active'");
	for ($x=1; $x<=$count; $x++) {
	$first = $this->getQuery('var', "SELECT id FROM $draupnir_table WHERE status = 'active' ORDER BY RAND() LIMIT 1");
	$second = $this->getQuery('var', "SELECT id FROM $draupnir_table WHERE status = 'active' ORDER BY RAND() LIMIT 1");
	$this->draupnir_swap($first, $second);
	}
}

// Function to check sites for ring code.
function draupnir_codecheck($id) {
  $options = get_option('plugin_draupnir_settings');
  global $wpdb;
  global $wp_query;
  $draupnir_table = $wpdb->prefix."draupnir";
  $myringpage = $this->getQuery('row', "SELECT * FROM $draupnir_table WHERE id = %s", $id);
  if ($myringpage->navbarstatus == 'override') {return;}
  $url = $myringpage->codeuri == '' ? $myringpage->uri : $myringpage->codeuri;
  $input = @file_get_contents($url) or $input = "Could not access file: $url";
if (strpos($input, "Could not access file") !== FALSE) { 
$codestatus = 'error';
$date = date("Y-m-d H:i:s");
$wpdb->update( $draupnir_table, array( 'navbarstatus' => $codestatus, 'navbardate' => $date ), array( 'id' => $id ) ); 
return; }
  $aretheyset[1] = (strpos($input, $options['ringhome']) !== FALSE) ? TRUE : FALSE;
  $aretheyset[2] = (strpos($input, $options['ringhome'].'?do=ADD&id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=ADD&amp;id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=ADD&#38;id='.$myringpage->id) !== FALSE) ? TRUE : FALSE;
  $aretheyset[3] = (strpos($input, $options['ringhome'].'?do=PREV&id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=PREV&amp;id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=PREV&#38;id='.$myringpage->id) !== FALSE) ? TRUE : FALSE;
  $aretheyset[4] = (strpos($input, $options['ringhome'].'?do=NEXT&id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=NEXT&amp;id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=NEXT&#38;id='.$myringpage->id) !== FALSE) ? TRUE : FALSE;
  $aretheyset[5] = (strpos($input, $options['ringhome'].'?do=RAND&id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=RAND&amp;id='.$myringpage->id) !== FALSE || strpos($input, $options['ringhome'].'?do=RAND&#38;id='.$myringpage->id) !== FALSE) ? TRUE : FALSE;
$codestatus = (in_array(FALSE, $aretheyset)) ? 'not found' : 'found';
$date = date("Y-m-d H:i:s");
// if () { echo '<div id="message" class="updated">'.array_values($aretheyset).'</p></div>';}  // For error messages.
$wpdb->update( $draupnir_table, array( 'navbarstatus' => $codestatus, 'navbardate' => $date ), array( 'id' => $id ) );
return;
}

// Function to automatically check sites for ring code once an hour.
function draupnir_do_this_hourly() {
	global $wpdb;
	global $wp_query;
	$draupnir_table = $wpdb->prefix."draupnir";
	$checkpage = $this->getQuery('var', "SELECT id FROM $draupnir_table WHERE navbarstatus != 'override' ORDER BY navbardate LIMIT 1"); 
$this->draupnir_codecheck($checkpage);
}

// Function to reorder the ring periodically.
function draupnir_scheduled_tasks() {
$options = get_option('plugin_draupnir_settings');
switch ($options['reorder_method']) {
case 'most active':
$this->draupnir_reorder('most');
break;

case 'least active':
$this->draupnir_reorder('least');
break;

case 'random':
$this->draupnir_randomize();
break;
}

}

// Function to accomplish reordering of the ring by most or least active sites.
function draupnir_reorder($method) {
	global $wpdb;
	global $wp_query;
	$draupnir_table = $wpdb->prefix."draupnir";
	$draupnir_stats_table = $wpdb->prefix."draupnir_stats";
	$ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table");
	switch ($method) {
	case 'most':
	$siteorder = $this->getQuery('results', "SELECT clickto AS site, COUNT(id) AS total FROM $draupnir_stats_table group by site order by total DESC"); 
	break;
	
	case 'least':
	$siteorder = $this->getQuery('results', "SELECT clickto AS site, COUNT(id) AS total FROM $draupnir_stats_table group by site order by total ASC");
	break;
	}
	$ringorder = 1;
	foreach ($siteorder AS $site) {
	if ($this->getQuery('var', "SELECT status FROM $draupnir_table WHERE id = %s", $site->site) == "active") {$wpdb->update($draupnir_table, array( 'ringorder' => $ringorder), array( 'id' => $site->site )); $ringorder++;}
	}	
}

// Function to display ring statistics.
function draupnir_ringstats() {
global $wpdb;
global $wp_query;

$siteid = isset($_GET['id']) == TRUE ? $_GET['id'] : NULL;
$sitequery = isset($siteid) == TRUE ? "clickto = %s AND" : '';
$draupnir_table = $wpdb->prefix."draupnir";
$draupnir_stats_table = $wpdb->prefix."draupnir_stats";
$ringpages = $this->getQuery('results', "SELECT * FROM $draupnir_table");
$ringstats = $this->getQuery('results', "SELECT * FROM $draupnir_stats_table");
$olddata = $this->getQuery('results', "SELECT * FROM $draupnir_stats_table WHERE clickfrom != NULL AND clicktime < date_sub(CURRENT_DATE, INTERVAL 8 WEEK)");
foreach ($olddata as $oldclick) {
$wpdb->delete($draupnir_ring_stats, array( 'id' => $oldclick->id ));
}
$totalhits3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickto != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$totalhits2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickto != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$totalhits8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickto != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$prevhits3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$prevhits2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$prevhits8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$nexthits3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$nexthits2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$nexthits8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$randhits3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$randhits2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$randhits8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$gohits3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'GO' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$gohits2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'GO' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$gohits8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'GO' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);

echo $stylehtml;
echo '<table class="draupnir_stats">';
echo '<tr class="dark"><th>Hits:</th><th>Three Days</th><th>Two Weeks</th><th>Eight Weeks</th></tr>';
echo '<tr class="light"><td>Total Hits:</td><td>'.$totalhits3days.'</td><td>'.$totalhits2weeks.'</td><td>'.$totalhits8weeks.'</td></tr>';
echo '<tr class="dark"><td>Previous:</td><td>'.$prevhits3days.'</td><td>'.$prevhits2weeks.'</td><td>'.$prevhits8weeks.'</td></tr>';
echo '<tr class="light"><td>Next:</td><td>'.$nexthits3days.'</td><td>'.$nexthits2weeks.'</td><td>'.$nexthits8weeks.'</td></tr>';
echo '<tr class="dark"><td>Random:</td><td>'.$randhits3days.'</td><td>'.$randhits2weeks.'</td><td>'.$randhits8weeks.'</td></tr>';
echo '<tr class="light"><td>Direct:</td><td>'.$gohits3days.'</td><td>'.$gohits2weeks.'</td><td>'.$gohits8weeks.'</td></tr>';
echo '</table>';
$totalclicks3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickfrom != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$totalclicks2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickfrom != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$totalclicks8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clickfrom != '' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$prevclicks3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$prevclicks2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$prevclicks8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'PREV' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$nextclicks3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$nextclicks2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$nextclicks8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'NEXT' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
$randclicks3days = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY)", $siteid);
$randclicks2weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK)", $siteid);
$randclicks8weeks = $this->getQuery('var', "SELECT COUNT(*) FROM $draupnir_stats_table WHERE $sitequery clicktype = 'RAND' AND clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK)", $siteid);
echo '<table class="draupnir_stats">';
echo '<tr class="dark"><th>Clicks:</th><th>Three Days</th><th>Two Weeks</th><th>Eight Weeks</th></tr>';
echo '<tr class="light"><td>Total Clicks:</td><td>'.$totalclicks3days.'</td><td>'.$totalclicks2weeks.'</td><td>'.$totalclicks8weeks.'</td></tr>';
echo '<tr class="dark"><td>Previous:</td><td>'.$prevclicks3days.'</td><td>'.$prevclicks2weeks.'</td><td>'.$prevclicks8weeks.'</td></tr>';
echo '<tr class="light"><td>Next:</td><td>'.$nextclicks3days.'</td><td>'.$nextclicks2weeks.'</td><td>'.$nextclicks8weeks.'</td></tr>';
echo '<tr class="dark"><td>Random:</td><td>'.$randclicks3days.'</td><td>'.$randclicks2weeks.'</td><td>'.$randclicks8weeks.'</td></tr>';
echo '</table>';

if (isset($_GET['id']) === FALSE) {
$top8 = $this->getQuery('results', "SELECT clickto AS site, COUNT(id) AS total FROM $draupnir_stats_table WHERE clicktime >= date_sub(CURRENT_DATE, INTERVAL 8 WEEK) group by site order by total DESC LIMIT 10");
$top2 = $this->getQuery('results', "SELECT clickto AS site, COUNT(id) AS total FROM $draupnir_stats_table WHERE clicktime >= date_sub(CURRENT_DATE, INTERVAL 2 WEEK) group by site order by total DESC");
$top3 = $this->getQuery('results', "SELECT clickto AS site, COUNT(id) AS total FROM $draupnir_stats_table WHERE clicktime >= date_sub(CURRENT_DATE, INTERVAL 3 DAY) group by site order by total DESC");
echo '<table class="draupnir_stats2">';
echo '<tr class="dark"><th>Site Name</th><th>Three Days</th><th>Two Weeks</th><th>Eight Weeks</th></tr>';
$thistopsite = 1;
foreach ($top8 as $topsite) {
$sitename = '';
$site3 = '';
$site2 = '';
foreach ($ringpages as $ringpage) {
if ($ringpage->id == $topsite->site) {$sitename=$ringpage->title;}
}
foreach ($top3 as $top) {
if ($topsite->site == $top->site) {$site3=$top->total;}
}
foreach ($top2 as $thetop) {
if ($topsite->site == $thetop->site) {$site2=$thetop->total;}
}
$thecurrenturl = "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
echo '<tr class="light"><td>'.$thistopsite++.'. <a href="'.$thecurrenturl.'&id='.$topsite->site.'">'.stripslashes($sitename).'</a></td><td>'.$site3.'</td><td>'.$site2.'</td><td>'.$topsite->total.'</td></tr>';
}
echo '</table>';
}
}

// Function to check if the site being accessed through the ring is currently available.
function draupnir_lookahead($url, $timeout = 30) {
	$ch = curl_init(); // get cURL handle

	// set cURL options
	$opts = array(CURLOPT_RETURNTRANSFER => true, // do not output to browser
				  CURLOPT_URL => $url,            // set URL
				  CURLOPT_NOBODY => true, 		  // do a HEAD request only
				  CURLOPT_TIMEOUT => $timeout,   // set timeout
				  CURLOPT_FOLLOWLOCATION => true, // follow redirects
				  CURLOPT_MAXREDIRS => 3); // maximum redirects 
	curl_setopt_array($ch, $opts); 

	curl_exec($ch); // do it!

	$retval = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200; // check if HTTP OK

	curl_close($ch); // close handle

	return $retval;
}

function getQuery() {
global $wpdb;
global $wp_query;
$draupnir_table = $wpdb->prefix."draupnir";
$draupnir_stats_table = $wpdb->prefix."draupnir_stats";
$args = func_num_args();
$theargs = func_get_args();
if ($args <= 1) {
die("Invalid query");
}
$query[1] = func_get_arg(1);
$i = 2;
if ($args >= 3) {
for ($x=2; $x < $args; $x++) {
if (func_get_arg($x) !== NULL && func_get_arg($x) !== '') {
$query[$i] = func_get_arg($x);
$i++;}
}
}

if (!array_key_exists(2, $query)) {$preparedquery = $query[1];} else {$preparedquery = call_user_func_array(array($wpdb, 'prepare'), $query);}

switch (func_get_arg(0)) {
case 'var':
return $wpdb->get_var($preparedquery);
break;

case 'results':
return $wpdb->get_results($preparedquery);
break;

case 'row':
return $wpdb->get_row($preparedquery);
break;

case 'column':
return $wpdb->get_column($preparedquery);
break;

case 'query':
return $wpdb->query($preparedquery);
break;
}
}
}
new Draupnir_Ringmanager_Plugin();
?>
