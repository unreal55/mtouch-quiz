<?php
require('wpframe.php');
wpframe_stop_direct_call(__FILE__);

$action = 'new';
if($_REQUEST['action'] == 'edit') $action = 'edit';

$dquiz = array();
if($action == 'edit') {
	$dquiz = $wpdb->get_row($wpdb->prepare("SELECT name,description,final_screen,answer_mode,single_page,show_hints,show_start, show_final,multiple_chances,random_questions,random_answers FROM {$wpdb->prefix}mtouchquiz_quiz WHERE ID=%d", $_REQUEST['quiz']));
	$final_screen = stripslashes($dquiz->final_screen);
	$answer_display = stripslashes($dquiz->answer_mode);
	$single_page = stripslashes($dquiz->single_page);
	$show_hints = stripslashes($dquiz->show_hints);
	$show_start = stripslashes($dquiz->show_start);
	$show_final = stripslashes($dquiz->show_final);
	$multiple_chances = stripslashes($dquiz->multiple_chances);
	$random_questions = stripslashes($dquiz->random_questions);
	$random_answers = stripslashes($dquiz->random_answers);
} else {
	$final_screen = t("<p>Congratulations - you have completed %%QUIZ_NAME%%.</p>

	<p>You scored %%SCORE%% out of %%TOTAL%%.</p>

	<p>Your performance has been rated as %%RATING%%</p>");
}

?>

<div class="wrap">
  <h2>
    <?php e(ucfirst($action) . " Quiz"); ?>
  </h2>
  <?php
	wpframe_add_editor_js();
?>
  <form name="post" action="<?php echo $GLOBALS['wpframe_plugin_folder'] ?>/quiz_action.php" method="post" id="post">
    <div id="poststuff">
      <div class="postbox" id="titlediv">
        <h3 class="hndle"> <span>
          <?php e('Quiz Name') ?>
          </span> </h3>
        <div class="inside">
          <input type='text' name='name' id="title" value='<?php echo stripslashes($dquiz->name); ?>' />
        </div>
      </div>
      <div class="postbox">
        <h3 class="hndle"> <span>
          <?php e('Quiz Start Screen') ?>
          </span> </h3>
        <p align="right"> <a class="button toggleVisual">Visual</a> <a class="button toggleHTML">HTML</a> </p>
        <div class="inside">
          <textarea name='description' rows='5' cols='50' style='width:100%' id='description' class='description'><?php echo stripslashes($dquiz->description); ?></textarea>
        </div>
      </div>
      <div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
        <h3 class="hndle"> <span>
          <?php e('Final Screen') ?>
          </span> </h3>
        <div class="inside">
          <?php the_editor($final_screen); ?>
          <p> <strong>
            <?php e('Usable Variables...') ?>
            </strong> </p>
          <table>
            <tr>
              <th style="text-align:left;"><?php e('Variable') ?></th>
              <th style="text-align:left;"><?php e('Value') ?></th>
            </tr>
            <tr>
              <td>%%SCORE%%</td>
              <td><?php e('The number of correct answers') ?></td>
            </tr>
            <tr>
              <td>%%TOTAL%%</td>
              <td><?php e('Total number of questions') ?></td>
            </tr>
            <tr>
              <td>%%PERCENTAGE%%</td>
              <td><?php e('Correct answer percentage') ?></td>
            </tr>
            <tr>
              <td>%%WRONG_ANSWERS%%</td>
              <td><?php e('Number of answers you got wrong') ?></td>
            </tr>
            <tr>
              <td>%%RATING%%</td>
              <td><?php e("A rating of your performance. (Customize below.)") ?></td>
            </tr>
            <tr>
              <td>%%QUIZ_NAME%%</td>
              <td><?php e('The name of the quiz') ?></td>
            </tr>
          </table>
        </div>
      </div>
      <?php
			// I'll put 2 editors here - as soon as 'http://wordpress.org/support/topic/179110?replies=2' bug is fixed.
	?>
      <?php
			// This is somewhat of a workaround to add some editing now.
	?>
      <script type="text/javascript">
			jQuery(document).ready(function($) {

			var id = 'description';

			$('a.toggleVisual').click(
				function() {
					tinyMCE.execCommand('mceAddControl', false, id);
				}
			);

			$('a.toggleHTML').click(
				function() {
					tinyMCE.execCommand('mceRemoveControl', false, id);
				}
			);

		});
		</script>
      <div class="postbox">
        <h3 class="hndle"> <span>
          <?php e('%%RATING%% Customization for Final Screen above') ?>
          </span> </h3>
        <div class="inside">
          <h4>Enter the percent (whole numbers only) and the message you would like the student to receive in place of the %%RATING%% variable. One of these messages will be displayed if their score is greater than or equal to the listed score.</h4>
          <?php 
		
			if ($action == 'edit') {
				$all_ratings = $wpdb->get_results($wpdb->prepare("SELECT score_rating, min_points FROM {$wpdb->prefix}mtouchquiz_ratings WHERE quiz_id=%d ORDER BY min_points", $_REQUEST['quiz']));
			}
			$default_ratings = array(0,40,60,80,100);
			$default_messages = array("Need more practice!","Keep trying!","Not bad!","Good work!","Perfect!");
			$num_ratings = 5;
			if ($action == 'edit' and $num_ratings < count($all_ratings)) $num_ratings = count($all_ratings) ;
			for($i=1; $i<=$num_ratings; $i++) 
			{
	?>
          <p>
            <textarea name="min_points[]" rows="1" cols="3" id="min_points_<?php echo $i?>" value="<?php echo $i?>"><?php if($action == 'edit') {echo stripslashes($all_ratings[$i-1]->min_points );} else {echo $default_ratings[$i-1];}?>
</textarea>
            <textarea name="score_rating[]" rows="1" cols="100" id="score_rating_<?php echo $i?>" value="<?php echo $i?>"><?php if($action == 'edit') {echo stripslashes($all_ratings[$i-1]->score_rating); }else {echo $default_messages[$i-1];} ?> 
</textarea>
          </p>
          <?php 	} ?>
        </div>
        <script type="text/javascript">
			var num_ratings = <?php echo $num_ratings?>;

			function newRating() {
				num_ratings++;
				var para = document.createElement("p");
				var Linput = document.createElement("textarea");;
				Linput.setAttribute("name", "min_points[]");
				Linput.setAttribute("value", num_ratings);
				Linput.setAttribute("id", "min_points_" + num_ratings);
				Linput.setAttribute("rows", "1");
				Linput.setAttribute("cols", "3");
				para.appendChild(Linput);
				var Atextarea = document.createElement("textarea");
				Atextarea.setAttribute("name", "score_rating[]");
				Atextarea.setAttribute("rows", "1");
				Atextarea.setAttribute("cols", "100");
				Atextarea.setAttribute("value", num_ratings);
				Atextarea.setAttribute("id", "score_rating_" + num_ratings);
				para.appendChild(Atextarea);
				//para.innerHTML("<table><tr><td>");
				//para.appendChild(Htextarea);
				//para.innerHTML("</td></tr></table>");
				//var BigTable = document.createElement("table");
				//BigTable.
				
				//$("extra-answers").innerHTML += code.replace(/%%NUMBER%%/g, answer_count);
				document.getElementById("extra-ratings").appendChild(para);
			}
		</script>
        <div id="extra-ratings"></div>
        <a href="javascript:newRating();">
        <?php e("Add New Rating"); ?>
        </a> </div>
      <div class="postbox">
        <h3 class="hndle"> <span>
          <?php e('Quiz Options') ?>
          </span> </h3>
        <div class="inside">
          <table width="100%">
          <thead>
          	<th> Options
            </th>
   			<th> Shortcode arguments
          </th>
          </thead>
            <tr>
              <td><input type="checkbox" name="single_page" <?php if($single_page == '1') echo 'checked="checked"'; ?> value="2" id="single_page" />
                <label for="single_page"> Show all questions on a single page.</label></td>
              <td> singlepage='on' or singlepage='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="multiple_chances" <?php if($multiple_chances == '1') echo 'checked="checked"'; else if ($action == 'new') { echo 'checked="checked"';} ?> value="2" id="multiple_chances" />
                <label for="multiple_chances"> Allow multiple opportunites to answer questions.*</label></td>
              <td>multiplechances='on' or multiplechances='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="show_hints" <?php if($show_hints == '1') echo 'checked="checked"'; else if ($action == 'new') { echo 'checked="checked"';} ?> value="2" id="show_hints" />
                <label for="show_hints"> Show the hints, when available.</label></td>
              <td> hints='on' or hints='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="show_start" <?php if($show_start == '1') echo 'checked="checked"'; else if ($action == 'new') { echo 'checked="checked"';} ?> value="2" id="show_start" />
                <label for="show_start"> Display Quiz Start Screen before quiz.</label></td>
              <td> startscreen='on' or startscreen='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="show_final" <?php if($show_final == '1') echo 'checked="checked"'; else if ($action == 'new') { echo 'checked="checked"';} ?> value="2" id="show_final" />
                <label for="show_final"> Display Quiz Final Screen after quiz.</label></td>
              <td> finalscreen='on' or finalscreen='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="random_questions" <?php if($random_questions == '1') echo 'checked="checked"'; ?> value="2" id="random_questions" />
                <label for="show_final"> Randomly arrange questions.</label></td>
              <td> randomq='on' or randomq='off' </td>
            </tr>
            <tr>
              <td><input type="checkbox" name="random_answers" <?php if($random_answers == '1') echo 'checked="checked"'; ?> value="2" id="random_answers" />
                <label for="show_final"> Randomly arrange answers.</label></td>
              <td> randoma='on' or randoma='off' </td>
            </tr>
            <tr>
              <td><input type="radio" name="answer_mode" <?php if($answer_display == '0') echo 'checked="checked"'; ?> value="0" id="no-show" />
                <label for="no-show"> Never indicate the correct answers.**</label></td>
              <td> showanswers='never' </td>
            </tr>
            <tr>
              <td><input type="radio" name="answer_mode" <?php if($answer_display == '1') echo 'checked="checked"'; ?> value="1" id="show-end" />
                <label for="show-end"> Indicate the correct answers only at the end of the quiz.</label></td>
              <td> showanswers='end' </td>
            </tr>
            <tr>
              <td><input type="radio" name="answer_mode" <?php if($answer_display == '2') echo 'checked="checked"'; else if ($action == 'new') { echo 'checked="checked"';} ?> value="2" id="show-between" />
                <label for="show-between"> Indicate the correct answers at the end of each question.</label></td>
              <td> showanswers='now' </td>
            </tr>
            <tfoot>
            	<td colspan="2">
                * Must also select 'Indicate the correct answers at the end of each question'.
                </td>
            </tfoot>
                        <tr>
            	<td colspan="2">
                ** It will still indicate which problems were marked correct/wrong at the end of the quiz.
                </td>
            </tr>
          </table>
        </div>
      </div>
      <p class="submit">
        <?php wp_nonce_field('mtouchquiz_create_edit_quiz'); ?>
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
        <input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
        <span id="autosave"></span>
        <input type="submit" name="submit" value="<?php e('Save') ?>" style="font-weight: bold;" tabindex="4" />
      </p>
    </div>
  </form>
</div>
