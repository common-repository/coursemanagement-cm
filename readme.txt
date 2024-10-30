=== courseManagement (CM) ===
Contributors: Jonna T&auml;rneberg
Tags: course handling, management, registration, course, course management
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 0.5

A simple course management plugin that handles courses and registrations via admin in wordpress. 

== Description ==
Handle courses and participants via admin in a simple way with CM. With CM you can add/edit/remove course and decide if you want to show them in the registrationform. For displaying the course overview on a wordpresspage add the following code <code><!--CMOVERVIEW--></code> and the courses will be displayed in a table. 

Within CM you can also handle the participants of the courses. Every time a registration is made from the registrationform it's saved in the database and it's displayed in the participant overview. You can also decide if the admin should get a email when a registration is made under settings. 
To display the registrationform on a wordpresspage add the code <code><!--CMFORM--></code> in code view of the editor and save the page. 

If you want captcha with the registrationform download the plugin Really Simple Captcha and activate. Then the registrationform gets a new field with the captcha image and inputfield.

If you like to style the registrationform you can use the css-class <code>cm_tbl_regform</code>. The class is set on the table that includes the registrationform.
On the table with course overview the css-class is <code>cm_tbl_courseoverview</code>. 
You can use these classes when you want to style course overview and registrationform on a wppage.

For uninstalling the CM. On the Settings page mark the Yes radiobutton at Uninstall and click the save button. Go to 'Plugin' menu in Wordpress and deactivate CM and then delete the plugin.

This plugin has been tested on:
<ul>
 <li>3.0</li> 
 <li>3.0.1</li>
</ul>

Stable version is 0.5.

== Installation ==
1. Download the plugin
2. Unzip the plugin to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in Wordpress
4. Read the manual that follows for more information how you using the plugin.

== Changelog ==

= 0.1 =
* Added CAPTCHA validation to registration form. Requires the plugin Really Simple Captcha.

= 0.2 =
* Added validation to the registration form and add/edit course and participant.

= 0.3 =
* Moved the registration date from the table cm_courseparticipant to cm_participants.
* Added a settingspage with uninstall, to remove the tables and settings from database.
* Allowed edit the participantDate
* Added option add new participant
* Added so that the admin can get an email when a registration is submitted in the form.

= 0.4 =
* Saved the settings in array instead of multiple options in wp_options table.
* Added Number of Free Places to course. If all courses in the system are full then a error message is displayed where the registrationform is.
* Disabled edit places of a course if a course has participants. 

= 0.5 =
* Added a Sort by function to Courses and Participants.
* Added Course description, startdate and enddate to course
* Change the view of course overview from
Course Name Day Time Price Places
to
Course Description Date Day Time Price Places
* Added classes to the tables registrationform and course overview when they are displayed on a wppage. CSS-classes are 
  * cm_tbl_regform
  * cm_tbl_courseoverview
* Updated the manual