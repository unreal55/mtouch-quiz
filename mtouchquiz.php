<?php
/*
Plugin Name: mTouch Quiz
Plugin URI: http://gmichaelguy.com/quizplugin/
Description: Create a multiple choice quiz (or exam). This plugin was written with learning and mobility at the forefront of design decisions.  The quiz interface is very finger friendly and allows for easy touch screen use. You can specify feedback (hints) based on answer selection, as well as give a detailed explanation of the problem. You can choose multiple correct answers and specify when the correct answers are displayed. You can specify if a question may be attempted only once or many times and specify point values for each question. You can include customized start and finish screens. You can randomly order questions and/or answers. All this, and more.  Built by pillaging the Quizzin plugin written by Binny V A, but please do not blame him for my ruining his plugin!
Version: 1.04
Author: G. Michael Guy
Author URI: http://gmichaelguy.com
License: GPL2
Text Domain: mtouchquiz
*/
?>
<?php
/*  Copyright 2010  G. Michael Guy  (email : Michael (Put-AN-AT) gmichaelguy.com)

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
?>
<?php
/**
 * Add a new menu page, visible for all users with template viewing level.
 */
 
define( 'mtouchquiz_VERSION', '1.04' );
define( 'mtouchquiz_URL','http://gmichaelguy.com/quizplugin/');
define( 'mtouchquiz_DISPLAY_NAME','mTouch Quiz');
add_action( 'admin_menu', 'mtouchquiz_add_menu_links' );
function mtouchquiz_add_menu_links() {
	global $wp_version, $_registered_pages;
	$view_level= 'upload_files';
	//$page = 'edit.php';
	//if($wp_version >= '2.7') $page = 'tools.php';
	
	add_menu_page(__('mTouch Quiz', 'mtouchquiz'), __('mTouch Quiz', 'mtouchquiz'), $view_level, 'mtouch_menu','mtouchquiz_plugin_options' , plugins_url('mtouch-quiz/images/menu-icon.png'));

	add_submenu_page('mtouch_menu', __('Manage mTouch Quizzes', 'mtouchquiz'), __('Manage Quizzes', 'mtouchquiz'), $view_level, 'mtouch-quiz/quiz.php');
	$code_pages = array('quiz_form.php','quiz_action.php', 'question_form.php', 'question.php');
	foreach($code_pages as $code_page) {
		$hookname = get_plugin_page_hookname("mtouch-quiz/$code_page", '' );
		$_registered_pages[$hookname] = true;
	}
}

/// Initialize this plugin. Called by 'init' hook.
add_action('init', 'mtouchquiz_init');
function mtouchquiz_init() {
	load_plugin_textdomain('mtouchquiz', 'wp-content/plugins/mtouch-quiz/lang/' );
	add_action('admin_menu', 'mtouchquiz_menu');
}


/**
 * Add Settings link to plugins - code from GD Star Ratings
 */

function add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=mtouchquiz">'.__("Settings", "mtouchquiz").'</a>';
 		array_unshift($links, $settings_link);
	}
	return $links;
}


add_filter('plugin_action_links', 'add_settings_link', 10, 2 );

add_action('admin_menu', 'mtouchquiz_options');
function mtouchquiz_options()
{
require('wpframe.php');
function mtouchquiz_menu() {
    add_options_page(__('mTouch Quiz Plugin Options', 'mtouchquiz'), __('mTouch Quiz Plugin', 'mtouchquiz'), 'manage_options', 'mtouchquiz', 'mtouchquiz_plugin_options');
  }
  
 function mtouchquiz_plugin_options() {
      //if (!current_user_can('manage_options'))  {
      if (!current_user_can('upload_files'))  {
	    wp_die( __('You do not have sufficient permissions to access this page.','mtouchquiz') );
      }
echo '<div class="wrap" id="mtouchquiz-options">
<h2>mTouch Quiz Plugin Options</h2>
';
    if ($_POST['mtouchquiz_hidden'] == 'Y') {
        //process form
        update_option('mtouchquiz_leftdelimit', $_REQUEST['left_delimiter']);
		update_option('mtouchquiz_rightdelimit', $_REQUEST['right_delimiter']);
		if(!empty($_POST['showalerts'])) {
			update_option('mtouchquiz_showalerts', $_POST['showalerts']);
		} else 
		{
			update_option('mtouchquiz_showalerts', 0);
		}
		wpframe_message(__('Options updated'));   
    }
?>

<form id="mtouchquiz" name="mtouchquiz" action="" method='POST'>
  <input type="hidden" name="mtouchquiz_hidden" value="Y">
  <table class="form-table">
    <tr valign="middle">
      <th scope="row"><?php _e("Left Delimiter"); ?><br/>
        <font size="-2"><?php _e("Left delimiter used when box is checked next to answer input."); ?></font></th>
      <td><input type="textbox" name="left_delimiter" value="<?php echo stripslashes(get_option('mtouchquiz_leftdelimit')) ?>"/></td>
    </tr>
    <tr valign="middle">
      <th scope="row"><?php _e("Right Delimiter"); ?><br/>
        <font size="-2"><?php _e("Right delimiter used when box is checked next to answer input."); ?></font></th>
      <td><input type="textbox" name="right_delimiter" value="<?php echo stripslashes(get_option('mtouchquiz_rightdelimit')) ?>" /></td>
    </tr>
    
       <tr valign="middle">
      <th scope="row"><?php _e("Show Alerts if Quiz unfinished?"); ?><br/>
        <font size="-2"><?php _e("Since results to the quiz are stored locally, leaving the quiz page will lose all progress."); ?></font></th>
      <td><?php showOption('showalerts', __('Display a warning before a user leaves an unfinished quiz.')); ?></td>
    </tr>
  </table>
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
</form>
</div>
<?php
  }
}

function showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('mtouchquiz_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php _e($title) ?></label><br />
<?php
}

/**
 * This will scan all the content pages that wordpress outputs for our special code. If the code is found, it will replace the requested quiz.
 */

add_shortcode( 'mtouchquiz', 'mtouchquiz_shortcode' );

function mtouchquiz_shortcode( $atts ) {
	extract( shortcode_atts( array(
      'id' => -1,
	  'questions' => -1,
	  'randomq' => -1,
	  'randoma' => -1,
	  'singlepage' => -1,
	  'multiplechances' => -1,
	  'hints' => -1,
	  'startscreen' => -1,
	  'finalscreen' => -1,
	  'showanswers' => -1,
	  'display' => 1,
	  'nav' => -1
      ), $atts ) );
	$quiz_id = -1;
	$input_number_questions = -1;
	$input_randomq = -1;
	$input_randoma = -1;
	$input_singlepage = -1;
	$input_multiplechances = -1;
	$input_hints = -1;
	$input_startscreen = -1;
	$input_finalscreen = -1;
	$input_showanswers = -1;
	$display_number = 1;
	$show_nav = 1;
	
	if  (! isset($atts['id'])){
		$quiz_id = $atts[0];
	} else {
		$quiz_id = $atts['id'];
	}

	
	if ( isset( $atts['questions']) ){
		$input_number_questions = $atts['questions'];
	}
	
	if ( isset( $atts['randomq']) ){
		$input_randomq = $atts['randomq'];
	}
	
	if ( isset( $atts['randoma']) ){
		$input_randoma = $atts['randoma'];
	}
	
	if ( isset( $atts['singlepage']) ){
		$input_singlepage = $atts['singlepage'];
	}
	
	if ( isset( $atts['multiplechances']) ){
		$input_multiplechances = $atts['multiplechances'];
	}
	
	if ( isset( $atts['hints']) ){
		$input_hints = $atts['hints'];
	}
	
	if ( isset( $atts['startscreen']) ){
		$input_startscreen = $atts['startscreen'];
	}
	
	if ( isset( $atts['finalscreen']) ){
		$input_finalscreen = $atts['finalscreen'];
	}
	
	if ( isset( $atts['showanswers']) ){
		$input_showanswers = $atts['showanswers'];
	}
	
	if ( isset( $atts['display']) && is_numeric($atts['display']) && $atts['display'] > 1 ){
		$display_number = $atts['display'];
	}
	if ( isset( $atts['nav']) && $atts['nav']== 'off' ){
		$show_nav = 0;
	}

	wp_enqueue_script("jquery");
	wp_enqueue_script('script.js', WP_CONTENT_URL . '/plugins/mtouch-quiz/script.js');
	$contents = '';
	if(is_numeric($quiz_id)) { // Basic validiation - more on the show_quiz.php file.
		ob_start();
		include(ABSPATH . 'wp-content/plugins/mtouch-quiz/show_quiz.php');
		$contents = ob_get_contents();
		ob_end_clean();
	}
	return $contents;
}

 

add_action('activate_mtouch-quiz/mtouchquiz.php','mtouchquiz_activate');
function mtouchquiz_activate() {
	global $wpdb;
	
	$database_version = '1.0';
	$installed_db = get_option('mtouchquiz_db_version');
	// Initial options.
	 //add_option('mtouchquiz_show_answers', 1);
	 //add_option('mtouchquiz_single_page', 0);
	 add_option('mtouchquiz_leftdelimit', "\\\(\\\displaystyle{");
     add_option('mtouchquiz_rightdelimit', "}\\\)");
	 add_option('mtouchquiz_showalerts', "1");
	
	if($database_version != $installed_db) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		$sql = "CREATE TABLE {$wpdb->prefix}mtouchquiz_answer (
					ID int(11) unsigned NOT NULL auto_increment,
					question_id int(11) unsigned NOT NULL,
					answer varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					hint varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					correct enum('0','1') NOT NULL default '0',
					sort_order int(3) NOT NULL default 0,
					PRIMARY KEY  (ID)
				);
				CREATE TABLE {$wpdb->prefix}mtouchquiz_ratings (
					ID int(11) unsigned NOT NULL auto_increment,
					quiz_id int(11) unsigned NOT NULL,
					score_rating varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					min_points int(3) NOT NULL default 0,
					PRIMARY KEY  (ID)
				);
				CREATE TABLE {$wpdb->prefix}mtouchquiz_question (
					ID int(11) unsigned NOT NULL auto_increment,
					quiz_id int(11) unsigned NOT NULL,
					question mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					sort_order int(3) NOT NULL default 0,
					point_value int(3) NOT NULL default 100,
					number_correct int(3) NOT NULL default 1,
					explanation mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					PRIMARY KEY  (ID),
					KEY quiz_id (quiz_id)
				);
				CREATE TABLE {$wpdb->prefix}mtouchquiz_quiz (
					ID int(11) unsigned NOT NULL auto_increment,
					name varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					description mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					final_screen mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					added_on datetime NOT NULL,
					show_hints enum('0','1') NOT NULL default '1',
					show_start enum('0','1') NOT NULL default '1',
					show_final enum('0','1') NOT NULL default '1',
					random_questions enum('0','1') NOT NULL default '0',
					random_answers enum('0','1') NOT NULL default '0',
					multiple_chances enum('0','1') NOT NULL default '1',
					single_page enum('0','1') NOT NULL default '0',
					answer_mode enum('0','1','2') NOT NULL default '2',
					PRIMARY KEY  (ID)
				);";
		dbDelta($sql);
		update_option( "mtouchquiz_db_version", $database_version );
	}
}

