//depend on mtqid
var current_question = [] ;
var mtouchquiz_total_questions = [] ;
var mtouchquiz_current_score = [] ;
var mtouchquiz_max_score = [] ;
var score_percent = [] ;
var questions_answered = [] ;
var mtouchquiz_questions_correct = [] ;
var problems_attempted = [] ;
var mtouchquiz_questions_wrong = [] ;
var mtouchquiz_questions_not_attempted = [];
var	mtouchquiz_answer_display = [] ;
var	mtouchquiz_single_page = [] ;
var	mtouchquiz_show_hints = [] ;
var	mtouchquiz_show_start = [] ;
var	mtouchquiz_show_final = [] ;
var extra_page = [] ;
var mtouchquiz_show_list = [] ;
var	mtouchquiz_multiple_chances = [] ;
var quiz_finished = [] ;
var exit_warning = [] ;
var quiz_started = [] ;
var mtouchquiz_display_number = [] ;

//Not dependent on mtqid
var	mtouchquiz_have_completed_string = "";
var	mtouchquiz_question_string = "";
var	mtouchquiz_questions_string = "";
var	mtouchquiz_your_score_is_string = "";
var mtouchquiz_correct_string = "Correct";
var mtouchquiz_wrong_string = "Wrong";
var mtouchquiz_partial_string = "Partial-Credit";
var mtouchquiz_exit_warning_string ="You have not finished your quiz. If you leave this page, your progress will be lost.";

window.onbeforeunload = function() {
  if (exit_warning[mtqid] && quiz_started[mtqid] && ! quiz_finished[mtqid]) {
    return mtouchquiz_exit_warning_string;
  }
}

function mtouchquiz_Init() {
	var quizzes_present = [];
	var N = 0;
	jQuery("input:hidden[name=mtouchquiz_id_value]").each(function(){
    quizzes_present.push($(this).val());
	N++;
	});

	var j;
	for (j = 0; j<= N-1; j++){
		mtouchquiz_Start_one(parseInt(quizzes_present[j]));
	}
}

function mtouchquiz_ShowNav(mtqid) {
	mtouchquiz_HideCurrent(mtqid);
	jQuery("#mtouchquiz_question-list-container"+"-"+mtqid).show();
	jQuery("#mtouchquiz_shaded-item-msg"+"-"+mtqid).show();
	jQuery("#mtouchquiz_return_list_t"+"-"+mtqid).show();
	jQuery("#mtouchquiz_return_list_b"+"-"+mtqid).show();
	jQuery("#mtouchquiz_next_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_back_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_show_list"+"-"+mtqid).hide();
		
}

function mtouchquiz_NavClick(q,mtqid) {
	mtouchquiz_HideCurrent(mtqid);
	if ( q > 0 ) {
		current_question[mtqid] = q;
	}
	jQuery("#mtouchquiz_return_list_t"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_return_list_b"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_shaded-item-msg"+"-"+mtqid).hide();
	mtouchquiz_ShowBatch(mtqid);	
}

function mtouchquiz_ResultsMessage(mtqid){
	var ResultsMsg = jQuery("#mtouchquiz_QuizResults"+"-"+mtqid).html();
	var numRatings = parseInt(jQuery("#mtouchquiz_num_ratings"+"-"+mtqid).val());
	var j=1;
	for(j=numRatings;j>0;j--){
		var rating_score = parseInt(jQuery("#mtouchquiz_ratingval-"+j+"-"+mtqid).val());
		if (score_percent[mtqid].toFixed(0) >= rating_score ) {
			var rating_message = jQuery("#mtouchquiz_rating-"+j+"-"+mtqid).html();
			ResultsMsg = ResultsMsg.replace("%%RATING%%",rating_message); // gotta do this
			break;
		}
	}
	
	ResultsMsg=ResultsMsg.replace("%%SCORE%%",mtouchquiz_questions_correct[mtqid]);
	ResultsMsg=ResultsMsg.replace("%%TOTAL%%",mtouchquiz_total_questions[mtqid]);
	ResultsMsg=ResultsMsg.replace("%%WRONG_ANSWERS%%",mtouchquiz_questions_wrong[mtqid]);
	ResultsMsg=ResultsMsg.replace("%%PERCENTAGE%%",score_percent[mtqid].toFixed(0)+"%");
	ResultsMsg=ResultsMsg;
	jQuery("#mtouchquiz_QuizResults"+"-"+mtqid).html(ResultsMsg);
}


function mtouchquiz_NextQuestion(mtqid) {
	mtouchquiz_HideCurrent(mtqid);
	current_question[mtqid]+=mtouchquiz_display_number[mtqid];
	mtouchquiz_ShowBatch(mtqid);	
	
}

function mtouchquiz_ShowBatch(mtqid) {

	if ( mtouchquiz_show_list[mtqid] ) {
		jQuery("#mtouchquiz_show_list"+"-"+mtqid).show();
	}
		
	for (j = current_question[mtqid]; j<= current_question[mtqid] + mtouchquiz_display_number[mtqid] - 1; j++) {
			jQuery("#mtouchquiz_question-" + j+"-"+mtqid).show();
	}
	
	if(mtouchquiz_total_questions[mtqid] + extra_page[mtqid] <= current_question[mtqid] + mtouchquiz_display_number[mtqid] - 1 ) {
		jQuery("#mtouchquiz_next_button"+"-"+mtqid).hide();
	} else {
		jQuery("#mtouchquiz_next_button"+"-"+mtqid).show();
	}
	
	if( current_question[mtqid] <= 1){
		jQuery("#mtouchquiz_back_button"+"-"+mtqid).hide();	
	} else {
		jQuery("#mtouchquiz_back_button"+"-"+mtqid).show();
	}

	if ( mtouchquiz_total_questions[mtqid] + extra_page[mtqid] < current_question[mtqid] + mtouchquiz_display_number[mtqid] ){ //If Pages available to display is less than last question displayed
		if ( extra_page[mtqid] ) {
			jQuery("#mtouchquiz_results-request"+"-"+mtqid).show();
			jQuery("#mtouchquiz_results_button"+"-"+mtqid).show();
			jQuery("#mtouchquiz_next_button"+"-"+mtqid).hide();
		}
	} else {
		jQuery("#mtouchquiz_results-request"+"-"+mtqid).hide();
		jQuery("#mtouchquiz_results_button"+"-"+mtqid).hide();	
	}

	
	document.location.href="#mtouchquiz_view-anchor"+"-"+mtqid;
}

function mtouchquiz_HideCurrent(mtqid) {
	var j;
	for (j = current_question[mtqid]; j<= current_question[mtqid] + mtouchquiz_display_number[mtqid] - 1; j++) {
		if ( j >=1 && j <= mtouchquiz_total_questions[mtqid]) {
			jQuery("#mtouchquiz_question-" + j+"-"+mtqid).hide();
		}
	}
	jQuery("#mtouchquiz_results-request"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_results_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_question-list-container"+"-"+mtqid).hide()	
}

function mtouchquiz_PreviousQuestion(mtqid) {
	mtouchquiz_HideCurrent(mtqid);
	current_question[mtqid] -=mtouchquiz_display_number[mtqid];
	
	if (current_question[mtqid] < 1) {
		current_question[mtqid] = 1;
	}
	mtouchquiz_ShowBatch(mtqid);
}

function mtouchquiz_StartQuiz(mtqid){
		
		current_question[mtqid] = 1;
		jQuery("#mtouchquiz_instructions"+"-"+mtqid).hide();
		jQuery("#mtouchquiz_start_button"+"-"+mtqid).hide();
		jQuery("#mtouchquiz_QuizStatus"+"-"+mtqid).show();
		jQuery("#mtouchquiz_listrow"+"-"+mtqid).show();
		quiz_started[mtqid] = true;
		mtouchquiz_ShowBatch(mtqid);
}



function mtouchquiz_GetResults(mtqid){
	
	quiz_finished[mtqid] = true;
	//Hide a bunch of stuff
	jQuery("#mtouchquiz_next_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_back_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_start_button"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_QuizStatus"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_instructions"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_results-request"+"-"+mtqid).hide();
	jQuery("#mtouchquiz_listrow"+"-"+mtqid).hide();

	var q=1;
	problems_attempted[mtqid]=0;
	for (q = 1; q <= mtouchquiz_total_questions[mtqid]; q++) // Mark every problem as complete
	{
		jQuery("#mtouchquiz_is_answered-"+q+"-"+mtqid).val(1);
		var number_answers =parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
		var N = parseInt(number_answers);
		var attempted_this_one = 0;
		var a=1;
		for (a = 1; a<= N; a++){
			var ever_selected = parseInt(jQuery("#mtouchquiz_was_ever_selected-"+q+"-"+a+"-"+mtqid).val());
			var end_selected = parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val());
			if ( ( ever_selected || end_selected ) && mtouchquiz_answer_display[mtqid] == 2 )	{
				jQuery("#mtouchquiz_row-"+q+"-"+a+"-"+mtqid).addClass("mtouchquiz_selected-row");
				attempted_this_one = 1;
			} else if ( end_selected && mtouchquiz_answer_display[mtqid] != 2 ) {
				jQuery("#mtouchquiz_row-"+q+"-"+a+"-"+mtqid).addClass("mtouchquiz_selected-row");
				attempted_this_one = 1;
			}
			
			if ( mtouchquiz_answer_display[mtqid] != 0 ){
				jQuery("#mtouchquiz_marker-"+q+"-"+a+"-"+mtqid).show();
				jQuery("#mtouchquiz_button-"+q+"-"+a+"-"+mtqid).hide();
			}
		}
		problems_attempted[mtqid]+=attempted_this_one;
		
		jQuery("#mtouchquiz_question-" + q+"-"+mtqid).show();
		var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
		var points_awarded = parseInt(jQuery("#mtouchquiz_points_awarded-"+q+"-"+mtqid).val());
		if ( points_awarded == points_possible ) {
			mtouchquiz_questions_correct[mtqid]++;
		}
		mtouchquiz_Stamp(q,mtqid);		
		var has_explanation = parseInt(jQuery("#mtouchquiz_has_explanation-"+q+"-"+mtqid).val());
		if ( has_explanation && mtouchquiz_answer_display[mtqid] != 0 )  {
			jQuery("#mtouchquiz_question_explanation-"+q+"-"+mtqid).show();	
		}
	}
	
	//mtouchquiz_MarkSelectedRows(); // Mark the answer rows that were selected
	if ( mtouchquiz_answer_display[mtqid] != 2 ){ // Calculate final grade
		mtouchquiz_ScoreBlindly(mtqid);	
	} else {
		mtouchquiz_UpdateStatus(mtqid);	
	}
	
	//Set a bunch of values
	jQuery("#mtouchquiz_total_questions"+"-"+mtqid).val(mtouchquiz_total_questions[mtqid]);
	jQuery("#mtouchquiz_current_score"+"-"+mtqid).val(mtouchquiz_current_score[mtqid]);
	jQuery("#mtouchquiz_max_score"+"-"+mtqid).val(mtouchquiz_max_score[mtqid]);
	jQuery("#mtouchquiz_questions_attempted"+"-"+mtqid).val(problems_attempted[mtqid]);
	jQuery("#mtouchquiz_questions_correct"+"-"+mtqid).val(mtouchquiz_questions_correct[mtqid]);
	mtouchquiz_questions_wrong[mtqid] = problems_attempted[mtqid] - mtouchquiz_questions_correct[mtqid];
	jQuery("#mtouchquiz_questions_wrong"+"-"+mtqid).val(mtouchquiz_questions_wrong[mtqid]);
	
	mtouchquiz_questions_not_attempted[mtqid]=mtouchquiz_total_questions[mtqid] -problems_attempted[mtqid];
	jQuery("#mtouchquiz_questions_not_attempted"+"-"+mtqid).val(mtouchquiz_questions_not_attempted[mtqid]);

	
	
	mtouchquiz_ResultsMessage(mtqid);
	jQuery("#mtouchquiz_QuizResults-bubble"+"-"+mtqid).show();
	jQuery("#mtouchquiz_QuizResultsHighlight"+"-"+mtqid).show();
	if ( mtouchquiz_show_final[mtqid] ) {
		jQuery("#mtouchquiz_QuizResults"+"-"+mtqid).show();
	}
	
	//jQuery("mtouchquiz_email-button").show();
	document.location.href="#mtouchquiz_view-anchor"+"-"+mtqid;
	
}

function mtouchquiz_ShowAllMarkers(mtqid){
	for (q = 1; q <= mtouchquiz_total_questions[mtqid]; q++)
	{
		var number_answers =parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
		var N = parseInt(number_answers)
		var a=1;
		for (a =1; a<= N; a++){
			if (mtouchquiz_answer_display[mtqid] != 0){
				jQuery("#mtouchquiz_marker-"+q+"-"+a+"-"+mtqid).show();
				jQuery("#mtouchquiz_button-"+q+"-"+a+"-"+mtqid).hide();
			}
		}
		
		var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
		var points_awarded = parseInt(jQuery("#mtouchquiz_points_awarded-"+q+"-"+mtqid).val());
		if ( points_awarded == points_possible ) {
			mtouchquiz_questions_correct[mtqid]++;
		}
		mtouchquiz_Stamp(q,mtqid);
		
		
	}
}



function mtouchquiz_UpdateStatus(mtqid){
	
	mtouchquiz_current_score[mtqid] = 0;
	mtouchquiz_max_score[mtqid] = 0;
	questions_answered[mtqid] = 0;
	mtouchquiz_questions_correct[mtqid] = 0;
	var q = 1;
	for (q = 1; q <= mtouchquiz_total_questions[mtqid]; q++)
	{
		var number_answers =parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
		var N = parseInt(number_answers);
		var is_answered = parseInt(jQuery("#mtouchquiz_is_answered-"+q+"-"+mtqid).val());
		var is_correct = parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+mtqid).val());
		var num_attempts = parseInt(jQuery("#mtouchquiz_num_attempts-"+q+"-"+mtqid).val());
		var T = parseInt(num_attempts);
		var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
		var P = parseInt(points_possible);
		var points_awarded = 0;
		if ( is_answered && is_correct) {
			if ( number_answers > 1 ) {
				points_awarded = P*(N-T)/(N-1);
				if ( points_awarded < 0) {
					points_awarded = 0;
				}
			} else {
				points_awarded = P;
			}
		}
		mtouchquiz_current_score[mtqid] += points_awarded;
		jQuery("#mtouchquiz_points_awarded-"+q+"-"+mtqid).val(points_awarded)
		if ( is_correct ) {
			mtouchquiz_questions_correct[mtqid]++;
		}
		
		if ( is_answered ) {
			questions_answered[mtqid]++;
		}
		
		if ( T > 0 || is_answered)
		{
			mtouchquiz_max_score[mtqid]+=P;
		}
	}
	score_percent[mtqid] = 0;
	if( mtouchquiz_max_score[mtqid] > 0) {
		score_percent[mtqid] = mtouchquiz_current_score[mtqid] / mtouchquiz_max_score[mtqid]*100;
	}
	
	
	var status_msg= mtouchquiz_have_completed_string + " " + questions_answered[mtqid] + "/"+ mtouchquiz_total_questions[mtqid] + " " + mtouchquiz_questions_string + "." ;
	if ( mtouchquiz_total_questions[mtqid] == 1 ) {
		status_msg= mtouchquiz_have_completed_string + " " + questions_answered[mtqid] + "/"+ mtouchquiz_total_questions[mtqid] + " " + mtouchquiz_question_string + "." ;
	}
	
	if ( mtouchquiz_answer_display[mtqid] == 2 ){
		status_msg+="<br>"+ mtouchquiz_your_score_is_string + " " +score_percent[mtqid].toFixed(0)+"%.";	
	}
	
	if ( questions_answered[mtqid] == mtouchquiz_total_questions[mtqid] && ! mtouchquiz_show_final[mtqid]) {
		quiz_finished[mtqid] = true;	
	}
	jQuery("#mtouchquiz_QuizStatus"+"-"+mtqid).html(status_msg);
}

function mtouchquiz_Stamp(q,mtqid) {
	if (mtouchquiz_answer_display[mtqid] != 0 ){
		
		if ( mtouchquiz_answer_display[mtqid] == 2 || quiz_finished[mtqid] ){
			var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
			var points_awarded = parseInt(jQuery("#mtouchquiz_points_awarded-"+q+"-"+mtqid).val());
			
			jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).removeClass('mtouchquiz_wrong-stamp');
			jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).removeClass('mtouchquiz_partial-stamp');
			jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).removeClass('mtouchquiz_correct-stamp');
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).removeClass('mtouchquiz_list-item-wrong');
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).removeClass('mtouchquiz_list-item-correct');
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).removeClass('mtouchquiz_list-item-partial');
			jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).html('');
			if ( points_awarded > 0 && points_awarded < points_possible ){
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).addClass('mtouchquiz_partial-stamp');
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).html(mtouchquiz_partial_string);
				jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-partial');	
			} else if ( points_awarded == points_possible ) {
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).addClass('mtouchquiz_correct-stamp');
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).html(mtouchquiz_correct_string);
				jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-correct');
			} else {
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).addClass('mtouchquiz_wrong-stamp');
				jQuery("#mtouchquiz_stamp-"+q+"-"+mtqid).html(mtouchquiz_wrong_string);
				jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-wrong');
			}
		}
	}
}



function mtouchquiz_Start_one(mtqid) {
	
	current_question[mtqid] = 0;
	mtouchquiz_total_questions[mtqid] = 0;
	mtouchquiz_current_score[mtqid] = 0;
	mtouchquiz_max_score[mtqid] = 0;
	score_percent[mtqid] = 0;
	questions_answered[mtqid] = 0;
	mtouchquiz_questions_correct[mtqid] = 0;
	problems_attempted[mtqid] = 0;
	mtouchquiz_questions_wrong[mtqid] = 0;
	mtouchquiz_questions_not_attempted[mtqid]=0;

	quiz_finished[mtqid] = false;
	exit_warning[mtqid] = false;
	quiz_started[mtqid] = false;

	jQuery("#mtouchquiz_javawarning"+"-"+mtqid).hide();
	mtouchquiz_answer_display[mtqid] = parseInt(jQuery("#mtouchquiz_answer_display"+"-"+mtqid).val());
	mtouchquiz_single_page[mtqid] = parseInt(jQuery("#mtouchquiz_single_page"+"-"+mtqid).val());
	mtouchquiz_show_hints[mtqid] = parseInt(jQuery("#mtouchquiz_show_hints"+"-"+mtqid).val());
	mtouchquiz_show_start[mtqid] = parseInt(jQuery("#mtouchquiz_show_start"+"-"+mtqid).val());
	mtouchquiz_show_final[mtqid] = parseInt(jQuery("#mtouchquiz_show_final"+"-"+mtqid).val());
	exit_warning[mtqid] = parseInt(jQuery("#mtouchquiz_show_alerts"+"-"+mtqid).val());
	mtouchquiz_multiple_chances[mtqid] = parseInt(jQuery("#mtouchquiz_multiple_chances"+"-"+mtqid).val());
	mtouchquiz_total_questions[mtqid] = parseInt(jQuery("#mtouchquiz_total_questions"+"-"+mtqid).val()); //jQuery(".mtouchquiz_question").length;
	mtouchquiz_display_number[mtqid]  = parseInt(jQuery("#mtouchquiz_display_number"+"-"+mtqid).val());
	var mtouchquiz_proofread  = parseInt(jQuery("#mtouchquiz_proofread"+"-"+mtqid).val());
	mtouchquiz_show_list[mtqid] =  parseInt(jQuery("#mtouchquiz_show_list_option"+"-"+mtqid).val());
	extra_page[mtqid] = 0;
	if ( mtouchquiz_show_final[mtqid] || mtouchquiz_answer_display[mtqid] != 2 ) {
		extra_page[mtqid] = 1;
	}
	
	mtouchquiz_have_completed_string = jQuery("#mtouchquiz_have_completed_string").html();
	mtouchquiz_question_string = jQuery("#mtouchquiz_question_string").html();
	mtouchquiz_questions_string = jQuery("#mtouchquiz_questions_string").html();
	mtouchquiz_your_score_is_string = jQuery("#mtouchquiz_your_score_is_string").html();
	mtouchquiz_correct_string = jQuery("#mtouchquiz_correct_string").html();
	mtouchquiz_wrong_string = jQuery("#mtouchquiz_wrong_string").html();
	mtouchquiz_partial_string = jQuery("#mtouchquiz_partial_string").html();
	mtouchquiz_exit_warning_string = jQuery("#mtouchquiz_exit_warning_string").html();
	
	if (! mtouchquiz_proofread ) {
		if (mtouchquiz_single_page[mtqid]){
			mtouchquiz_SinglePage(mtqid);	
		} else if (! mtouchquiz_show_start[mtqid]){
			mtouchquiz_StartQuiz(mtqid);	
		} else
		{
			jQuery("#mtouchquiz_instructions"+"-"+mtqid).show();
			jQuery("#mtouchquiz_start_button"+"-"+mtqid).show();	
		}
	}
	
	
}

function mtouchquiz_SinglePage(mtqid)
{
	if ( extra_page[mtqid] ) {
		jQuery("#mtouchquiz_results-request"+"-"+mtqid).show();
		jQuery("#mtouchquiz_results_button"+"-"+mtqid).show();
	}
	
	if (mtouchquiz_show_start[mtqid]) {
		jQuery("#mtouchquiz_instructions"+"-"+mtqid).show();
	}
	jQuery("#mtouchquiz_QuizStatus"+"-"+mtqid).show();
	var j;
	for (j=1;j<=mtouchquiz_total_questions[mtqid];j++){
		
		jQuery("#mtouchquiz_question-" + j+"-"+mtqid).show();
	}
	
	quiz_started[mtqid] = true;
}

function mtouchquiz_RevealAnswer(q,mtqid) {
	var number_answers = parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
	var a=1;
	for (a=1;a<=number_answers;a++){
		var is_correct = parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+a+"-"+mtqid).val());
		if (is_correct){
			jQuery("#mtouchquiz_marker-"+q+"-"+a+"-"+mtqid).show();
			jQuery("#mtouchquiz_button-"+q+"-"+a+"-"+mtqid).hide();
		}
	}
}

function mtouchquiz_ButtonClick (q,a,mtqid)
{
	var is_answered = parseInt(jQuery("#mtouchquiz_is_answered-"+q+"-"+mtqid).val());
	var is_correct = parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+a+"-"+mtqid).val());
	var num_attempts = parseInt(jQuery("#mtouchquiz_num_attempts-"+q+"-"+mtqid).val());
	var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
	var number_correct = parseInt(jQuery("#mtouchquiz_num_correct-"+q+"-"+mtqid).val());
	var was_selected = parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val());
	var number_answers = parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
	var has_explanation = parseInt(jQuery("#mtouchquiz_has_explanation-"+q+"-"+mtqid).val());
	var choices_remain = number_answers;
	
	if( ( (! is_answered) || mtouchquiz_answer_display[mtqid] != 2 ) && ! quiz_finished[mtqid] ) { // 
		if ( was_selected ) {
			jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val('0');
			jQuery("#mtouchquiz_was_ever_selected-"+q+"-"+a+"-"+mtqid).val('0');
			jQuery("#mtouchquiz_image_button-"+q+"-"+a+"-"+mtqid).removeClass('mtouchquiz_letter-selected');
			jQuery("#mtouchquiz_image_button-"+q+"-"+a+"-"+mtqid).removeClass('mtouchquiz_letter-selected-'+q+"-"+mtqid);
		} 
		else
		{
			jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val('1');
			jQuery("#mtouchquiz_image_button-"+q+"-"+a+"-"+mtqid).addClass('mtouchquiz_letter-selected');
			jQuery("#mtouchquiz_image_button-"+q+"-"+a+"-"+mtqid).addClass('mtouchquiz_letter-selected-'+q+"-"+mtqid);
		}
	}
	
	var number_selected = jQuery(".mtouchquiz_letter-selected-"+q+"-"+mtqid).length;
	
	if (! is_answered && number_selected >= number_correct && ! was_selected  ) { //Inc attempts if the q is not complete, enough items selected and the user is selecting an item rather than removing a selection.
		num_attempts++;		
		jQuery("#mtouchquiz_num_attempts-"+q+"-"+mtqid).val(num_attempts);
	}
	
	var question_correct = 1;
	if (number_selected == number_correct) { // If the correct number are selected, see if correct ones are selected
		if (! mtouchquiz_multiple_chances[mtqid] ) {//if you only get one shot, mark this question as done!
			jQuery("#mtouchquiz_is_answered-"+q+"-"+mtqid).val('1');
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-complete');
			if (mtouchquiz_answer_display[mtqid] == 2){
				mtouchquiz_RevealAnswer(q,mtqid);
			}
			is_answered = 1;
		}
		for (j=1;j<=number_answers;j++){
			if ( parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+j+"-"+mtqid).val()) ) { // This choice is correct
				if ( ! parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+j+"-"+mtqid).val()) ) { // But it was not selected
					question_correct = 0;
				}
			}
		}
	} else { // Wrong number selected so there's no way it's correct
		question_correct = 0;	
	}
	
	jQuery("#mtouchquiz_is_correct-"+q+"-"+mtqid).val(question_correct);

	
	if ( number_selected >= number_correct && mtouchquiz_show_hints[mtqid] ) { // Wrong answer, but sufficient number to show hints.
		for (j=1;j<=number_answers;j++){
			var has_hint = parseInt(jQuery("#mtouchquiz_has_hint-"+q+"-"+j+"-"+mtqid).val());
			if( has_hint && parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+j+"-"+mtqid).val()) ) {
				jQuery("#mtouchquiz_hint-"+q+"-"+j+"-"+mtqid).show();
			}
		}
	}
	
	var has_hint = parseInt(jQuery("#mtouchquiz_has_hint-"+q+"-"+a+"-"+mtqid).val()); // Clicked answer has hint? This is for after the question is correct, but hints can still be revealed
	
	if ( is_answered && has_hint && mtouchquiz_show_hints[mtqid] ){ // Question is complete, but still display hint of clicked value.
		jQuery("#mtouchquiz_hint-"+q+"-"+a+"-"+mtqid).show();
	}
	var how_many_left = number_answers;
	if ( number_selected >= number_correct  ) { // Sufficiently many choices have been selected to trigger grading. Mark correct and wrong choices
		for (j=1; j<=number_answers; j++ ){
			if ( parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+j+"-"+mtqid).val() ) ){
				if (mtouchquiz_answer_display[mtqid] == 2 ){
					jQuery("#mtouchquiz_marker-"+q+"-"+j+"-"+mtqid).show();
					jQuery("#mtouchquiz_button-"+q+"-"+j+"-"+mtqid).hide();
				}
				
				if ( ! parseInt(jQuery("#mtouchquiz_was_ever_selected-"+q+"-"+j+"-"+mtqid).val()) ) { // If this is the first time it is selected, keep track of when it was selected
					if ( (num_attempts == 1 || mtouchquiz_multiple_chances[mtqid]) ){
						jQuery("#mtouchquiz_was_ever_selected-"+q+"-"+j+"-"+mtqid).val(num_attempts);
					}
				} else {
					how_many_left--;
				}
				
				if (( ! parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+j+"-"+mtqid).val()) ) && (mtouchquiz_answer_display[mtqid] == 2) ) { //Unselect the wrong answers automatically since they will be hidden and user cannot do it
					jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).removeClass('mtouchquiz_letter-selected');
					jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).removeClass('mtouchquiz_letter-selected-'+q+"-"+mtqid);
				}
			}
		}
	}
	
	if ( mtouchquiz_answer_display[mtqid] == 2 ){
		if ( question_correct ) {
			jQuery("#mtouchquiz_is_answered-"+q+"-"+mtqid).val('1');
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-complete');
		}
		
		if ( how_many_left == 0) { //Nothing left which is unselected so it's over!
			jQuery("#mtouchquiz_list_item-"+q+"-"+mtqid).addClass('mtouchquiz_list-item-complete');
		}
		if( has_explanation && ( is_answered || question_correct )){
				jQuery("#mtouchquiz_question_explanation-"+q+"-"+mtqid).show();	
		}
	}
	
	
	if ( mtouchquiz_answer_display[mtqid] != 2 && number_correct == 1 && ! quiz_finished[mtqid]  ) {// If there is only one answer, we will not allow multiple selects to be nice, even though it's a hint!
		var j=1;
		for (j=1; j<=number_answers; j++ ){
			if (j != a) {
				jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).removeClass('mtouchquiz_letter-selected');
				jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).removeClass('mtouchquiz_letter-selected-'+q+"-"+mtqid);
				jQuery("#mtouchquiz_was_selected-"+q+"-"+j+"-"+mtqid).val('0');
				jQuery("#mtouchquiz_was_ever_selected-"+q+"-"+j+"-"+mtqid).val('0'); // does this defeat purpose of was_ever_selected? Don't think so.
			} else if(parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid)) ) {
				jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).addClass('mtouchquiz_letter-selected');
				jQuery("#mtouchquiz_image_button-"+q+"-"+j+"-"+mtqid).addClass('mtouchquiz_letter-selected-'+q+"-"+mtqid);
			}
		}
	}
	
	mtouchquiz_UpdateStatus(mtqid);
	mtouchquiz_Stamp(q,mtqid); // Must follow status update where points are calculated
	return;
}

function mtouchquiz_ScoreBlindly(mtqid){ // This assumes that there was only one attempt for each problem and ignores number of attempts. 
	mtouchquiz_current_score[mtqid] = 0;
	mtouchquiz_max_score[mtqid] = 0;
	questions_answered[mtqid] = mtouchquiz_total_questions[mtqid];
	mtouchquiz_questions_correct[mtqid] = 0;
	var q = 1;
	for (q = 1; q <= mtouchquiz_total_questions[mtqid]; q++)
	{
		var number_answers =parseInt(jQuery("#mtouchquiz_num_ans-"+q+"-"+mtqid).val());
		var N = parseInt(number_answers);
		var points_possible = parseInt(jQuery("#mtouchquiz_is_worth-"+q+"-"+mtqid).val());
		var P = parseInt(points_possible);
		var number_correct = parseInt(jQuery("#mtouchquiz_num_correct-"+q+"-"+mtqid).val());
		var points_awarded = 0;

		
		var parts_correct = 0;
		var selected_something = 0; // To ensure partial credit isn'__ given for doing nothing!
		var a=1;
		for ( a=1; a<=number_answers; a++) 
		{
			if (parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val()) == parseInt(jQuery("#mtouchquiz_is_correct-"+q+"-"+a+"-"+mtqid).val())) 
			{
				parts_correct++;	
			}
			
			if ( parseInt(jQuery("#mtouchquiz_was_selected-"+q+"-"+a+"-"+mtqid).val())) 
			{
				selected_something = 1;
			}
		}
		
		if ( number_correct == 1 && parts_correct == number_answers ) 
		{
				points_awarded = P;
		} else if ( number_correct > 1 ) 
		{
			points_awarded = selected_something *P * parts_correct / N;
		}
			
		mtouchquiz_current_score[mtqid] += points_awarded;
		jQuery("#mtouchquiz_points_awarded-"+q+"-"+mtqid).val(points_awarded);
		if ( points_awarded == points_possible ){
			mtouchquiz_questions_correct[mtqid]++;
		}
		mtouchquiz_Stamp(q);
		
		mtouchquiz_max_score[mtqid]+=P;
	}
	score_percent[mtqid] = 0;
	if( mtouchquiz_max_score[mtqid] > 0) {
		score_percent[mtqid] = mtouchquiz_current_score[mtqid] / mtouchquiz_max_score[mtqid]*100;
	}
}



jQuery(document).ready(mtouchquiz_Init);
