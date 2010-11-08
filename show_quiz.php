<?php
	require_once('wpframe.php');
	if(!is_single() and isset($GLOBALS['mtouchquiz_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, do not show another.
		printf(__("Please go to <a href='%s'>%s</a> to view the quiz", 'mtouchquiz'), get_permalink(), get_the_title());
	} else 
	{

		global $wpdb;
		$GLOBALS['wpframe_plugin_name'] = basename(dirname(__FILE__));
		$GLOBALS['wpframe_plugin_folder'] = $GLOBALS['wpframe_wordpress'] . '/wp-content/plugins/' . $GLOBALS['wpframe_plugin_name'];

		//$answer_display = get_option('mtouchquiz_show_answers');
		//$offset_result = mysql_query( " SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=1 "); // method 3 on http://ranawd.wordpress.com/2009/03/25/select-random-value-from-mysql-database-table/
		//$offset_row = mysql_fetch_object( $offset_result );
		//$num_questions_to_select = 2; // It appears I must select 1 over and over again and then combine them? If I choose 2, it picks 2 consecutive questions. That's just a random start and not a random collection
		//$offset = $offset_row->offset;
		//$all_question = mysql_query( " SELECT * FROM {$wpdb->prefix}mtouchquiz_question LIMIT $offset, 1 " );
		//$all_question = $wpdb->get_results($wpdb->prepare("SELECT ID,question,explanation, point_value FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=%d ORDER BY ID LIMIT $offset, $num_questions_to_select ", $quiz_id));
		// end method 3

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
		
		$show_alerts = get_option('mtouchquiz_showalerts');
		
		if ( $input_randomq != -1 ) {
			if ( $input_randomq == 'on' ){
				$random_questions = 1;
			} elseif  ( $input_randomq == 'off' ){
				$random_questions = 0;
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
			$show_nav = 0;	
		}
		
		//if ( $answer_display != 2 ) { // if you aren'__ showing answers as you go, must show the final screen 
		//	$show_final = 1;
		//}
		

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
			if(!isset($GLOBALS['mtouchquiz_client_includes_loaded']))  // Make sure that this code is not loaded more than once.
			{
?>
				<link type="text/css" rel="stylesheet" href="<?php echo $GLOBALS['wpframe_plugin_folder']?>/style.css" />
				<?php wp_head(); ?>
				<?php 
				$GLOBALS['mtouchquiz_client_includes_loaded'] = true; // Make sure that this code is not loaded more than once.
			}
				if(isset($_REQUEST['action']) and $_REQUEST['action']) 
				{ 
					// Quiz Results.
					// Replaced with client side javascript
				} else  // Show The Quiz.
				{ 
				?>
					<div class="quiz-area"> 
					  <!--Quiz generated using <?php echo mtouchquiz_DISPLAY_NAME ?> Version <?php echo mtouchquiz_VERSION ?> by G. Michael Guy (<?php echo mtouchquiz_URL ?>)-->
						<form action="" method="post" class="quiz-form" id="quiz-<?php echo $quiz_id?>">
							<!-- OLD get_row was here! --> 
							<a name='mtouchquiz-view-anchor'></a>
							<h2>
							  <div id="mtouchquiz-quiztitle" class="mtouchquiz-quiztitle"><?php echo stripslashes($quiz_options->name)?></div>
							</h2>
							<div id="javawarning"><?php _e('Please wait while the activity loads. If this activity does not load, try refreshing your browser. Also, this page requires javascript. Please visit using a browser with javascript enabled.', 'mtouchquiz'); ?></div>
							<div id="mtouchquiz-instructions" class="mtouchquiz-instructions"><?php echo stripslashes($quiz_options->description)?></div>
							<div id="start_button" class='mtouchquiz-start-button mtouchquiz-action-button' onclick='mtouchquizStartQuiz()'><?php _e("Start", 'mtouchquiz'); ?></div>
							<input type='hidden' id='answer_display' value='<?php echo $answer_display;?>'/>
							<input type='hidden' id='single_page' value='<?php echo $single_page;?>'/>
							<input type='hidden' id='show_hints' value='<?php echo $show_hints;?>'/>
							<input type='hidden' id='show_start' value='<?php echo $show_start;?>'/>
							<input type='hidden' id='show_final' value='<?php echo $show_final;?>'/>
                            <input type='hidden' id='show_alerts' value='<?php echo $show_alerts;?>'/>
							<input type='hidden' id='multiple_chances' value='<?php echo $multiple_chances;?>'/>
							<div id="QuizResults-bubble" class="QuizResults-bubble">
								<div id="QuizResults" class="QuizResults"><?php echo str_replace('%%QUIZ_NAME%%','<em>'.stripslashes($quiz_options->name).'</em>',$final_screen);?> <br></div>
									<div id="QuizResultsHighlight"><?php _e('Your answers are highlighed below.', 'mtouchquiz'); ?></div>
							</div>
							<?php
								$question_count = 1;
								foreach ($all_question as $ques) {
									echo "<div class='mtouchquiz-question' id='question-$question_count'>"; 
										echo "<div id='mtouchquiz-question-item'>";
											echo "<table class='mtouchquiz-question-heading-table'><tr><td>";
												echo "<div class='mtouchquiz-question-label'>";
													printf(__('Question %d', 'mtouchquiz'), $question_count);
												echo "</div>";
												echo "<div id='mtouchquiz_stamp-$question_count' class='mtouchquiz-stamp'></div>";
												echo "</td></tr></table>";
											//echo "<hr class='mtouchquiz-question-divider' noshade='noshade'/>";
											echo stripslashes($ques->question) ;
											echo "<input type='hidden' name='question_id[]' value='{$ques->ID}'/>";
											echo "<input type='hidden' id='is_answered-{$question_count}' value='0'/>";
											echo "<input type='hidden' id='is_correct-{$question_count}' value='0'/>";
											echo "<input type='hidden' id='is_worth-{$question_count}' value='{$ques->point_value}'/>";
											echo "<input type='hidden' id='num_attempts-{$question_count}' value='0'/>";
											echo "<input type='hidden' id='points_awarded-{$question_count}' value='0'/>";
											//echo "</div>";
											echo "<table class='mtouchquiz-answer-table'>";
												echo "<colgroup>";
													echo "<col class='oce-first'/>";
												echo "</colgroup>";

												if ( $random_answers == 1 ) {
													$dans = $wpdb->get_results("SELECT ID,answer,correct,hint FROM {$wpdb->prefix}mtouchquiz_answer WHERE question_id={$ques->ID} ORDER BY RAND()"); // This will randomize the question answer order
												} else {
													$dans = $wpdb->get_results("SELECT ID,answer,correct,hint FROM {$wpdb->prefix}mtouchquiz_answer WHERE question_id={$ques->ID} ORDER BY sort_order");
												}
												$answer_count = 1;
												$num_correct = 0;
												foreach ($dans as $ans) {
													$image_number = ($answer_count-1) % 26;
													echo "<tr id='row-{$question_count}-{$answer_count}'>";
														echo "<td width='40'>";
															echo "<div id='button-{$question_count}-{$answer_count}'>";
																echo "<div id='image_button-{$question_count}-{$answer_count}' class='mtouchquiz-letter-button mtouchquiz-letter-button-{$image_number}' 	onclick='mtouchquizButton_click({$question_count},{$answer_count})'></div>"; 
															echo "</div>";
														if ($ans->correct) {
																echo "<div id='marker-{$question_count}-{$answer_count}' class='mtouchquiz-marker mtouchquiz-correct-marker'></div>"; 
																$num_correct++;
														} else {
																echo "<div id='marker-{$question_count}-{$answer_count}' class='mtouchquiz-marker mtouchquiz-wrong-marker mtouchquiz-marker'></div>"; 
														}
														echo "</td>";
														echo "<td class='mtouch-answer-td'>";
															echo stripslashes($ans->answer);
															$is_correct_value = '0';
															if($ans->correct) $is_correct_value = '1';
															$has_hint_value = '0';
															if($ans->hint) $has_hint_value = '1';
															echo "<input type='hidden' id='is_correct-{$question_count}-{$answer_count}' value='$is_correct_value'/>";
															echo "<input type='hidden' id='has_hint-{$question_count}-{$answer_count}' value='$has_hint_value'/>";
															echo "<input type='hidden' id='was_selected-{$question_count}-{$answer_count}' value='0'/>";
															echo "<input type='hidden' id='was_ever_selected-{$question_count}-{$answer_count}' value='0'/>";
															echo "<div id='hint-$question_count-$answer_count' class='mtouchquiz-hint'>";					
																echo "<div class='mtouchquiz-hint-label'>".__('Hint', 'mtouchquiz').":</div>";
																echo "<div class='mtouchquiz-hint-text'>".stripslashes($ans->hint)."</div>";
															echo "</div>";
														echo "</td>";
													echo "</tr>";
													$answer_count++;
												}
												$answer_count--;
											echo "</table>";

											if ($ques->explanation) //Need to format this better 
											{
												echo "<div id='question_explanation-{$question_count}' class='mtouchquiz-explanation'>";
														echo "<div class='mtouchquiz-explanation-label'>";
															printf(__('Question %d Explanation:', 'mtouchquiz'), $question_count);
														echo "</div>";
														echo "<div class='mtouchquiz-explanation-text'>".stripslashes($ques->explanation."</div>");
												echo "</div>";
												echo "<input type='hidden' id='has_explanation-$question_count' value='1' />";
											} else {
												echo "<input type='hidden' id='has_explanation-$question_count' value='0' />";
											}
										echo "</div>";
									echo "</div>";
									echo "<input type='hidden' id='num_ans-$question_count' value='$answer_count' />";
									echo "<input type='hidden' id='num_correct-$question_count' value='$num_correct' />";
									$question_count++;
								}
								$question_count--;
							?>
							<div id="mtouchquiz-results-request" class="mtouchquiz-results-request">
								<text><?php _e('Once you are finished, click the button below. Any items you have not completed will be marked incorrect.', 'mtouchquiz'); ?></text>
								<div id="results_button" class='mtouchquiz-results-button mtouchquiz-action-button' onclick='mtouchquizGetResults()'><?php _e("Get Results", 'mtouchquiz'); ?> </div>
							</div>
                            <!--mtouchquiz-status-->
                            <div id="QuizStatus"><?php if ($question_count == 1 ){ _e('There is 1 question to complete.', 'mtouchquiz');} else { printf(__("There are %d questions to complete.", 'mtouchquiz'), $question_count); } ?></div>
                            
							<table id="mtouchquiznavrow">
								<tr>
									<td width="86px">
										<div id="back_button" class='mtouchquiz-back-button' onclick='mtouchquizPreviousQuestion()'></div>
									</td>
									<td>
                                    <div id=mtouchquiz-nav-row-container">
									
                                <?php
								if ( $show_nav ) { 
									for ($i=1; $i<=$question_count; $i++) {
										echo "<div id='mtouchquiz_nav_item-$i' class='mtouchquiz-nav-item' onclick='mtouchquizNavClick($i)'>$i</div>";
									}
									if ( $show_final ) {
										echo "<div id='mtouchquiz_nav_item-end' class='mtouchquiz-nav-item' onclick='mtouchquizNavClick($i)'>".__('End', 'mtouchquiz')."</div>";
									}
								}
								?>
                               
                               </div>
									</td>
									<td width="86px">
										<div id="next_button" class='mtouchquiz-next-button' onclick='mtouchquizNextQuestion()'></div>
									</td>
								</tr>
                                </table>
                                
                             <!--   <div id="mtouchquiz_nav_table">-->
<!--                                <tr>
                                <td>-->
                                
                                
                                
                                
<!--                                </td>
                                </tr>
              
							</table>-->
                            
                           
							<!-- <input type="hidden" id="plugin_dir" value="<?php //echo $GLOBALS['wpframe_plugin_folder'] ?>"/>-->
                            
                           	<div id="have_completed_string" class="preload"><?php _e('You have completed', 'mtouchquiz') ?> </div>
                            <div id="questions_string" class="preload"><?php _e('questions', 'mtouchquiz') ?></div>
                            <div id="question_string" class="preload"><?php _e('question', 'mtouchquiz') ?></div>
                            <div id="your_score_is_string"  class="preload"><?php _e('Your score is', 'mtouchquiz')?></div>
                            <div id="correct_string"  class="preload"><?php _e('Correct', 'mtouchquiz')?></div>
                            <div id="wrong_string"  class="preload"><?php _e('Wrong', 'mtouchquiz')?></div>
                            <div id="partial_string"  class="preload"><?php _e('Partial-Credit', 'mtouchquiz')?></div>
                            <div id="exit_warning_string"  class="preload"><?php _e('You have not finished your quiz. If you leave this page, your progress will be lost.', 'mtouchquiz')?></div>
                            
							<input type="hidden" id="quiz_id" value="<?php echo  $quiz_id ?>" />
							<input type="hidden" id="total_questions" value="<?php echo  $question_count; ?>" />
							<input type="hidden" id="current_score" value="0" />
							<input type="hidden" id="max_score" value="0" />
							<input type="hidden" id="questions_attempted" value="0" />
							<input type="hidden" id="questions_correct" value="0" />
							<input type="hidden" id="questions_wrong" value="0" />
							<input type="hidden" id="questions_not_attempted" value="0" />
                            <input type="hidden" id="display_number" value="<?php echo  $display_number; ?>" />
                            <?php 
								$all_ratings = $wpdb->get_results($wpdb->prepare("SELECT score_rating, min_points FROM {$wpdb->prefix}mtouchquiz_ratings WHERE quiz_id=%d ORDER BY min_points", $quiz_id));
								$num_ratings=count($all_ratings);
								if ($num_ratings == 0)
								{
							?>
                            <input type="hidden" id="num_ratings" value="5"/>
                            <input type="hidden" id="ratingval-1" value="0"/>
                            <div id="rating-1" class="preload"><?php _e('Need more practice!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="ratingval-2" value="40"/>
                            <div id="rating-2" class="preload"><?php _e('Keep trying!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="ratingval-3" value="60"/>
                            <div id="rating-3" class="preload"><?php _e('Not bad!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="ratingval-4" value="80"/>
                            <div id="rating-4" class="preload"><?php _e('Good work!', 'mtouchquiz'); ?></div>
                            <input type="hidden" id="ratingval-5" value="100"/>
                            <div id="rating-5" class="preload"><?php _e('Perfect!', 'mtouchquiz'); ?></div>
                            <?php
								} else
								{
									$how_many = $num_ratings + 1;
									echo "<input type='hidden' id='num_ratings' value='". $how_many ."'/>";
									echo "<input type='hidden' id='ratingval-1' value='-1'/>";
                            		echo "<div id='rating-1' class='preload'>".__('All done', 'mtouchquiz')."</div>";
									$counter = 2;
									foreach ($all_ratings as $quiz_rating) 
									{
										echo "<input type='hidden' id='ratingval-$counter' value='$quiz_rating->min_points'/>";
                            			echo "<div id='rating-$counter' class='preload'>".trim(stripslashes($quiz_rating->score_rating))."</div>";
										$counter++;
									}
									
								}
							?>
						</form>
						<?php $preload_classes = array("mtouchquiz-back-button","mtouchquiz-next-button","mtouchquiz-wrong-marker","mtouchquiz-correct-marker"); 
							foreach ($preload_classes as $i => $value) {
								echo "<div class='$preload_classes[$i] preload'></div>";
							}
							
							for( $i = 0 ; $i <= 25; $i++ ){
								echo "<div class='mtouchquiz-letter-button-$i preload'></div>";
							}
						?>
					</div> <!--Quiz area div-->
<?php 			} // closes show the quiz else statement
			} //closes the else that prevents more than one loading
	} // closes the else for #If this is in the listing page
?>
