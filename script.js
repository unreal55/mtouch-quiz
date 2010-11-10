var current_question = 0;
var total_questions = 0;
var current_score = 0;
var max_score = 0;
var score_percent = 0;
var questions_answered = 0;
var questions_correct = 0;
var problems_attempted = 0;
var questions_wrong = 0;
var questions_not_attempted=0;

var	answer_display = 0;
var	single_page = 0;
var	show_hints = 1;
var	show_start = 1;
var	show_final = 1;
var extra_page = 1;
var	multiple_chances = 1;
var quiz_finished = false;
var exit_warning = false;
var quiz_started = false;
var display_number = 1;
var	have_completed_string = "";
var	question_string = "";
var	questions_string = "";
var	your_score_is_string = "";
var correct_string = "Correct";
var wrong_string = "Wrong";
var partial_string = "Partial-Credit";
var exit_warning_string ="You have not finished your quiz. If you leave this page, your progress will be lost.";

window.onbeforeunload = function() {
  if (exit_warning && quiz_started && ! quiz_finished) {
    return exit_warning_string;
  }
}

function mtouchquizResultsMessage(){
	var ResultsMsg = jQuery("#QuizResults").html();
	var numRatings = parseInt(jQuery("#num_ratings").val());
	var j=1;
	for(j=numRatings;j>0;j--){
		var rating_score = parseInt(jQuery("#ratingval-"+j).val());
		if (score_percent.toFixed(0) >= rating_score ) {
			var rating_message = jQuery("#rating-"+j).html();
			ResultsMsg = ResultsMsg.replace("%%RATING%%",rating_message); // gotta do this
			break;
		}
	}
	
	ResultsMsg=ResultsMsg.replace("%%SCORE%%",questions_correct);
	ResultsMsg=ResultsMsg.replace("%%TOTAL%%",total_questions);
	ResultsMsg=ResultsMsg.replace("%%WRONG_ANSWERS%%",questions_wrong);
	ResultsMsg=ResultsMsg.replace("%%PERCENTAGE%%",score_percent.toFixed(0)+"%");
	ResultsMsg=ResultsMsg;
	jQuery("#QuizResults").html(ResultsMsg);
}

function mtouchquizNavClick(q) {
	mtouchquizHideCurrent();
	current_question = q;
	mtouchquizShowBatch();	
}


function mtouchquizNextQuestion() {
	mtouchquizHideCurrent();
	current_question+=display_number;
	mtouchquizShowBatch();	
	
}

function mtouchquizShowBatch() {

	
	for (j = current_question; j<= current_question + display_number - 1; j++) {
			jQuery("#question-" + j).show();
	}

	if ( total_questions <= current_question ){
		if ( extra_page && current_question + display_number - 1 > total_questions ) {
			jQuery("#mtouchquiz-results-request").show();
			jQuery("#results_button").show();
		}
	} else {
		jQuery("#mtouchquiz-results-request").hide();
		jQuery("#results_button").hide();	
	}

	if(total_questions + extra_page <= current_question ) {
		jQuery("#next_button").hide();
	} else {
		jQuery("#next_button").show();
	}
	
	if( current_question == 1){
		jQuery("#back_button").hide();	
	} else {
		jQuery("#back_button").show();
	}
	
	if ( current_question <= total_questions ){
		jQuery("#mtouchquiz_nav_item-" + current_question).addClass('mtouchquiz-nav-item-selected');
		jQuery("#mtouchquiz_nav_item-end").removeClass('mtouchquiz-nav-item-selected');
		
	} else {
		jQuery("#mtouchquiz_nav_item-end").addClass('mtouchquiz-nav-item-selected');
	}
	
	document.location.href="#mtouchquiz-view-anchor";
}

function mtouchquizHideCurrent() {
	var j;
	for (j = current_question; j<= current_question + display_number - 1; j++) {
		if ( j >=1 && j <= total_questions) {
			jQuery("#question-" + j).hide();
			jQuery("#mtouchquiz_nav_item-" + j).removeClass('mtouchquiz-nav-item-selected');
		}
	}
	jQuery("#mtouchquiz-results-request").hide();
	jQuery("#results_button").hide();	
}

function mtouchquizPreviousQuestion() {
	mtouchquizHideCurrent();
	current_question -=display_number;
	
	if (current_question < 1) {
		current_question = 1;
	}
	mtouchquizShowBatch();
	
//	if (current_question <= total_questions){
//		jQuery("#question-" + current_question).hide();
//		jQuery("#mtouchquiz_nav_item-"+current_question).removeClass('mtouchquiz-nav-item-selected');
//	} else {	
//		jQuery("#mtouchquiz-results-request").hide();
//		jQuery("#results_button").hide();
//	}
//	current_question--;
//	jQuery("#question-" + current_question).show();
//	jQuery("#mtouchquiz_nav_item-"+current_question).addClass('mtouchquiz-nav-item-selected');
//	if(total_questions + extra_page <= current_question) {
//		jQuery("#next_button").hide();
//	} else {
//		jQuery("#next_button").show();
//	}
//	
//	if( current_question == 1){
//		jQuery("#back_button").hide();	
//	} else {
//		jQuery("#back_button").show();
//	}
//	
//	document.location.href="#mtouchquiz-view-anchor";
}

function mtouchquizStartQuiz(){
		current_question = 1;
		//jQuery("#back_button").hide(); //do not need
		jQuery("#mtouchquiz-instructions").hide();
		jQuery("#start_button").hide();
		jQuery("#QuizStatus").show();
		jQuery("#mtouchquiznavrow").show();
		quiz_started = true;
		//jQuery("#next_button").show();
		mtouchquizShowBatch();
		//jQuery("#mtouchquiz_nav_item-"+current_question).addClass('mtouchquiz-nav-item-selected');
		//if (! show_final) {//do not need
			//jQuery("#mtouchquiz-results-request").hide();
		//}
		document.location.href="#mtouchquiz-view-anchor";
}



function mtouchquizGetResults(){
	
	quiz_finished = true;
	//Hide a bunch of stuff
	jQuery("#next_button").hide();
	jQuery("#back_button").hide();
	jQuery("#start_button").hide();
	jQuery("#QuizStatus").hide();
	jQuery("#mtouchquiz-instructions").hide();
	jQuery("#mtouchquiz-results-request").hide();
	jQuery("#mtouchquiznavrow").hide();

	var q=1;
	problems_attempted=0;
	for (q = 1; q <= total_questions; q++) // Mark every problem as complete
	{
		jQuery("#is_answered-"+q).val(1);
		var number_answers =parseInt(jQuery("#num_ans-"+q).val());
		var N = parseInt(number_answers);
		var attempted_this_one = 0;
		var a=1;
		for (a = 1; a<= N; a++){
			var ever_selected = parseInt(jQuery("#was_ever_selected-"+q+"-"+a).val());
			var end_selected = parseInt(jQuery("#was_selected-"+q+"-"+a).val());
			if ( ( ever_selected || end_selected ) && answer_display == 2 )	{
				jQuery("#row-"+q+"-"+a).addClass("mtouchquiz-selected-row");
				attempted_this_one = 1;
			} else if ( end_selected && answer_display != 2 ) {
				jQuery("#row-"+q+"-"+a).addClass("mtouchquiz-selected-row");
				attempted_this_one = 1;
			}
			
			if ( answer_display != 0 ){
				//mtouchquizShowAllMarkers();
				jQuery("#marker-"+q+"-"+a).show();
				jQuery("#button-"+q+"-"+a).hide();
			}
		}
		problems_attempted+=attempted_this_one;
		
		jQuery("#question-" + q).show();
		var points_possible = parseInt(jQuery("#is_worth-"+q).val());
		var points_awarded = parseInt(jQuery("#points_awarded-"+q).val());
		if ( points_awarded == points_possible ) {
			questions_correct++;
		}
		mtouchquizStamp(q);		
		var has_explanation = parseInt(jQuery("#has_explanation-"+q).val());
		if ( has_explanation && answer_display != 0 )  {
			jQuery("#question_explanation-"+q).show();	
		}
	}
	
	//mtouchquizMarkSelectedRows(); // Mark the answer rows that were selected
	if ( answer_display != 2 ){ // Calculate final grade
		mtouchquizScoreBlindly();	
	} else {
		mtouchquizUpdateStatus();	
	}
	
	//Set a bunch of values
	jQuery("#total_questions").val(total_questions);
	jQuery("#current_score").val(current_score);
	jQuery("#max_score").val(max_score);
	jQuery("#questions_attempted").val(problems_attempted);
	jQuery("#questions_correct").val(questions_correct);
	questions_wrong = problems_attempted - questions_correct;
	jQuery("#questions_wrong").val(questions_wrong);
	
	questions_not_attempted=total_questions -problems_attempted;
	jQuery("#questions_not_attempted").val(questions_not_attempted);

	
	
	mtouchquizResultsMessage();
	jQuery("#QuizResults-bubble").show();
	jQuery("#QuizResultsHighlight").show();
	if ( show_final ) {
		jQuery("#QuizResults").show();
	}
	
	document.location.href="#mtouchquiz-view-anchor";
	
}

function mtouchquizShowAllMarkers(){
	for (q = 1; q <= total_questions; q++)
	{
		var number_answers =parseInt(jQuery("#num_ans-"+q).val());
		var N = parseInt(number_answers)
		var a=1;
		for (a =1; a<= N; a++){
			if (answer_display != 0){
				jQuery("#marker-"+q+"-"+a).show();
				jQuery("#button-"+q+"-"+a).hide();
			}
		}
		
		var points_possible = parseInt(jQuery("#is_worth-"+q).val());
		var points_awarded = parseInt(jQuery("#points_awarded-"+q).val());
		if ( points_awarded == points_possible ) {
			questions_correct++;
		}
		mtouchquizStamp(q);
		
		
	}
}



function mtouchquizUpdateStatus(){
	
	current_score = 0;
	max_score = 0;
	questions_answered = 0;
	questions_correct = 0;
	var q = 1;
	for (q = 1; q <= total_questions; q++)
	{
		var number_answers =parseInt(jQuery("#num_ans-"+q).val());
		var N = parseInt(number_answers);
		var is_answered = parseInt(jQuery("#is_answered-"+q).val());
		var is_correct = parseInt(jQuery("#is_correct-"+q).val());
		var num_attempts = parseInt(jQuery("#num_attempts-"+q).val());
		var T = parseInt(num_attempts);
		var points_possible = parseInt(jQuery("#is_worth-"+q).val());
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
		current_score += points_awarded;
		jQuery("#points_awarded-"+q).val(points_awarded)
		if ( is_correct ) {
			questions_correct++;
		}
		
		if ( is_answered ) {
			questions_answered++;
		}
		
		if ( T > 0 || is_answered)
		{
			max_score+=P;
		}
	}
	score_percent = 0;
	if( max_score > 0) {
		score_percent = current_score / max_score*100;
	}
	
	
	var status_msg= have_completed_string + " " + questions_answered + "/"+ total_questions + " " + questions_string + "." ;
	if ( total_questions == 1 ) {
		status_msg= have_completed_string + " " + questions_answered + "/"+ total_questions + " " + question_string + "." ;
	}
	
	if ( answer_display == 2 ){
		status_msg+="<br>"+ your_score_is_string + " " +score_percent.toFixed(0)+"%.";	
	}
	
	if ( questions_answered == total_questions && ! show_final) {
		quiz_finished = true;	
	}
	jQuery("#QuizStatus").html(status_msg);
}

function mtouchquizStamp(q) {
	if (answer_display != 0 ){
		
		if ( answer_display == 2 || quiz_finished ){
			var points_possible = parseInt(jQuery("#is_worth-"+q).val());
			var points_awarded = parseInt(jQuery("#points_awarded-"+q).val());
			
			jQuery("#mtouchquiz_stamp-"+q).removeClass('mtouchquiz-wrong-stamp');
			jQuery("#mtouchquiz_stamp-"+q).removeClass('mtouchquiz-partial-stamp');
			jQuery("#mtouchquiz_stamp-"+q).removeClass('mtouchquiz-correct-stamp');
			jQuery("#mtouchquiz_nav_item-"+q).removeClass('mtouchquiz-nav-item-wrong');
			jQuery("#mtouchquiz_nav_item-"+q).removeClass('mtouchquiz-nav-item-correct');
			
			jQuery("#mtouchquiz_stamp-"+q).html('');
			if ( points_awarded > 0 && points_awarded < points_possible ){
				jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-partial-stamp');
				jQuery("#mtouchquiz_stamp-"+q).html(partial_string);
				jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-correct');	
			} else if ( points_awarded == points_possible ) {
				jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-correct-stamp');
				jQuery("#mtouchquiz_stamp-"+q).html(correct_string);
				jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-correct');
			} else {
				jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-wrong-stamp');
				jQuery("#mtouchquiz_stamp-"+q).html(wrong_string);
				jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-wrong');
			}
		}
	}
}

function mtouchquizInit() {

	jQuery("#javawarning").hide();
	answer_display = parseInt(jQuery("#answer_display").val());
	single_page = parseInt(jQuery("#single_page").val());
	show_hints = parseInt(jQuery("#show_hints").val());
	show_start = parseInt(jQuery("#show_start").val());
	show_final = parseInt(jQuery("#show_final").val());
	exit_warning = parseInt(jQuery("#show_alerts").val());
	multiple_chances = parseInt(jQuery("#multiple_chances").val());
	total_questions = parseInt(jQuery("#total_questions").val()); //jQuery(".mtouchquiz-question").length;
	display_number  = parseInt(jQuery("#display_number").val())
	extra_page = 0;
	if ( show_final || answer_display != 2 ) {
		extra_page = 1;
	}
	//if (total_questions == 1) {
	//	jQuery("#QuizStatus").html("There is "+ total_questions + " question to complete.");
	//} else if ( total_questions > 1) {
	//	jQuery("#QuizStatus").html("There are "+ total_questions + " questions to complete.");	
	//}
	//if(total_questions == 1) { //do not need this
		//jQuery("#next_button").hide();
	//} 
	

	
	have_completed_string = jQuery("#have_completed_string").html();
	question_string = jQuery("#question_string").html();
	questions_string = jQuery("#questions_string").html();
	your_score_is_string = jQuery("#your_score_is_string").html();
	correct_string = jQuery("#correct_string").html();
	wrong_string = jQuery("#wrong_string").html();
	partial_string = jQuery("#partial_string").html();
	exit_warning_string = jQuery("#exit_warning_string").html();
	
	if (single_page){
		mtouchquizSinglePage();	
	} else if (! show_start){
		mtouchquizStartQuiz();	
	} else
	{
		jQuery("#mtouchquiz-instructions").show();
		jQuery("#start_button").show();	
	}
	
	
}

function mtouchquizSinglePage()
{
	//jQuery("#next_button").hide(); //do not need this
	//jQuery("#back_button").hide(); //do not need this
	//jQuery("#start_button").hide(); //do not need this
	if ( extra_page ) {
		jQuery("#mtouchquiz-results-request").show();
		jQuery("#results_button").show();
	}
	
	if (show_start) {
		jQuery("#mtouchquiz-instructions").show();
	}
	jQuery("#mtouchquiz-status").show();
	var j;
	for (j=1;j<=total_questions;j++){
		
		jQuery("#question-" + j).show();
	}
	
	quiz_started = true;
}

function mtouchquizRevealAnswer(q) {
	var number_answers = parseInt(jQuery("#num_ans-"+q).val());
	var a=1;
	for (a=1;a<=number_answers;a++){
		var is_correct = parseInt(jQuery("#is_correct-"+q+"-"+a).val());
		if (is_correct){
			jQuery("#marker-"+q+"-"+a).show();
			jQuery("#button-"+q+"-"+a).hide();
		}
	}
}

function mtouchquizButton_click (q,a)
{
	var is_answered = parseInt(jQuery("#is_answered-"+q).val());
	var is_correct = parseInt(jQuery("#is_correct-"+q+"-"+a).val());
	var num_attempts = parseInt(jQuery("#num_attempts-"+q).val());
	var points_possible = parseInt(jQuery("#is_worth-"+q).val());
	var number_correct = parseInt(jQuery("#num_correct-"+q).val());
	var was_selected = parseInt(jQuery("#was_selected-"+q+"-"+a).val());
	var number_answers = parseInt(jQuery("#num_ans-"+q).val());
	var has_explanation = parseInt(jQuery("#has_explanation-"+q).val());
	
	if( ( (! is_answered) || answer_display != 2 ) && ! quiz_finished ) { // 
		if ( was_selected ) {
			jQuery("#was_selected-"+q+"-"+a).val('0');
			jQuery("#was_ever_selected-"+q+"-"+a).val('0');
			jQuery("#image_button-"+q+"-"+a).removeClass('mtouchquiz-letter-selected');
			jQuery("#image_button-"+q+"-"+a).removeClass('mtouchquiz-letter-selected-'+q);
		} 
		else
		{
			jQuery("#was_selected-"+q+"-"+a).val('1');
			jQuery("#image_button-"+q+"-"+a).addClass('mtouchquiz-letter-selected');
			jQuery("#image_button-"+q+"-"+a).addClass('mtouchquiz-letter-selected-'+q);
		}
	}
	
	var number_selected = jQuery(".mtouchquiz-letter-selected-"+q).length;
	
	if (! is_answered && number_selected >= number_correct && ! was_selected  ) { //Inc attempts if the q is not complete, enough items selected and the user is selecting an item rather than removing a selection.
		num_attempts++;		
		jQuery("#num_attempts-"+q).val(num_attempts);
	}
	
	var question_correct = 1;
	if (number_selected == number_correct) { // If the correct number are selected, see if correct ones are selected
		if (! multiple_chances ) {//if you only get one shot, mark this question as done!
			jQuery("#is_answered-"+q).val('1');
			if (answer_display == 2){
				mtouchquizRevealAnswer(q);
			}
			is_answered = 1;
		}
		for (j=1;j<=number_answers;j++){
			if ( parseInt(jQuery("#is_correct-"+q+"-"+j).val()) ) { // This choice is correct
				if ( ! parseInt(jQuery("#was_selected-"+q+"-"+j).val()) ) { // But it was not selected
					question_correct = 0;
				}
			}
		}
	} else { // Wrong number selected so there's no way it's correct
		question_correct = 0;	
	}
	
	jQuery("#is_correct-"+q).val(question_correct);

	
	if ( number_selected >= number_correct && show_hints ) { // Wrong answer, but sufficient number to show hints.
		for (j=1;j<=number_answers;j++){
			var has_hint = parseInt(jQuery("#has_hint-"+q+"-"+j).val());
			if( has_hint && parseInt(jQuery("#was_selected-"+q+"-"+j).val()) ) {
				jQuery("#hint-"+q+"-"+j).show();
			}
		}
	}
	
	var has_hint = parseInt(jQuery("#has_hint-"+q+"-"+a).val()); // Clicked answer has hint? This is for after the question is correct, but hints can still be revealed
	if ( is_answered && has_hint && show_hints ){ // Question is complete, but still display hint of clicked value.
		jQuery("#hint-"+q+"-"+a).show();
	}
	
	if ( number_selected >= number_correct  ) { // Sufficiently many choices have been selected to trigger grading. Mark correct and wrong choices
		for (j=1; j<=number_answers; j++ ){
			if ( parseInt(jQuery("#was_selected-"+q+"-"+j).val() ) ){
				if (answer_display == 2 ){
					jQuery("#marker-"+q+"-"+j).show();
					jQuery("#button-"+q+"-"+j).hide();
				}
				
				if ( ! parseInt(jQuery("#was_ever_selected-"+q+"-"+j).val()) ) { // If this is the first time it is selected, keep track of when it was selected
					if ( (num_attempts == 1 || multiple_chances) ){
						jQuery("#was_ever_selected-"+q+"-"+j).val(num_attempts);
					}
				}
				
				if (( ! parseInt(jQuery("#is_correct-"+q+"-"+j).val()) ) && (answer_display == 2) ) { //Unselect the wrong answers automatically since they will be hidden and user cannot do it
					jQuery("#image_button-"+q+"-"+j).removeClass('mtouchquiz-letter-selected');
					jQuery("#image_button-"+q+"-"+j).removeClass('mtouchquiz-letter-selected-'+q);
				}
			}
		}
	}
	
	if ( answer_display == 2 ){ // Stamp the ones that are right or wrong
		if ( question_correct ) {
			jQuery("#is_answered-"+q).val('1');
		}
//			if( num_attempts == 1){ 
//				jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-correct-stamp');
//				jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-correct');	
//			} else if (num_attempts > 1) {
//				jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-partial-stamp');
//				jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-correct');
//			}
//		} else if ( number_selected >= number_correct  ) {
//			jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-wrong-stamp');
//			jQuery("#mtouchquiz_nav_item-"+q).addClass('mtouchquiz-nav-item-wrong');	
//		}
//		
		if( has_explanation && ( is_answered || question_correct )){
				jQuery("#question_explanation-"+q).show();	
		}
	}
	
	if ( answer_display != 2 && number_correct == 1 && ! quiz_finished  ) {// If there is only one answer, we will not allow multiple selects to be nice, even though it's a hint!
		var j=1;
		for (j=1; j<=number_answers; j++ ){
			if (j != a) {
				jQuery("#image_button-"+q+"-"+j).removeClass('mtouchquiz-letter-selected');
				jQuery("#image_button-"+q+"-"+j).removeClass('mtouchquiz-letter-selected-'+q);
				jQuery("#was_selected-"+q+"-"+j).val('0');
				jQuery("#was_ever_selected-"+q+"-"+j).val('0'); // does this defeat purpose of was_ever_selected?
			} else if(parseInt(jQuery("#was_selected-"+q+"-"+a)) ) {
				jQuery("#image_button-"+q+"-"+j).addClass('mtouchquiz-letter-selected');
				jQuery("#image_button-"+q+"-"+j).addClass('mtouchquiz-letter-selected-'+q);
			}
		}
	}
	
	mtouchquizUpdateStatus();
	mtouchquizStamp(q); // Must follow status update when points are calculated
	return;
}

function mtouchquizScoreBlindly(){ // This assumes that there was only one attempt for each problem and ignores number of attempts. 
	current_score = 0;
	max_score = 0;
	questions_answered = total_questions;
	questions_correct = 0;
	var q = 1;
	for (q = 1; q <= total_questions; q++)
	{
		var number_answers =parseInt(jQuery("#num_ans-"+q).val());
		var N = parseInt(number_answers);
		var points_possible = parseInt(jQuery("#is_worth-"+q).val());
		var P = parseInt(points_possible);
		var number_correct = parseInt(jQuery("#num_correct-"+q).val());
		var points_awarded = 0;
		//if ( is_correct) {
		//	points_awarded=P;
		//	current_score +=points_awarded;				
		//}
		
		var parts_correct = 0;
		var selected_something = 0; // To ensure partial credit isn'__ given for doing nothing!
		//if (! is_correct && number_correct > 1) {// This awards partial credit to problems with more than 1 correct answer even if not fully correct
		var a=1;
		for ( a=1; a<=number_answers; a++) 
		{
			if (parseInt(jQuery("#was_selected-"+q+"-"+a).val()) == parseInt(jQuery("#is_correct-"+q+"-"+a).val())) 
			{
				parts_correct++;	
			}
			
			if ( parseInt(jQuery("#was_selected-"+q+"-"+a).val())) 
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
			
		current_score += points_awarded;
		jQuery("#points_awarded-"+q).val(points_awarded);
		if ( points_awarded == points_possible ){
			questions_correct++;
		}
		mtouchquizStamp(q);
//		if ( points_awarded > 0 && points_awarded < points_possible ) 
//		{
//			jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-partial-stamp');	
//		} else 
//			jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-correct-stamp');
//		} else 
//		{
//			jQuery("#mtouchquiz_stamp-"+q).addClass('mtouchquiz-wrong-stamp');
//		}
		
		max_score+=P;
	}
	score_percent = 0;
	if( max_score > 0) {
		score_percent = current_score / max_score*100;
	}
}



jQuery(document).ready(mtouchquizInit);
