<?php
	require_once('wpframe.php');
	//require_once('show_question.php');
	
	if(!isset($GLOBALS['mtouchquiz_number_displayed'])) {
		$GLOBALS['mtouchquiz_number_displayed'] = 1;
	} else {
		$GLOBALS['mtouchquiz_number_displayed']++;
	}
	//if(!is_single() and isset($GLOBALS['mtouchquiz_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, do not show another.
	//	printf(__("Please go to <a href='%s'>%s</a> to view the quiz", 'mtouchquiz'), get_permalink(), get_the_title());
	//} else 
	if (true) {
		
		global $wpdb;
		$GLOBALS['wpframe_plugin_name'] = basename(dirname(__FILE__));
		$GLOBALS['wpframe_plugin_folder'] = $GLOBALS['wpframe_wordpress'] . '/wp-content/plugins/' . $GLOBALS['wpframe_plugin_name'];

		$quiz_options = $wpdb->get_row($wpdb->prepare("SELECT name,description,answer_mode,single_page,show_hints,show_start,show_final,multiple_chances,final_screen,random_questions,random_answers FROM {$wpdb->prefix}mtouchquiz_quiz WHERE ID=%d", $quiz_id));			 		
		$final_screen = stripslashes($quiz_options->final_screen);
		$answer_display = stripslashes($quiz_options->answer_mode);
		$single_page = stripslashes($quiz_options->single_page);
		$show_hints = stripslashes($quiz_options->show_hints);
		$show_start = stripslashes($quiz_options->show_start);
		$show_final = stripslashes($quiz_options->show_final);
		$multiple_chances = stripslashes($quiz_options->multiple_chances);
		$random_questions = stripslashes($quiz_options->random_questions);
		$random_answers = stripslashes($quiz_options->random_answers);
		$mtouchquiz_show_alerts = get_option('mtouchquiz_showalerts');
		
		if ( $input_randomq != -1 ) {
			if ( $input_randomq == 'on' ){
				$random_questions = 1;
			} elseif  ( $input_randomq == 'off' ){
				$random_questions = 0;
			}
		}
		
		if ( $input_alerts != -1 ) {
			if ( $input_alerts == 1 ){
				$mtouchquiz_show_alerts = 1;
			} elseif  ( $input_alerts == 0 ){
				$mtouchquiz_show_alerts = 0;
			}
		}
		
		if ( $input_randoma != -1 ) {
			if ( $input_randoma == 'on' ){
				$random_answers = 1;
			} elseif  ( $input_randoma == 'off' ){
				$random_answers = 0;
			}
		}
		
		if ( $input_singlepage != -1 ) {
			if ( $input_singlepage == 'on' ){
				$single_page = 1;
			} elseif  ( $input_singlepage == 'off' ){
				$single_page = 0;
			}
		}
		if ( $input_hints!= -1 ) {
			if ( $input_hints == 'on' ){
				$show_hints = 1;
			} elseif  ( $input_hints == 'off' ){
				$show_hints = 0;
			}
		}
		if ( $input_startscreen!= -1 ) {
			if ( $input_startscreen == 'on' ){
				$show_start = 1;
			} elseif  ( $input_startscreen == 'off' ){
				$show_start = 0;
			}
		}

		if ( $input_showanswers != -1 ) {
			if ( $input_showanswers == 'never' ){
				$answer_display = 0;
			} elseif  ( $input_showanswers == 'end' ){
				$answer_display = 1;
			} elseif  ( $input_showanswers == 'now' ){
				$answer_display = 2;
			}
		}
		
		if ( $input_finalscreen != -1 ) {
			if ( $input_finalscreen == 'on' ){
				$show_final = 1;
			} elseif  ( $input_finalscreen == 'off' ){
				$show_final = 0;
			}
		}
		
		if ( $input_multiplechances!= -1 ) {
			if ( $input_multiplechances == 'on' ){
				$multiple_chances = 1;
			} elseif  ( $input_multiplechances == 'off' ){
				$multiple_chances = 0;
			}
		}
		if( $multiple_chances == 1  ) {
			$answer_display = 2; // You cannot allow multiple chances and not show the answers immediately.
		}
		
		if ( $single_page ) {
			$show_list = 0;	
		}
		
		if ( $proofread ) {
			$random_answers = 0;
			$random_questions =0;
			$input_number_questions = 0;
			$single_page = 1;
		}

		// Thanks http://ranawd.wordpress.com/2009/03/25/select-random-value-from-mysql-database-table/
		
		if ( $random_questions == 1 && $input_number_questions <= 0 ) { // Select all questions randomly
			$all_question = $wpdb->get_results($wpdb->prepare("SELECT ID,question,explanation, point_value FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=%d ORDER BY RAND()  ", $quiz_id)); 
		} elseif( $random_questions == 1 && $input_number_questions > 0 ) { // Select input number of questions randomly
			$all_question = $wpdb->get_results($wpdb->prepare("SELECT ID,question,explanation, point_value FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=%d ORDER BY RAND() LIMIT 0, $input_number_questions", $quiz_id));
		} elseif( $random_questions != 1 && $input_number_questions > 0 ) { // Select some questions in order
			$all_question = $wpdb->get_results($wpdb->prepare("SELECT ID,question,explanation, point_value FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=%d ORDER BY ID LIMIT 0, $input_number_questions", $quiz_id)); // Not random
		} else { // select all questions in order
			$all_question = $wpdb->get_results($wpdb->prepare("SELECT ID,question,explanation, point_value FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=%d ORDER BY ID", $quiz_id)); // Not random
		}

		if($all_question) 
		{
			
			$mtqid=$GLOBALS['mtouchquiz_number_displayed'];
			if ($proofread != 1 ) {//Only include script and css once?>
			<link type="text/css" rel="stylesheet" href="<?php echo $GLOBALS['wpframe_plugin_folder']?>/style.css" />
			<script type="text/javascript" src="<?php echo $GLOBALS['wpframe_wordpress']?>/wp-includes/js/jquery/jquery.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['wpframe_plugin_folder']?>/script.js"></script>
            <?php
			} else {
			?>
            	<link type="text/css" rel="stylesheet" href="<?php echo $GLOBALS['wpframe_plugin_folder']?>/style.css" />
                <link type="text/css" rel="stylesheet" href="<?php echo $GLOBALS['wpframe_plugin_folder']?>/proofread.css" />
            <?php
			} 
			?>
            <?php
			//if(!isset($GLOBALS['mtouchquiz_client_includes_loaded']))  // Make sure that this code is not loaded more than once.
			//{
?>

				<?php 
				$GLOBALS['mtouchquiz_client_includes_loaded'] = true; // Make sure that this code is not loaded more than once.
			//}
				if(isset($_REQUEST['action']) and $_REQUEST['action']) 
				{ 
					// Quiz Results.
					// Replaced with client side javascript
				} else  // Show The Quiz.
				{ 
				?>
					<div id="mtouchquiz_quiz-area" class="mtouchquiz_quiz-area"> 
					  <!--Quiz generated using <?php echo mtouchquiz_DISPLAY_NAME ?> Version <?php echo mtouchquiz_VERSION ?> by G. Michael Guy (<?php echo mtouchquiz_URL ?>)-->
						<form action="" method="post" class="quiz-form" id="quiz-<?php echo $quiz_id?>">
							<!-- OLD get_row was here! --> 
							<a name='mtouchquiz_view-anchor<?php echo "-".$mtqid ?>'></a>
							<h2>
							  <div id="mtouchquiz_quiztitle<?php echo "-".$mtqid ?>" class="mtouchquiz_quiztitle"><?php echo stripslashes($quiz_options->name)?></div>
							</h2>
							<div id="mtouchquiz_javawarning<?php echo "-".$mtqid ?>" class="mtouchquiz_javawarning"><?php _e('Please wait while the activity loads. If this activity does not load, try refreshing your browser. Also, this page requires javascript. Please visit using a browser with javascript enabled.', 'mtouchquiz'); ?><div class="mtouchquiz_failed_button" onclick="mtouchquiz_Start_one(<?php echo $mtqid ?>)"> <?php _e('If loading fails, click here to try again','mtouchquiz') ?></div></div>
							<div id="mtouchquiz_instructions<?php echo "-".$mtqid ?>" class="mtouchquiz_instructions"><?php echo stripslashes($quiz_options->description)?></div>
							<div id="mtouchquiz_start_button<?php echo "-".$mtqid ?>" class='mtouchquiz_action-button mtouchquiz_css-button' onclick='mtouchquiz_StartQuiz(<?php echo $mtqid ?>)'><?php _e("Start", 'mtouchquiz'); ?></div>
							<input type='hidden' id='mtouchquiz_answer_display<?php echo "-".$mtqid ?>' value='<?php echo $answer_display;?>'/>
							<input type='hidden' id='mtouchquiz_single_page<?php echo "-".$mtqid ?>' value='<?php echo $single_page;?>'/>
							<input type='hidden' id='mtouchquiz_show_hints<?php echo "-".$mtqid ?>' value='<?php echo $show_hints;?>'/>
							<input type='hidden' id='mtouchquiz_show_start<?php echo "-".$mtqid ?>' value='<?php echo $show_start;?>'/>
							<input type='hidden' id='mtouchquiz_show_final<?php echo "-".$mtqid ?>' value='<?php echo $show_final;?>'/>
                            <input type='hidden' id='mtouchquiz_show_alerts<?php echo "-".$mtqid ?>' value='<?php echo $mtouchquiz_show_alerts;?>'/>
							<input type='hidden' id='mtouchquiz_multiple_chances<?php echo "-".$mtqid ?>' value='<?php echo $multiple_chances;?>'/>
                            <input type='hidden' id='mtouchquiz_proofread<?php echo "-".$mtqid ?>' value='<?php echo $mtouchquiz_proofread;?>'/>
							<div id="mtouchquiz_QuizResults-bubble<?php echo "-".$mtqid ?>" class="mtouchquiz_QuizResults-bubble">
								<div id="mtouchquiz_QuizResults<?php echo "-".$mtqid ?>" class="mtouchquiz_QuizResults"><?php echo str_replace('%%QUIZ_NAME%%','<em>'.stripslashes($quiz_options->name).'</em>',$final_screen);?> <br></div>
									<div id="mtouchquiz_QuizResultsHighlight<?php echo "-".$mtqid ?>" class="mtouchquiz_QuizResultsHighlight"><?php _e('Your answers are highlighed below.', 'mtouchquiz'); ?></div>
							</div>
							<?php
								$question_count = 1;
								foreach ($all_question as $ques) {
									//$question_label = __('Question', 'mtouchquiz')." ". $question_count;
									//echo mtouchquiz_showquestion($ques,$question_count,$question_label,$random_answers);
									echo   "<div class='mtouchquiz_question' id='mtouchquiz_question-$question_count-$mtqid'>"; 
										echo   "<div id='mtouchquiz_question-item'>";
											echo   "<table class='mtouchquiz_question-heading-table'><tr><td>";
												echo   "<div class='mtouchquiz_question-label'>";
													printf(__('Question %d', 'mtouchquiz'), $question_count);
												echo   "</div>";
												echo   "<div id='mtouchquiz_stamp-$question_count-$mtqid' class='mtouchquiz_stamp'></div>";
												echo   "</td></tr></table>";
											echo   stripslashes($ques->question) ;
											echo   "<input type='hidden' name='question_id[]' value='{$ques->ID}'/>";
											echo   "<input type='hidden' id='mtouchquiz_is_answered-{$question_count}-$mtqid' value='0'/>";
											echo   "<input type='hidden' id='mtouchquiz_is_correct-{$question_count}-$mtqid' value='0'/>";
											echo   "<input type='hidden' id='mtouchquiz_is_worth-{$question_count}-$mtqid' value='{$ques->point_value}'/>";
											echo   "<input type='hidden' id='mtouchquiz_num_attempts-{$question_count}-$mtqid' value='0'/>";
											echo   "<input type='hidden' id='mtouchquiz_points_awarded-{$question_count}-$mtqid' value='0'/>";
											//echo   "</div>";
											echo   "<table class='mtouchquiz_answer-table'>";
												echo   "<colgroup>";
													echo   "<col class='mtouchquiz_oce-first'/>";
												echo   "</colgroup>";

												if ( $random_answers == 1 ) {
													$dans = $wpdb->get_results("SELECT ID,answer,correct,hint FROM {$wpdb->prefix}mtouchquiz_answer WHERE question_id={$ques->ID} ORDER BY RAND()"); // This will randomize the question answer order
												} else {
													$dans = $wpdb->get_results("SELECT ID,answer,correct,hint FROM {$wpdb->prefix}mtouchquiz_answer WHERE question_id={$ques->ID} ORDER BY sort_order");
												}
												$answer_count = 1;
												$num_correct = 0;
												foreach ($dans as $ans) {
													$image_number = ($answer_count-1) % 26;
													echo   "<tr id='mtouchquiz_row-{$question_count}-{$answer_count}-$mtqid'>";
														echo   "<td class='mtouchquiz_letter-button-td'>";
															echo   "<div id='mtouchquiz_button-{$question_count}-{$answer_count}-$mtqid'>";
																echo   "<div id='mtouchquiz_image_button-{$question_count}-{$answer_count}-$mtqid' class='mtouchquiz_letter-button mtouchquiz_letter-mtouchquiz_button-{$image_number}' 	onclick='mtouchquiz_ButtonClick({$question_count},{$answer_count},$mtqid)'></div>"; 
															echo   "</div>";
														if ($ans->correct) {
																echo   "<div id='mtouchquiz_marker-{$question_count}-{$answer_count}-$mtqid' class='mtouchquiz_marker mtouchquiz_correct-marker'></div>"; 
																$num_correct++;
														} else {
																echo   "<div id='mtouchquiz_marker-{$question_count}-{$answer_count}-$mtqid' class='mtouchquiz_marker mtouchquiz_wrong-marker mtouchquiz_marker'></div>"; 
														}
														echo   "</td>";
														echo   "<td class='mtouchquiz_answer-td'>";
															echo   "<div id='mtouchquiz_answer-text-{$question_count}-{$answer_count}-$mtqid' class='mtouchquiz_answer-text'>".stripslashes($ans->answer)."</div>";
															$is_correct_value = '0';
															if($ans->correct) $is_correct_value = '1';
															$has_hint_value = '0';
															if($ans->hint) $has_hint_value = '1';
															echo   "<input type='hidden' id='mtouchquiz_is_correct-{$question_count}-{$answer_count}-$mtqid' value='$is_correct_value'/>";
															echo   "<input type='hidden' id='mtouchquiz_has_hint-{$question_count}-{$answer_count}-$mtqid' value='$has_hint_value'/>";
															echo   "<input type='hidden' id='mtouchquiz_was_selected-{$question_count}-{$answer_count}-$mtqid' value='0'/>";
															echo   "<input type='hidden' id='mtouchquiz_was_ever_selected-{$question_count}-{$answer_count}-$mtqid' value='0'/>";
															echo   "<div id='mtouchquiz_hint-$question_count-$answer_count-$mtqid' class='mtouchquiz_hint'>";					
																echo   "<div class='mtouchquiz_hint-label'>".__('Hint', 'mtouchquiz').":</div>";
																echo   "<div class='mtouchquiz_hint-text'>".stripslashes($ans->hint)."</div>";
															echo   "</div>";
														echo   "</td>";
													echo   "</tr>";
													$answer_count++;
												}
												$answer_count--;
											echo   "</table>";

											if ($ques->explanation) //Need to format this better 
											{
												echo   "<div id='mtouchquiz_question_explanation-{$question_count}-$mtqid' class='mtouchquiz_explanation'>";
														echo   "<div class='mtouchquiz_explanation-label'>";
															 printf(__('Question %d Explanation:', 'mtouchquiz'), $question_count);
														echo   "</div>";
														echo   "<div class='mtouchquiz_explanation-text'>".stripslashes($ques->explanation."</div>");
												echo   "</div>";
												echo   "<input type='hidden' id='mtouchquiz_has_explanation-$question_count-$mtqid' value='1' />";
											} else {
												echo   "<input type='hidden' id='mtouchquiz_has_explanation-$question_count-$mtqid' value='0' />";
											}
										echo   "</div>";
									echo   "</div>";
									echo   "<input type='hidden' id='mtouchquiz_num_ans-$question_count-$mtqid' value='$answer_count' />";
									echo   "<input type='hidden' id='mtouchquiz_num_correct-$question_count-$mtqid' value='$num_correct' />";
									$question_count++;
								}
								$question_count--;
							?>
							<div id="mtouchquiz_results-request<?php echo "-".$mtqid ?>" class="mtouchquiz_results-request">
								<text><?php _e('Once you are finished, click the button below. Any items you have not completed will be marked incorrect.', 'mtouchquiz'); ?></text>
								<div id="mtouchquiz_results_button<?php echo "-".$mtqid ?>" class='mtouchquiz_results-button mtouchquiz_action-button mtouchquiz_css-button'  onclick='mtouchquiz_GetResults(<?php echo $mtqid ?>)'><?php _e("Get Results", 'mtouchquiz'); ?> </div>
							</div>
                            <!--mtouchquiz_status-->
                            <div id="mtouchquiz_QuizStatus<?php echo "-".$mtqid ?>" class="mtouchquiz_QuizStatus"><?php if ($question_count == 1 ){ _e('There is 1 question to complete.', 'mtouchquiz');} else { printf(__("There are %d questions to complete.", 'mtouchquiz'), $question_count); } ?></div>
                            <div id='mtouchquiz_return_list_t<?php echo "-".$mtqid ?>' class="mtouchquiz_return_list mtouchquiz_css-button" onclick='mtouchquiz_NavClick(0,<?php echo $mtqid ?>)'><?php _e('Return', 'mtouchquiz');?> </div>
                            <div id="mtouchquiz_shaded-item-msg<?php echo "-".$mtqid ?>" class="mtouchquiz_shaded-item-msg"><?php _e('Shaded items are complete.','mtouchquiz');?></div>
                            <table id="mtouchquiz_question-list-container<?php echo "-".$mtqid ?>" class="mtouchquiz_question-list-container">
                                <tr>	
                                <?php
								//if ( $show_list ) { 
									for ($i=1; $i<=$question_count; $i++) {
										echo "<td id='mtouchquiz_list_item-$i-$mtqid' class='mtouchquiz_list-item' onclick='mtouchquiz_NavClick($i,$mtqid)'>$i</td>";
										if ( ($i % 5) == 0 && $i > 1) {
											echo "</tr><tr>";
										}
									}
									if ( $show_final ) {
										echo "<td id='mtouchquiz_list_item-end-$mtqid' class='mtouchquiz_list-item' onclick='mtouchquiz_NavClick($i,$mtqid)'>".__('End', 'mtouchquiz')."</td>";
									}
									
								//}
								?>
                               	</tr>
                                </table>
                               <div id='mtouchquiz_return_list_b<?php echo "-".$mtqid ?>' class="mtouchquiz_return_list mtouchquiz_css-button" onclick='mtouchquiz_NavClick(0,<?php echo $mtqid ?>)'><?php _e('Return', 'mtouchquiz');?> </div>
                              
                            
							<table id="mtouchquiz_listrow<?php echo "-".$mtqid ?>" class="mtouchquiz_listrow">
								<tr>
									<td class="mtouchquiz_listrow-button-td">
										<div id="mtouchquiz_back_button<?php echo "-".$mtqid ?>" class='mtouchquiz_back-button mtouchquiz_listrow-button' onclick='mtouchquiz_PreviousQuestion(<?php echo $mtqid ?>)'></div>
									</td>
									<td>
<div id="mtouchquiz_show_list<?php echo "-".$mtqid ?>" class="mtouchquiz_show_list mtouchquiz_css-button" onclick="mtouchquiz_ShowNav(<?php echo $mtqid ?>)"><?php _e("List", 'mtouchquiz');?></div>
									</td>
									<td class="mtouchquiz_listrow-button-td">
										<div id="mtouchquiz_next_button<?php echo "-".$mtqid ?>" class='mtouchquiz_next-button mtouchquiz_listrow-button' onclick='mtouchquiz_NextQuestion(<?php echo $mtqid ?>)'></div>
									</td>
								</tr>
                                </table>
                            
                           	<div id="mtouchquiz_have_completed_string" class="mtouchquiz_preload"><?php _e('You have completed', 'mtouchquiz') ?> </div>
                            <div id="mtouchquiz_questions_string" class="mtouchquiz_preload"><?php _e('questions', 'mtouchquiz') ?></div>
                            <div id="mtouchquiz_question_string" class="mtouchquiz_preload"><?php _e('question', 'mtouchquiz') ?></div>
                            <div id="mtouchquiz_your_score_is_string"  class="mtouchquiz_preload"><?php _e('Your score is', 'mtouchquiz')?></div>
                            <div id="mtouchquiz_correct_string"  class="mtouchquiz_preload"><?php _e('Correct', 'mtouchquiz')?></div>
                            <div id="mtouchquiz_wrong_string"  class="mtouchquiz_preload"><?php _e('Wrong', 'mtouchquiz')?></div>
                            <div id="mtouchquiz_partial_string"  class="mtouchquiz_preload"><?php _e('Partial-Credit', 'mtouchquiz')?></div>
                            <div id="mtouchquiz_exit_warning_string"  class="mtouchquiz_preload"><?php _e('You have not finished your quiz. If you leave this page, your progress will be lost.', 'mtouchquiz')?></div>
                            
							<input type="hidden" name="quiz_id" id="quiz_id<?php echo "-".$mtqid ?>" value="<?php echo  $quiz_id ?>" />
							<input type="hidden" name="mtouchquiz_total_questions" id="mtouchquiz_total_questions<?php echo "-".$mtqid ?>" value="<?php echo  $question_count; ?>" />
							<input type="hidden" name="mtouchquiz_current_score" id="mtouchquiz_current_score<?php echo "-".$mtqid ?>" value="0" />
							<input type="hidden" name="mtouchquiz_max_score" id="mtouchquiz_max_score<?php echo "-".$mtqid ?>" value="0" />
							<input type="hidden" name="mtouchquiz_questions_attempted" id="mtouchquiz_questions_attempted<?php echo "-".$mtqid ?>" value="0" />
							<input type="hidden" name="mtouchquiz_questions_correct" id="mtouchquiz_questions_correct<?php echo "-".$mtqid ?>" value="0" />
							<input type="hidden" name="mtouchquiz_questions_wrong" id="mtouchquiz_questions_wrong<?php echo "-".$mtqid ?>" value="0" />
							<input type="hidden" name="mtouchquiz_questions_not_attempted" id="mtouchquiz_questions_not_attempted<?php echo "-".$mtqid ?>" value="0" />
                            <input type="hidden" id="mtouchquiz_display_number<?php echo "-".$mtqid ?>" value="<?php echo  $display_number; ?>" />
                            <input type="hidden" id="mtouchquiz_show_list_option<?php echo "-".$mtqid ?>" value="<?php echo  $show_list; ?>" />
                            <?php 
								$all_ratings = $wpdb->get_results($wpdb->prepare("SELECT score_rating, min_points FROM {$wpdb->prefix}mtouchquiz_ratings WHERE quiz_id=%d ORDER BY min_points", $quiz_id));
								$mtouchquiz_num_ratings=count($all_ratings);
								if ($mtouchquiz_num_ratings == 0)
								{
							?>
                            <input type="hidden" id="mtouchquiz_num_ratings<?php echo "-".$mtqid ?>" value="5"/>
                            <input type="hidden" id="mtouchquiz_ratingval-1<?php echo "-".$mtqid ?>" value="0"/>
                            <div id="mtouchquiz_rating-1<?php echo "-".$mtqid ?>" class="mtouchquiz_preload"><?php _e('Need more practice!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="mtouchquiz_ratingval-2<?php echo "-".$mtqid ?>" value="40"/>
                            <div id="mtouchquiz_rating-2<?php echo "-".$mtqid ?>" class="mtouchquiz_preload"><?php _e('Keep trying!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="mtouchquiz_ratingval-3<?php echo "-".$mtqid ?>" value="60"/>
                            <div id="mtouchquiz_rating-3<?php echo "-".$mtqid ?>" class="mtouchquiz_preload"><?php _e('Not bad!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="mtouchquiz_ratingval-4<?php echo "-".$mtqid ?>" value="80"/>
                            <div id="mtouchquiz_rating-4<?php echo "-".$mtqid ?>" class="mtouchquiz_preload"><?php _e('Good work!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="mtouchquiz_ratingval-5<?php echo "-".$mtqid ?>" value="100"/>
                            <div id="mtouchquiz_rating-5<?php echo "-".$mtqid ?>" class="mtouchquiz_preload"><?php _e('Perfect!', 'mtouchquiz'); ?></div>
                            <?php
								} else
								{
									$how_many = $mtouchquiz_num_ratings + 1;
									echo "<input type='hidden' id='mtouchquiz_num_ratings-$mtqid' value='". $how_many ."'/>";
									echo "<input type='hidden' id='mtouchquiz_ratingval-1-$mtqid' value='-1'/>";
                            		echo "<div id='mtouchquiz_rating-1-$mtqid' class='mtouchquiz_preload'>".__('All done', 'mtouchquiz')."</div>";
									$counter = 2;
									foreach ($all_ratings as $quiz_rating) 
									{
										echo "<input type='hidden' id='mtouchquiz_ratingval-$counter-$mtqid' value='$quiz_rating->min_points'/>";
                            			echo "<div id='mtouchquiz_rating-$counter-$mtqid' class='mtouchquiz_preload'>".trim(stripslashes($quiz_rating->score_rating))."</div>";
										$counter++;
									}
									
								}
							?>
                            <!--<input type="submit" name="action" id="mtouchquiz_email-button" value="<?php _e("Email Results",'mtouchquiz') ?>"  />-->
						</form>
						<?php 
							//if ( $mtqid == 1 ) {//Only include preloads and css once
                                $preload_classes = array("mtouchquiz_back-button","mtouchquiz_next-button","mtouchquiz_wrong-marker","mtouchquiz_correct-marker"); 
                                foreach ($preload_classes as $i => $value) {
                                    echo "<div class='$preload_classes[$i] mtouchquiz_preload'></div>";
                                }
                                
                                for( $i = 0 ; $i <= 25; $i++ ){
                                    echo "<div class='mtouchquiz_letter-mtouchquiz_button-$i mtouchquiz_preload'></div>";
                                }
                            //}
						?>
					</div> <!--Quiz area div-->
<?php 			} // closes show the quiz else statement
			} //closes the else that prevents more than one loading
	} // closes the else for #If this is in the listing page
?>
