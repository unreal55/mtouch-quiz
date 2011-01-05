=== mTouch Quiz ===
Contributors: gmichaelguy
Donate link: http://gmichaelguy.com/quizplugin/
Tags: quiz, question, answer, test, touch, education, learning, elearning
Requires at least: 2.8
Tested up to: 3.0.4
Stable tag: 2.1.0

mTouch Quiz lets you add quizzes to your site. This plugin was designed with learning, touch friendliness and versatility in mind.


== Description ==

Create a multiple choice quiz (or exam). This plugin was written with learning and mobility in mind.  The quiz interface is touch friendly. You can: specify hints based on answer selection; give a detailed explanation of the solution; choose multiple correct answers; specify when the correct answers are displayed; specify if a question may be attempted only once or many times; specify point values for each question; include customized start and finish screens; randomly order questions and/or answers; and more.  This plugin was built by pillaging the Quizzin plugin written by Binny V A, but please do not blame him for my ruining his plugin!

== Installation ==
Best way to install is via the "Add New Plugins" feature inside your WordPress dashboard. Or...

1. Download the zipped file.
1. Extract and upload the contents of the folder to /wp-contents/plugins/ folder
1. Go to the Plugin management page of WordPress admin section and enable the 'mTouch Quiz' plugin
1. Go to the mTouch Quiz Management page (in a new group) to create or edit the quiz.
1. Add the shortcode (and any options) [mtouchquiz #] to a post to display your quiz!

== Frequently Asked Questions ==

= Where can I find the most up to date version of the FAQ? =
You can find the most up to date version at the [ homepage of mTouch Quiz Plugin](http://gmichaelguy.com/mtouchquiz "mTouch Quiz Plugin Site")

= Can a question have more than one correct answer? =
Yes. Each question can have as many correct answers as you like.

= How does scoring work? =
If the correct answer is selected on the first attempt, full credit is assigned for the problem. However, if you allow multiple chances to answer each question, partial credit is calculated based on the number of attempts as well as the number of correct answers to the question.

= Can I give Hints based on the answer selected? =
Yes. This was one of the main reasons I wrote this plugin. Learning often requires more feedback than just right or wrong.

= Can questions be assigned point values/weights? =
Yes. It is at the bottom of each question, right above the Save button. The default value is 100.

= How do I get the quiz to show up on my page? =
You simply include the shortcode mtouchquiz id# options inside square brackets anywhere on your page. The id# is listed in the leftmost column on the Manage Quiz page and at the top of the Manage Questions page.

= Once I set the quiz options such a single page and randomization, can I use different options on different versions of the same quiz? =
Yes! Just set the options on the shortcode when you embed the quiz in your page.

= Where can I find out about the configurable options for the shortcode? =
Next to each option on the Edit Quiz page, there is a column to the right, which indicates what to add to the shortcode to set the options. I think they are clear, but ask if you are unsure.

= What arguments can I use on the shortcode? =
Each argument must be followed by an equals sign and the selection in quotations. Here they are: questions (number of questions to display if you do not choose random questions, it will be the first ones in order), singlepage, multiplechances, hints, startscreen, finalscreen, randomq, randoma, showanswers.

= What does Delimit mean next to the answer choices in the question editor? =
If you want to enclose all your answers in some kind of delimiter, select this option to save time. For example, you may want to modify the style or formatting or enclose the answer choice in some LaTeX code, as the default is set.

= Can I change the default delimiters I see in the answers? =
Yes. You can change that in the options page. You will have to make any changes manually to answers already in the database, however.

= Does this work with multi-site? =
Yes, as far as I can test it does! Any user with Author role or higher should have access to this plugin. My site is a 3.0+ multi-site enabled site, and it works fine.

= Who can access to this plugin within my WordPress site? =
Any user with Author role or higher has access to this plugin. This can be changed to require higher role, if desired.

= Does this work in a sidebar? =
It worked in mine when I tried it. Just make sure you sidebar executes and shortcodes contained inside it.

= Can I keep track of statistics of which answers are selected? =
Look for this in the future!

= Can I have the results of the quiz emailed to me? =
Look for this in the future if enough people want it!

= Does this plugin support international translation? =
I hope so. I've tested this some. I uploaded the pot and po files in the lang subdirectory. Put your file named mtouchquiz-language.po/mo in the lang subdirectory. No graphics have words on them as of v 1.05.

= Can I do this or that? =
I don't know.  Ask me. Perhaps if it seems useful to a large number of people, I can find a way to make it do this or that.

= Where can I get more help or get a question answered? =
Go to the plugin website at the [ homepage of mTouch Quiz Plugin](http://gmichaelguy.com/mtouchquiz "mTouch Quiz Plugin Site") and contact me using the methods listed there.



== Screenshots ==

1. A Typical Question on a Desktop Browser
2. A Typical Question After a Selection on a Desktop Browser
3. A Typical Question on a Mobile Browser (Landscape) (using WPTouch plugin on iPhone 4)
4. A Typical Question on a Mobile Browser (Portrait) (using WPTouch plugin on iPhone 4)
5. List view for easy navigation of longer quizzes

== Changelog ==

= 2.1.0 =
* Improved UI. Questions now scroll instead of just flashing from question to question (thanks to jQuery Tools Scrollable scripts)
* Added minified CSS and javascript files
* Changed namespace for CSS to a shorter name. If you made customized CSS changes, you will need to update your CSS after install.
* Added new shortcode options to eliminate certain parts of the quiz that are displayed. See the shortcode reference page on the plugin website for full details.
* Added new shortcode options to control question selection.
* Eliminated the shortcode option to display more than 1 question at a time. The choice is now 1 question or all questions. This is due to new scrolling option limitations. If enough people cry about this maybe I'll find a way to make this work too, but I doubt it.
* Cleaned up some of the HTML. More changes in the future.
* Added Estonian Translation. (Thanks to Martin Orn)

= 2.0.4 =
* Single character changed. What a difference a byte makes?

= 2.0.3 =
* Fixed bug with multiple quiz implementation. Please update even if only using a single quiz at a time.

= 2.0.2 =
* Another bug with multiple quizzes

= 2.0.1 =
* Fixed bug with missing CSS and java

= 2.0.0 =
* Same as 1.1. Changed due to typo in version number preventing upgrades.

= 1.1 =
* Several new UI features added.
* Now includes a handy list view for easy navigation of longer quizzes. This is "on" by default. To turn it off, include "list=off" in the shortcode arguments.
* New proofreading mode that displays all questions and all answers for easy reading. Simply include in the shortcode "proofread=on"
* Display more than one question at a time. For example: use display=4 in the shortcode to show 4 questions at once.
* Now you can include as many quizzes on a page as you'd like, each with different options if you desire. One note is that the new proofreading option is global. Turn it on for one, and it'll likely result in them all being in proofreading mode.
* Updated graphics. Originals are now included inside the images directory in the original subdirectory as well.
* CSS has been better namespaced and tidyed up a bit. It's still a bit of a mess in places.
* French translation provided kindly by Jean-Michel Meyer. Many thanks!
* Bug fixes
* New bugs added


= 1.06 =
* Bugfix issue where questions would be stamped correct/wrong even though answers were supposed to be hidden until the end.
* Updated Czech language files (again thanks to Tomas Hubka!)

= 1.05 =
* Bugfixes
* Enhanced international support by removing all words from images. Now translating the pot file creates a complete translation.
* Added a Portuguese for Brazil translation by Daniel Oliveira.

= 1.04 =
* Fixed a typo causing a fatal error when calling the isnumeric function. Should have been is_numeric. My apologies.
* Includes a complete text Czech Translation by Tomas Hubka

= 1.03 =
* Skipped this number

= 1.02 =
* Fixed the international support. Thanks to Tomas Hubka for the assistance. He has also kindly sent a partial Czech translation.
* Fixed an IE formatting issue.

= 1.01 =
* Hopefully added international support for translators.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.0.4 =
* Single character changed. What a difference a byte makes?

= 2.0.3 =
* Fixed bug with multiple quiz implementation. Please update even if only using a single quiz at a time.

= 2.0.2 =
* Another bug with multiple quizzes

= 2.0.1 =
* Fixed bug with missing CSS and java. Please update to this version.

= 2.0.0 =
* Same as 1.1. Changed due to typo in version number preventing upgrades.

= 1.1 =
* Several new UI features added.
* Now includes a handy list view for easy navigation of longer quizzes. This is "on" by default. To turn it off, include "list=off" in the shortcode arguments.
* New proofreading mode that displays all questions and all answers for easy reading. Simply include in the shortcode "proofread=on"
* Display more than one question at a time. For example: use display=4 in the shortcode to show 4 questions at once.
* Now you can include as many quizzes on a page as you'd like, each with different options if you desire. One note is that the new proofreading option is global. Turn it on for one, and it'll likely result in them all being in proofreading mode.
* Updated graphics. Originals are now included inside the images directory in the original subdirectory as well.
* CSS has been better namespaced and tidyed up a bit. It's still a bit of a mess in places.
* French translation provided kindly by Jean-Michel Meyer. Many thanks!
* Bug fixes
* New bugs added

= 1.06 =
* Bugfix issue where questions would be stamped correct/wrong even though answers were supposed to be hidden until the end.
* Updated Czech language files (again thanks to Tomas Hubka!)

= 1.05 =
* Bugfixes
* Enhanced international support by removing all words from images. Now translating the pot file creates a complete translation.
* Added a Portuguese for Brazil translation by Daniel Oliveira.

= 1.04 =
* Fixed a typo causing a fatal error when calling the isnumeric function. Should have been is_numeric. My apologies.
* Includes a complete text Czech Translation by Tomas Hubka

= 1.03 =
* Skipped this number

= 1.02 =
* Fixed the international support. Thanks to Tomas Hubka for the assistance. He has also kindly sent a partial Czech translation.
* Fixed an IE formatting issue.

= 1.01 =
* Hopefully added international support for translators.

= 1.0 =
* Initial release
