<?php
/*
Plugin Name: mTouch Quiz
Plugin URI: http://gmichaelguy.com/quizplugin/
Description: Create a multiple choice quiz (or exam). This plugin was written with learning and mobility in mind.  The quiz interface is touch friendly. You can: specify hints based on answer selection; give a detailed explanation of the solution; choose multiple correct answers; specify when the correct answers are displayed; specify if a question may be attempted only once or many times; specify point values for each question; include customized start and finish screens; randomly order questions and/or answers; and more.  This plugin was built by pillaging the Quizzin plugin written by Binny V A, but please do not blame him for my ruining his plugin!
Version: 2.2.0
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
 
define( 'mtq_VERSION', '2.2.0' );
define( 'mtq_URL','http://gmichaelguy.com/quizplugin/');
define( 'mtq_DISPLAY_NAME','mTouch Quiz');
add_action( 'admin_menu', 'mtq_add_menu_links' );
function mtq_add_menu_links() {
	global $wp_version, $_registered_pages;
	$view_level= 'upload_files';
	//$page = 'edit.php';
	//if($wp_version >= '2.7') $page = 'tools.php';
	
	add_menu_page(__('mTouch Quiz', 'mtouchquiz'), __('mTouch Quiz', 'mtouchquiz'), $view_level, 'mtq_menu','mtq_plugin_options' , plugins_url('mtouch-quiz/images/menu-icon.png'));

	add_submenu_page('mtq_menu', __('Manage mTouch Quizzes', 'mtouchquiz'), __('Manage Quizzes', 'mtouchquiz'), $view_level, 'mtouch-quiz/quiz.php');
	$code_pages = array('quiz_form.php','quiz_action.php', 'question_form.php', 'question.php');
	foreach($code_pages as $code_page) {
		$hookname = get_plugin_page_hookname("mtouch-quiz/$code_page", '' );
		$_registered_pages[$hookname] = true;
	}
}

/// Initialize this plugin. Called by 'init' hook.
add_action('init', 'mtq_init');
function mtq_init() {
	load_plugin_textdomain('mtouchquiz', 'wp-content/plugins/mtouch-quiz/lang/' );
	add_action('admin_menu', 'mtq_menu');
}


/**
 * Add Settings link to plugins - code from GD Star Ratings
 */

function add_mtq_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=mtouchquiz">'.__("Settings", "mtouchquiz").'</a>';
 		array_unshift($links, $settings_link);
	}
	return $links;
}


add_filter('plugin_action_links', 'add_mtq_settings_link', 10, 2 );

add_action('admin_menu', 'mtq_options');
function mtq_options()
{
require('wpframe.php');
function mtq_menu() {
    add_options_page(__('mTouch Quiz Plugin Options', 'mtouchquiz'), __('mTouch Quiz Plugin', 'mtouchquiz'), 'upload_files', 'mtouchquiz', 'mtq_plugin_options');
  }
  
 function mtq_plugin_options() {
      //if (!current_user_can('manage_options'))  {
      if (!current_user_can('upload_files'))  {
	    wp_die( __('You do not have sufficient permissions to access this page.','mtouchquiz') );
      }
echo '<div class="wrap" id="mtouchquiz-options">
<h2>mTouch Quiz Plugin Options</h2>
';
    if ($_POST['mtq_hidden'] == 'Y') {
        //process form
        update_option('mtouchquiz_leftdelimit', $_REQUEST['left_delimiter']);
		update_option('mtouchquiz_rightdelimit', $_REQUEST['right_delimiter']);
		if(!empty($_POST['showalerts'])) {
			update_option('mtouchquiz_showalerts', $_POST['showalerts']);
		} else 
		{
			update_option('mtouchquiz_showalerts', 0);
		}
		
		//if(!empty($_POST['skiploadjquerytools'])) {
		//	update_option('mtouchquiz_skiploadjquerytools', $_POST['skiploadjquerytools']);
		//} else 
		//{
		//	update_option('mtouchquiz_skiploadjquerytools', 0);
		//}
		wpframe_message(__('Options updated', 'mtouchquiz'));   
    }
?>

<form id="mtouchquiz" name="mtouchquiz" action="" method='POST'>
  <input type="hidden" name="mtq_hidden" value="Y">
  <table class="form-table">
    <tr valign="middle">
      <th scope="row"><?php _e("Left Delimiter", 'mtouchquiz'); ?><br/>
        <font size="-2"><?php _e("Left delimiter used when box is checked next to answer input.", 'mtouchquiz'); ?></font></th>
      <td><input type="textbox" name="left_delimiter" value="<?php echo stripslashes(get_option('mtouchquiz_leftdelimit')) ?>"/></td>
    </tr>
    <tr valign="middle">
      <th scope="row"><?php _e("Right Delimiter", 'mtouchquiz'); ?><br/>
        <font size="-2"><?php _e("Right delimiter used when box is checked next to answer input.", 'mtouchquiz'); ?></font></th>
      <td><input type="textbox" name="right_delimiter" value="<?php echo stripslashes(get_option('mtouchquiz_rightdelimit')) ?>" /></td>
    </tr>
    
       <tr valign="middle">
      <th scope="row"><?php _e("Show Alerts if Quiz unfinished?", 'mtouchquiz'); ?><br/>
        <font size="-2"><?php _e("Since results to the quiz are stored locally, leaving the quiz page will lose all progress.", 'mtouchquiz'); ?></font></th>
      <td><?php mtq_showOption('showalerts', __('Display a warning before a user leaves an unfinished quiz.', 'mtouchquiz')); ?></td>
  </table>
  <!-- <?php _e('I will email my completed translation file to Michael at gmichaelguy.com so that others can benefit from my work. ;-)', 'mtouchquiz'); ?>-->
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'mtouchquiz') ?>" />
  </p>
</form>
</div>
<?php
  }
}

function mtq_showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('mtouchquiz_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php _e($title, 'mtouchquiz') ?></label><br />
<?php
}

/**
 * This will scan all the content pages that wordpress outputs for our special code. If the code is found, it will replace the requested quiz.
 */

add_shortcode( 'mtouchquiz', 'mtq_shortcode' );

function mtq_shortcode( $atts ) {
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
	  //'display' => 1,
	  'list' => -1,
	  'proofread' => -1,
	  'alerts' => -1,
	  'title' =>-1,
	  'labels' =>-1,
	  'status' => -1,
	  //'javawarning' => -1,
	  'offset'=>0,
	  'singlequestion'=>0
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
	$show_list = 0;
	$proofread = 0;
	$input_alerts = -1;
	
	$thetypedcode= "[[mtouchquiz";
	if  (! isset($atts['id'])){
		$quiz_id = $atts[0];	
	} else {
		$quiz_id = $atts['id'];
	}
	$thetypedcode.= " id=" . $quiz_id;
	
	if ( isset( $atts['questions']) ){
		$input_number_questions = $atts['questions'];
		$thetypedcode.= " questions=".$input_number_questions;
	}
	
	if ( isset( $atts['randomq']) ){
		$input_randomq = $atts['randomq'];
		$thetypedcode.= " randomq=".$input_randomq;
	}
	
	if ( isset( $atts['randoma']) ){
		$input_randoma = $atts['randoma'];
		$thetypedcode.= " randoma=".$input_randoma;
	}
	
	if ( isset( $atts['singlepage']) ){
		$input_singlepage = $atts['singlepage'];
		$thetypedcode.= " singlepage=".$input_singlepage;
	}
	
	if ( isset( $atts['multiplechances']) ){
		$input_multiplechances = $atts['multiplechances'];
		$thetypedcode.= " multiplechances=".$input_multiplechances;
	}
	
	if ( isset( $atts['hints']) ){
		$input_hints = $atts['hints'];
		$thetypedcode.= " hints=".$input_hints;
	}
	
	if ( isset( $atts['startscreen']) ){
		$input_startscreen = $atts['startscreen'];
		$thetypedcode.= " startscreen=".$input_startscreen;
	}
	
	if ( isset( $atts['finalscreen']) ){
		$input_finalscreen = $atts['finalscreen'];
		$thetypedcode.= " finalscreen=".$input_finalscreen;
	}
	
	if ( isset( $atts['showanswers']) ){
		$input_showanswers = $atts['showanswers'];
		$thetypedcode.= " showanswers=".$input_showanswers;
	}
	
	//if ( isset( $atts['display']) && is_numeric($atts['display']) && $atts['display'] > 1 ){
	//	$display_number = $atts['display'];
	//}
	
	$show_list = 1;
	if ( isset( $atts['list']) && $atts['list']== 'off' ){
		$show_list = 0;
		$thetypedcode.= " list=off";
	}
	
	if ( isset( $atts['alerts']) && $atts['alerts']== 'on' ){
		$input_alerts = 1;
		$thetypedcode.= " alerts=on";
	} elseif (isset( $atts['alerts']) && $atts['alerts']== 'off' ){
		$input_alerts = 0;
		$thetypedcode.= " alerts=off";
	}
	
	if ( isset( $atts['proofread'])  && $atts['proofread']== 'on' ){
		$proofread = 1;
		$thetypedcode.= " proofread=on";
	}
	
	$show_title = 1;
	if ( isset( $atts['title']) && $atts['title']== 'off' ){
		$show_title = 0;
		$thetypedcode.= " title=off";
	}
	
	$show_labels = 1;
	if ( isset( $atts['labels']) && $atts['labels']== 'off' ){
		$show_labels = 0;
		$thetypedcode.= " labels=off";
	}
	
	$show_status = 1;
	if ( isset( $atts['status']) && $atts['status']== 'off' ){
		$show_status = 0;
		$thetypedcode.= " status=off";
	}
	
	$offset_start = 1;
	if( isset( $atts['offset']) && is_numeric($atts['offset']) && $atts['offset'] > 1 ){
		$offset_start = $atts['offset'];
		$thetypedcode.= " offset=".$offset_start;
	}
	
	//$single_question = -1;
	if( isset( $atts['singlequestion']) && is_numeric($atts['singlequestion']) && $atts['singlequestion'] >= 1 ){
		$offset_start = $atts['singlequestion'];
		$input_randomq='off';
		$input_singlepage='on';
		$input_number_questions = 1;
		$thetypedcode.= " singlequestion=".$offset_start;
		$thetypedcode.= " offset=".$offset_start;
	}
	
	
	
	$thetypedcode.= "]]";
	$replace_these	= array('showanswers=0','showanswers=1','showanswers=2');
	$with_these = array ('showanswers=never','showanswers=end','showanswers=now');
	$thetypedcodee = str_replace($replace_these, $with_these,$thetypedcode);
	
	$contents = '';
	$mtq_mobile_device = mtq_is_mobile_device();
	if(is_numeric($quiz_id)) { // Basic validiation - more on the show_quiz.php file.
		ob_start();
		include(ABSPATH . 'wp-content/plugins/mtouch-quiz/show_quiz.php');
		$contents = ob_get_contents();
		ob_end_clean();
		
	}
	$switch_my_latex =false;
	if ( $mtq_mobile_device && $switch_my_latex ) {
		$contents=switch_latex($contents);	
	}
	return do_shortcode($contents);
}

function switch_latex($stuff){
	$replace_these	= array('\(', '\)','\[', '\]');
	$with_these		= array("[latex]",'[/latex]',"[latex]",'[/latex]');
	return str_replace($replace_these, $with_these, $stuff);
}

function mtq_is_mobile_device(){
		$return_value=false;
	
		$client_useragent=$_SERVER['HTTP_USER_AGENT'];
		//echo $client_useragent;
		
		$simple_mobile_detect= Array("iPhone","iPod","android","webOS");
		//echo $client_useragent;
		foreach ($simple_mobile_detect as $mobile_agent) {
			if (strpos($client_useragent,$mobile_agent)) {
				//echo "is mobile detected ".$mobile_agent	;
				$return_value=true;
			}
			else {
				//echo "not mobile".$mobile_agent;
			}	
		}
		
		return $return_value;	
}


add_action('init', 'mtq_enqueue_stuff');
function mtq_enqueue_stuff() {
	$mtq_use_min=false;
	//$mtq_use_min=false;
	if ( $mtq_use_min ) {
		$mtq_StyleUrl = WP_PLUGIN_URL . '/mtouch-quiz/style.min.css';
		$mtq_StyleFile = WP_PLUGIN_DIR . '/mtouch-quiz/style.min.css';
	} else {
		$mtq_StyleUrl = WP_PLUGIN_URL . '/mtouch-quiz/style.css';
		$mtq_StyleFile = WP_PLUGIN_DIR . '/mtouch-quiz/style.css';
	}
	 if ( file_exists($mtq_StyleFile)) {
		wp_register_style('mtq_StyleSheets', $mtq_StyleUrl,false,mtq_VERSION);
		wp_enqueue_style( 'mtq_StyleSheets');
     }
		
	$mtq_proofread_StyleUrl = WP_PLUGIN_URL . '/mtouch-quiz/proofread.min.css';
    $mtq_proofread_StyleFile = WP_PLUGIN_DIR . '/mtouch-quiz/proofread.min.css';
	 
	wp_enqueue_script("jquery");
	if ( $mtq_use_min ) {
		wp_enqueue_script('mtq_script', WP_CONTENT_URL . '/plugins/mtouch-quiz/script.min.js',array('jquery'),mtq_VERSION,false);
	} else {
		wp_enqueue_script('mtq_script', WP_CONTENT_URL . '/plugins/mtouch-quiz/script.js',array('jquery'),mtq_VERSION,false);
	}
	//if (! get_option('mtouchquiz_skiploadjquerytools'))  {
		wp_enqueue_script('mtq_scrollable', WP_CONTENT_URL . '/plugins/mtouch-quiz/scrollable.min.js',array('jquery'),mtq_VERSION,false);
	//}
	//wp_enqueue_script('jquerytools_full','http://cdn.jquerytools.org/1.2.5/full/jquery.tools.min.js','1.2.5',false);
}

add_filter('plugin_row_meta', 'mtq_filter_plugin_links', 10, 2);

// Add FAQ and contact information
function mtq_filter_plugin_links($links, $file)
{
	if ( $file == plugin_basename(__FILE__) )
	{
		$links[] = '<a href="http://gmichaelguy.com/quizplugin/go/faq/">' . __('FAQ', 'mtouchquiz') . '</a>';
	}
	
	return $links;
}

 

add_action('activate_mtouch-quiz/mtouchquiz.php','mtq_activate');
function mtq_activate() {
	global $wpdb;
	
	$database_version = '1.4';
	$installed_db = get_option('mtouchquiz_db_version');
	// Initial options.
	 //add_option('mtq_show_answers', 1);
	 //add_option('mtq_single_page', 0);
	 add_option('mtouchquiz_leftdelimit', "\\\(\\\displaystyle{");
     add_option('mtouchquiz_rightdelimit', "}\\\)");
	 add_option('mtouchquiz_showalerts', "1");
	 //add_option('mtouchquiz_skiploadjquerytools', "0");
	
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
					form_code mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
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

