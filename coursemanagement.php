<?php
/*
Plugin Name: courseManagement
Plugin URI: http://jotaprojects.se/2010/08/23/wordpress-plugin-coursemanagement-cm/
Description: A plugin to handle course and registrations to the course
Version: 0.5
Author: Jonna T&auml;rneberg
License: GPL2
*/

/*  Copyright 2010  Jonna Tärneberg

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

global $wpdb;
// Database tables name
$tblCourses           = $wpdb->prefix . "cm_courses";
$tblParticipants      = $wpdb->prefix . "cm_participants";
$tblCourseParticipant = $wpdb->prefix . "cm_courseparticipant";
$cmDBVersion          = "0.5";

/*
 *
 */
function cm_add_menu() {
	add_menu_page( __( "courseManagement (CM)" ), __( "CM" ), "manage_options", __FILE__, "cm_settings_page" );
	//add_submenu_page(__FILE__,__("Settings", "coursemanagament"), __("Settings", "coursemanagament"), "manage_options", "coursemanagement/cm_settings.php", "cm_settings_page");
	add_submenu_page( __FILE__, __( "Courses","coursemanagement" ), __( "Courses","coursemanagement" ), "manage_options", "coursemanagement/cm_courses.php", "cm_courses_page" );
	add_submenu_page( __FILE__, __( "New Course","coursemanagement" ), __( "New Course","coursemanagement" ), "manage_options", "coursemanagement/cm_addcourse.php", "cm_addcourse_page" );
	add_submenu_page( __FILE__, __( "Participants","coursemanagement" ), __( "Participants","coursemanagement" ), "manage_options", "coursemanagement/cm_participants.php", "cm_participants_page" );
	add_submenu_page( __FILE__, __( "New Participant","coursemanagement" ), __( "New Participant","coursemanagement" ), "manage_options", "coursemanagement/cm_addparticipant.php", "cm_addparticipant_page" );

	add_submenu_page( "coursemanagement/cm_courses.php", "Edit Course", "Edit Course", "manage_options", "coursemanagement/cm_editcourse.php", "cm_editcourse_page" );
	add_submenu_page( "coursemanagement/cm_courses.php", "Order by", "Order by", "manage_options", "coursemanagement/cm_courses.php", "cm_courses_page" );
	add_submenu_page( "coursemanagement/cm_participants.php", "Edit Participant", "Edit Participant", "manage_options", "coursemanagement/cm_editparticipant.php", "cm_editparticipant_page" );
	add_submenu_page( "coursemanagement/cm_courses.php", "Show Course", "Show Course", "manage_options", "coursemanagement/cm_show.php", "cm_show_page" );
	add_submenu_page( "coursemanagement/cm_participants.php", "Show Participant", "Show Participant", "manage_options", "coursemanagement/cm_show.php", "cm_show_page" );
	add_submenu_page( "coursemanagement/cm_settings.php", __("Settings", "coursemanagament"), __("Settings", "coursemanagament"), "manage_options", "coursemanagement/cm_settings.php", "cm_settings_page" );
}

/*
 * Show the settings page
 */
function cm_settings_page() {
	include_once( "cm_settings.php" );
}
/*
 * Show the courses main page
 */
function cm_courses_page() {
	include_once( "cm_courses.php" );
}
/*
 * Show the add new course page
 */
function cm_addcourse_page() {
	include_once( "cm_addcourse.php" );
}
/*
 * Show the edit course page
 */
function cm_editcourse_page() {
	include_once( "cm_editcourse.php" );
}
/*
 * Show the page that handles to view a single course or participant
 */
function cm_show_page() {
	include_once( "cm_show.php" );
}
/*
 * Show the main page of participants
 */
function cm_participants_page() {
	include_once( "cm_participants.php" );
}
/*
 * Show the add new participant page
 */
function cm_addparticipant_page() {
	include_once( "cm_addparticipant.php" );
}
/*
 * Show the edit page for participant
 */
function cm_editparticipant_page() {
	include_once( "cm_editparticipant.php" );
}

/* Print a message
 * @param $message message to print (string)
 * @param $class which class to add to the message
 */
function cm_get_message( $message, $class ) {
	echo '<div id="message" class="'. $class .'"><p><strong>'. $message .'!</strong></p></div>';
}

/* Get the latest version of CM
 * @return string the lastest version
 */
function cm_get_version() {
	global $cmDBVersion;
	return $cmDBVersion;
}

/* Add course to the database
 * @param $name the name of the course
 * @param $desc the description of the course
 * @param $day the day that the course is on
 * @param $startDate the date the course starts
 * @param $endDate the date the course ends
 * @param $startTime The time the course starts
 * @param $endTime The time the course ends
 * @param $price the price of the course
 * @param places number of places in course
 * @param $visible show the course or not
 * @return true if course was added, otherwise array
 */
function cm_add_course( $name, $desc, $day, $startDate, $endDate, $startTime, $endTime, $price, $places, $visible ) {
	global $wpdb;
	global $tblCourses;
	$result = false;

	$valid = cm_validate_course( $name, $day, $startDate, $endDate, $startTime, $endTime, $price, $places );

	if( count( $valid ) > 0 ) {
		$result = $valid;
	} else {
		// Format the varibles to match the table in database
		$startDate = strftime( "%Y-%m-%d", strtotime( $startDate ) ); // have type DATE in database
		$endDate   = strftime( "%Y-%m-%d", strtotime( $endDate ) ); // have type DATE in database
		$startTime = strftime( "%H:%M", strtotime( $startTime ) ); // have type TIME in database
		$endTime   = strftime( "%H:%M", strtotime( $endTime ) ); // have type TIME in database
		$price 	   = ( int )$price; // have type INT in database
		$places    = ( int )$places; // have type INT in database

		// Insert the new course
		$rows_affected = $wpdb->insert( $tblCourses, array(
			'courseName' => $name,
			'courseDesc' => $desc,
			'courseDay' => $day,
			'courseStartDate' => $startDate,
			'courseEndDate' => $endDate,
			'courseStartTime' => $startTime,
			'courseEndTime' => $endTime,
			'coursePrice' => $price,
			'coursePlaces' => $places,
			'courseFreePlaces' => $places,
			'courseVisible' => $visible ) );

		// Output message if the course was created or not.
		if( $rows_affected > 0 )
			$result = true;
	}
	return $result;
}

/* Update the selected course
 * @param $id (the id of selected course)
 * @param $name (the name of the course)
 * @param $desc (the description of the course)
 * @param $day (the day that the course is on)
 * @param $startDate (the date the course starts)
 * @param $endDate (the date the course ends)
 * @param $startTime (the time the course starts)
 * @param $endTime (the time the course ends)
 * @param $price (the price of the course)
 * @param places (number of places in course)
 * @param $visible (show the course or not)
 * @return true if the course was updated, otherwise array
 */
function cm_update_course( $id, $name, $desc, $day, $startDate, $endDate, $startTime, $endTime, $price, $places, $freeplaces, $visible ) {
	global $wpdb;
	global $tblCourses;
	$result = false;

	$valid = cm_validate_course( $name, $day, $startDate, $endDate, $startTime, $endTime, $price, $places );

	if( count( $valid ) > 0 ) {
		$result = $valid;
	} else {
		// Format the varibles to match the table in database
		$startDate = strftime( "%Y-%m-%d", strtotime( $startDate ) ); // have type DATE in database
		$endDate   = strftime( "%Y-%m-%d", strtotime( $endDate ) ); // have type DATE in database
		$startTime  = strftime( "%H:%M", strtotime( $startTime ) ); // have type TIME in database
		$endTime    = strftime( "%H:%M", strtotime( $endTime ) ); // have type TIME in database
		$price      = ( int )$price; // have type INT in database
		$places     = ( int )$places; // have type INT in database
		$freeplaces = ( int )$freeplaces;

		// Update the selected course
		$rows_affected = $wpdb->update( $tblCourses, array(
			'courseName' => $name,
			'courseDesc' => $desc,
			'courseDay' => $day,
		 	'courseStartDate' => $startDate,
			'courseEndDate' => $endDate,
			'courseStartTime' => $startTime,
			'courseEndTime' => $endTime,
			'coursePrice' => $price,
			'coursePlaces' => $places,
			'courseFreePlaces' => $freeplaces,
			'courseVisible' => $visible ), array( 'courseID' => $id ) );

		// Output message if the course was updated or not.
		if( $rows_affected >= 0 )
			$result = true;
	}

	return $result;
}

/* Delete course from database
 * @param $id (the id of the course to be deleted)
 * @return true if the course was deleted, otherwise false
 */
function cm_delete_course( $id ) {
	global $wpdb;
	global $tblCourses;
	$result = false;

	// Query database
	$cm_query = "DELETE FROM ". $tblCourses ." WHERE courseID = ". $id .";";
	$rows_affected = $wpdb->query( $wpdb->prepare( $cm_query ) );

	// Output message if the course was created or not.
	if( $rows_affected > 0 )
		$result = true;

	return $result;
}

/* Get all the courses with all information
 * @return 0 if no courses were found, FALSE if error when query database, otherwise the array of courses
 * TODO: Make one function for get all courses and get all participant
 */
function cm_get_all_courses( $orderby = "" ) {
	global $wpdb;
	global $tblCourses;

	$query = "SELECT * FROM " . $tblCourses;
	if ( $orderby != "" )
		$query .= " ORDER BY " . $orderby . ";";

	//Query database
	$result = $wpdb->get_results( $wpdb->prepare( $query ) );

	if( is_array( $result ) ) {		// Call succeeded
		if( empty( $result ) ) {	// No rows found
			$return = 0;
		} else {
			$return = $result;
		}
	} else {
		$return = false;
	}
	return $return;
}

/* Get one course based on the id
 * @param $id (the name of the course)
 * @return 0 if no course was found, otherwise the row of data
 */
function cm_get_course( $courseID ) {
	global $wpdb;
	global $tblCourses;

	// Query database
	$cm_query = "SELECT * FROM ". $tblCourses ." WHERE courseID=". $courseID .";";
	$result = $wpdb->get_row( $wpdb->prepare( $cm_query ) );

	if( empty( $result ) ) {
		$return = 0;
	} else {
		$return = $result;
	}
	return $return;
}

/* Get specific columns from the table with courses
 * @param $columns (array of column names)
 * @return 0 if result is empty, otherwise array
 */
function cm_get_courses_columns( $columns ) {
	global $wpdb;
	global $tblCourses;
	$str = implode( ", ", $columns );

	//Query database
	$cm_query = "SELECT ". $str ." FROM ". $tblCourses .";";
	$result = $wpdb->get_results( $wpdb->prepare( $cm_query ) );

	if( empty( $result ) ) {
		$return = 0;
	} else {
		$return = $result;
	}
	return $return;
}

/* Add participant to the database
 * @param $name (the name of the participant)
 * @param $email (the email address of the participant)
 * @return true if participant was added, otherwise false
 */
function cm_add_participant( $name, $email ) {
	global $wpdb;
	global $tblParticipants;

	// Insert participant to database
	$date = date( 'Y-m-d H:i:s' );
	$rows_affected = $wpdb->insert( $tblParticipants, array(
		'participantName' => $name,
		'participantEmail' => $email,
		'participantDate' => $date ) );

	// Output message if the participant is added or not
	if( $rows_affected > 0 )
		return true;
	else
		return false;
}

/* Add participant to a course
 * Checks also if the participant already is registrer to the course or not
 * @param $participantID (the id of the participant to be added to course)
 * @param $courseID (the id of the course that the participant should be added to)
 * @return false if something went wrong, otherwise true
 */
function cm_add_participant_to_course( $participantID, $courseID ) {
	global $wpdb;
	global $tblCourseParticipant;
	$return = false;

	// Check if the participant is registred to the course
	$result = cm_check_courseparticipant( $participantID, $courseID );

	// If result is 0 then the participant is added to the course
	if( $result == 0 ) {
		$rows_affected = $wpdb->insert( $tblCourseParticipant, array(
			'courseparticipant_courseID' => $courseID,
			'courseparticipant_participantID' => $participantID ) );
		if( $rows_affected > 0 ) {
			$return = true;
			$course = cm_get_course( $courseID );
			$freeplaces = $course->courseFreePlaces - 1;
			cm_update_course( $course->courseID, $course->courseName, $course->courseDay, $course->courseStartTime, $course->courseEndTime, $course->coursePrice, $course->coursePlaces, $freeplaces, $course->courseVisible );
		}
	}

	return $return;
}

/* Add course to the database
 * @param $id (the id of the participant to be updated)
 * @param $name (the updated name)
 * @param $email (the updated email)
 * @param $courses (array of the courses tha participant should be registred to)
 * @return 0 if the participant was updated, otherwise array
 */
function cm_update_participant( $id, $name, $email, $date, $courses ) {
	global $wpdb;
	global $tblParticipants;
	$error  = 0;
	$result = -1;

	$valid = cm_validate_participant( $name, $email );
	$nrOfErrors = count( $valid );

	if( $nrOfErrors == 0 ) {
		// Get participants old courses
		$oldCourses = cm_get_participant_courses( $id );

		// Query database
		$rows_affected = $wpdb->update( $tblParticipants, array(
			'participantName' => $name,
			'participantEmail' => $email,
			'participantDate' => $date ), array( 'participantID' => $id ) );

		if( is_array( $oldCourses ) ) {
			// Remove the participant from old courses
			$nrOfOldCourses = count( $oldCourses );
			for( $i = 0; $i < $nrOfOldCourses; $i++ ) {
				if( !cm_delete_participant_from_course( $id, $oldCourses[$i]->courseID ) )
					$error++;
			}
		}

		if( is_array( $courses ) ) {
			// Add the participant to the new courses
			$nrOfCourses = count( $courses );
			for( $i = 0; $i < $nrOfCourses; $i++ ) {
				$add = cm_add_participant_to_course( $id, $courses[$i] );
				if( !$add )
					$error++;
			}
		}
	} else {
		$error++;
	}

	if( $error == 0 && $rows_affected >= 0 )
		$result = 0;
	else
		$result = $valid;

	return $result;
}

/* Delete a participant
 * @param $participantID (the id of the participant to be deleted)
 * @return true if the participant was deleted, otherwise false
 */
function cm_delete_participant( $participantID ) {
	global $wpdb;
	global $tblParticipants;
	$result = false;

	// Query database
	$cm_query = "DELETE FROM ". $tblParticipants ." WHERE participantID = ". $participantID .";";
	$rows_affected = $wpdb->query( $wpdb->prepare( $cm_query ) );

	if( $rows_affected > 0 || !$rows_affected )
		$result = true;

	return $result;
}

/* Delete participant from a course
 * @param $participantID (the id of the participant)
 * @param $courseID (the id of the course that the participant should be deleted from)
 * @return true if the participant was deleted from course, otherwise false
 */
function cm_delete_participant_from_course( $participantID, $courseID ) {
	global $wpdb;
	global $tblCourseParticipant;
	$result = false;

	// Query database
	$cm_query = "DELETE FROM ". $tblCourseParticipant ." WHERE courseparticipant_participantID = ". $participantID ." AND courseparticipant_courseID = ". $courseID .";";
	$rows_affected = $wpdb->query( $wpdb->prepare( $cm_query ) );

	if( $rows_affected > 0 ) {
		$result = true;
		$course = cm_get_course( $courseID );
		if( $course->courseFreePlaces < $course->coursePlaces ) {
			$freeplaces = $course->courseFreePlaces + 1;
			cm_update_course( $course->courseID, $course->courseName, $course->courseDay, $course->courseStartTime, $course->courseEndTime, $course->coursePrice, $course->coursePlaces, $freeplaces, $course->courseVisible );
		}
	}

	return $result;
}

/* Get all participants and their data
 * @return 0 if no participants are found, FALSE if error when query database,
 * otherwise array of participants
 * TODO: Make one function for get all courses and get all participant
 */
function cm_get_all_participants( $orderby = "") {
	global $wpdb;
	global $tblParticipants;

	// Query database
	$cm_query = "SELECT * FROM ". $tblParticipants;
	if ( $orderby != "" )
		$cm_query .= " ORDER BY " . $orderby;
	$result = $wpdb->get_results( $wpdb->prepare( $cm_query ) );

	if( is_array( $result ) ) {		// Call succeeded
		if( empty( $result ) ) {	// No rows found
			$return = 0;
		} else {
			$return = $result;
		}
	} else {
		$return = false;
	}
	return $return;
}

/* Get one participant
 * @param $participantID (the id of the participant)
 * @return 0 if no participant is found, otherwise the row of data
 */
function cm_get_participant( $id ) {
	global $wpdb;
	global $tblParticipants;

	// Query database
	$cm_query = "SELECT * FROM ". $tblParticipants ." WHERE participantID=". $id .";";
	$result = $wpdb->get_row( $wpdb->prepare( $cm_query ) );

	if( empty( $result ) ) {
		$return = 0;
	} else {
		$return = $result;
	}
	return $return;
}

/* Get participants of a specific course
 * @param $courseID (the id of the course)
 * @return 0 if no participants are found, FALSE if error when query database,
 * otherwise the array of participants
 */
function cm_get_participants_of_course( $courseID ) {
	global $wpdb;
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;

	// Query database
	$cm_query = "SELECT participantID, participantName, participantEmail, participantDate
		FROM ". $tblParticipants ." as P, ". $tblCourses ." as C, ". $tblCourseParticipant ." as CP
		WHERE C.courseID = ". $courseID ." AND ". $courseID ." = CP.courseparticipant_courseID AND
		P.participantID = CP.courseparticipant_participantID;";

	$result = $wpdb->get_results( $wpdb->prepare( $cm_query ) );

	if( is_array( $result ) ) {		// Call succeeded
		if( empty( $result ) ) {	// No rows found
			$return = 0;
		} else {
			$return = $result;
		}
	} else {
		$return = false;
	}
	return $return;
}

/* Get the courses that a specific participant is registred to
 * @param $id (the id of the participant)
 * @return 0 if no courses are found, FALSE if error when query database,
 * otherwise the array of courses
 */
function cm_get_participant_courses( $id ) {
	global $wpdb;
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;

	// Query database
	$cm_query = "SELECT courseID, courseName, courseDay
		FROM ". $tblParticipants ." as P, ". $tblCourses ." as C, ". $tblCourseParticipant ." as CP
		WHERE P.participantID = ". $id ." AND C.courseID = CP.courseparticipant_courseID AND ".
		$id ." = CP.courseparticipant_participantID;";
	$result = $wpdb->get_results( $wpdb->prepare( $cm_query ) );

	if( is_array( $result ) ) {		// Call succeeded
		if( empty( $result ) ) {	// No rows found
			$return = 0;
		} else {
			$return = $result;
		}
	} else {
		$return = false;
	}
	return $return;
}

/* Check if a participant exists in the database
 * @param $participantName (the name of the participant)
 * @param $participantEmail (the email of the participant)
 * @return 0 if no participant is found, otherwise the row of data
 */
function cm_check_participant( $participantName, $participantEmail ) {
	global $wpdb;
	global $tblParticipants;

	// Query database
	$cm_query = "SELECT participantID FROM ". $tblParticipants ." WHERE participantName='". $participantName ."' AND participantEmail= '". $participantEmail ."';";
	$result = $wpdb->get_row( $wpdb->prepare( $cm_query ) );

	if( empty( $result ) ) {
		$return = 0;
	} else {
		$return = $result;
	}
	return $return;
}

/* Check if a participant is registred to a course
 * @param $participantID (the id of the participant)
 * @param $courseID (the id of the course)
 * @return 0 if no participant is found, otherwise the row of data
 */
function cm_check_courseparticipant( $participantID, $courseID ) {
	global $wpdb;
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;

	// Query database
	$cm_query = "SELECT courseID, courseName, courseDay
		FROM ". $tblCourses ." AS C, ". $tblCourseParticipant ." AS CP, ". $tblParticipants ." AS P
		WHERE P.participantID = ". $participantID ." AND C.courseID = ". $courseID ." AND
		CP.courseparticipant_courseID = C.courseID AND CP.courseparticipant_participantID = P.participantID;";
	$result = $wpdb->get_row( $wpdb->prepare( $cm_query ) );

	if( empty( $result ) ) {
		$return = 0;
	} else {
		$return = $result;
	}
	return $return;
}

/* Check if shorttag exists in the content
 * @param $content (the content to be search)
 * @return if not found the content is returned
 * otherwise the registrationform is displayed and returned with the content
 */
function cm_display_registration_form( $content ) {
	if( !preg_match( '<!--CMFORM-->', $content ) ) {
		return $content;
	}
	cm_create_registration_form();
	return __( '<!--CMFORM-->', $content );
}

/*
 * Create the registration form and process if it is submitted.
 */
function cm_create_registration_form() {
	// Get specific data from the table of course in database
	$courses = cm_get_all_courses();
	$nrOfCourses = count( $courses );

	if ( class_exists( 'ReallySimpleCaptcha' ) ) {
		$captcha 	 = new ReallySimpleCaptcha();
		$word 		 = $captcha->generate_random_word();
		$prefix 	 = mt_rand();
		$captcha_url = WP_PLUGIN_URL . "/really-simple-captcha/tmp/" . $captcha->generate_image( $prefix, $word );
	}

	// If the form is submitted
	if ( isset( $_POST['regParticipant'] ) ) {
		global $wpdb;
		$errors         = 0;
		$message        = "";
		$registered	    = array();
		$notRegistered  = array();
		$send_mail      = get_option('cm_send_mail');
		$adm_email      = get_option('cm_adm_email');

		// Get the POST variables
		$participant_name    = $_POST['participantName'];
		$participant_email   = $_POST['participantEmail'];
		$participant_courses = $_POST['participantCourses'];
		$nrOfCourses         = count( $participant_courses );

		// Check validation
		$valid = cm_validate_regform( $participant_name, $participant_email, $participant_courses );
		$nrOfErrors = count( $valid );
		if ( $nrOfErrors > 0 )
			$error++;

		if ( class_exists( 'ReallySimpleCaptcha' ) ) {
			// Captcha POST variables
			$captcha_input 	= $_POST['captcha_input'];
			$captcha_prefix = $_POST['captcha_prefix'];
			// Check captcha
			$correct 		= $captcha->check( $captcha_prefix, $captcha_input );
			if( !$correct )
				$error++;
			// Remove captcha
			$captcha->remove( $captcha_prefix );
		}

		if ( $error == 0 ) {
			// Check if the participant exists already
			$return = cm_check_participant( $participant_name, $participant_email );

			// if a participant is found save the id for later
			// otherwise add the participant to database and save the new id for later
			if ( $return != 0 ) {
				$participantID = $return->participantID;
			} else {
				if( cm_add_participant( $participant_name, $participant_email ) ) {
					cm_get_message( __( "You've been added", "coursemanagement" ), "updated" );
					$participantID = $wpdb->insert_id;
				} else {
					cm_get_message( __( "Couldn't add you", "coursemanagement" ), "error" );
				}
			}

			// Loop through the selected courses
			// and add the participant
			for ( $i = 0; $i < $nrOfCourses; $i++ ) {
				$add = cm_add_participant_to_course( $participantID, $participant_courses[$i] );
				$course = cm_get_course( $participant_courses[$i] );
				if( !$add ) {
					$registered[] = $course->courseName .", ". $course->courseDay;
				} else {
					$notRegistered[] = $course->courseName .", ". $course->courseDay;
					cm_update_course( $course->courseID, $course->courseName, $course->courseDay, $course->courseStartTime, $course->courseEndTime, $course->coursePrice, $course->coursePlaces, $course->courseFreePlaces-1, $course->courseVisible );
				}
			}

			// Output the courses that the participant already is registered to.
			if ( count( $registered ) > 0 ) {
				$message = "<br />". __( "You're already registered to", "coursemanagement" );
				foreach( $registered as $reg ) {
					$message .= "<br />" . $reg;
				}
			}
			// Output the courses that the participant have been registered to.
			if ( count( $notRegistered ) > 0 ) {
				$message .= "<br />". __( "Registered to", "coursemanagement" );
				foreach ( $notRegistered as $reg ) {
					$message .= "<br />" . $reg;
				}
			}
			//Output the message.
			cm_get_message( $message, "updated" );

			// Send mail to admin
			if ( $send_mail == 1 ) {
				$headers = 'From: '. $participant_name .' <'. $participant_email .'>'. "\r\n\\";
				$msg = printf( __( "A new participant (%s, %s) has been registrered to %s", "coursemanagement" ), $participant_name, $participant_email, implode( "\n", $notRegistered ) );
				wp_mail( $adm_email, __( "New CM Registration", "coursemanagement" ), $msg, $headers );
				// if( !wp_mail( $adm_email, __( "New CM Registration", "coursemanagement" ), $msg, $headers ) )
					// cm_get_message( __( "Email couldn't be sent", "coursemanagement" ), "error" );
				// else
					// cm_get_message( __( "Email was sent", "coursemanagement" ), "updated" );
			}

		} else {
			// Output a message if something went wrong
			$message = __( "Something went wrong", "coursemanagement" ) . "<br />";
			if ( $nrOfErrors > 0 )
				$message .= implode( "<br />", $valid );

			cm_get_message( $message, "error" );
		}
	}
	if( $nrOfCourses != 0 ) {
		$nofree = 0;
		foreach( $courses as $row ) {
			if( $row->courseFreePlaces == 0 )
				$nofree++;
		}
		if( $nofree == 0 || $nofree != $nrOfCourses ) {
?>
		<form action="<?php echo $PHP_SELF ?>" method="post" name="participantRegForm" id="participantRegForm">
			<input type="hidden" name="captcha_prefix" value="<?php echo $prefix;?>" />
			<table cellpadding="3" cellspacing="0" class="cm_tbl_regform">
				<tr>
					<td><?php _e( "Name", "coursemanagement" ); ?></td>
					<td><input name="participantName" type="text" id="participantName" size="45" maxlength="45" /></td>
				</tr>
				<tr>
					<td><?php _e( "Email", "coursemanagement" ); ?></td>
					<td><input name="participantEmail" type="text" id="participantEmail" size="45" /></td>
				</tr>
				<tr>
					<td><?php _e( "Courses", "coursemanagement" ); ?></td>
					<td>
					<?php
					foreach( $courses as $row ) {
						if( $row->courseVisible == 1 )
							if( $row->courseFreePlaces != 0 )
								echo '<br /><input name="participantCourses[]" type="checkbox" id="participantCourses[]" value="'. $row->courseID .'" />'. $row->courseName .', '. $row->courseDay;
					}
					?>
					</td>
				</tr>
				<?php if( class_exists( 'ReallySimpleCaptcha' ) ) { ?>
				<tr>
					<td><?php _e( "Control", "coursemanagement" ); ?></td>
					<td><img alt="captcha" src="<?php echo $captcha_url; ?>" width="60" height="20" /><br />
					<?php _e( "Write the code you see above", "coursemanagement" ); ?><br />
					<input type="text" name="captcha_input" id="captcha_input" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2"><input name="regParticipant" type="submit" value="<?php _e( "Send", "coursemanagement" ); ?>" /></td>
				</tr>
			</table>
		</form>
<?php
		} else {
			cm_get_message( __( "All courses are full", "coursemanagement" ), "error" );
		}
	} else {
		cm_get_message( __( "No courses exists", "coursemanagement" ), "error" );
	}
}

/* Check if shorttag exists in the content
 * @param $content (the content to be search)
 * @return if not found the content is returned
 * otherwise the overview of courses is displayed and returned with the content
 */
function cm_display_course_overview( $content ) {
	if( !preg_match( '<!--CMOVERVIEW-->', $content ) ) {
		return $content;
	}
	cm_create_course_overview();
	return __( '<!--CMOVERVIEW-->', $content );
}

/*
 * Create the overview of courses
 */
function cm_create_course_overview() {
?>
<table cellpadding="3" cellspacing="0" class="cm_tbl_courseoverview">
	<thead>
	<tr>
      	<th><?php _e( "Course", "coursemanagement" ); ?></th>
      	<th><?php _e( "Description", "coursemanagement" ); ?></th>
      	<th><?php _e( "Date", "coursemanagement" ); ?></th>
    	<th><?php _e( "Day", "coursemanagement" ); ?></th>
        <th><?php _e( "Time","coursemanagement" ); ?></th>
        <th><?php _e( "Price","coursemanagement" ); ?></th>
        <th><?php _e( "Places","coursemanagement" ); ?></th>
    </tr>
    </thead>
    <tbody>
<?php
$courses = cm_get_all_courses();
if( $courses == 0 ) {
	echo '<tr><td colspan="7"><strong>'. __( "Sorry, no entries.","coursemanagement" ) .'</strong></td></tr>';
} else {
	foreach( $courses as $row ) {
		if( $row->courseVisible == 1 ) {
			if( $row->coursePlaces == 0 )
				$places = str_replace( '0', '-', $row->coursePlaces );
			else
				$places = $row->coursePlaces;
			echo '<tr>';
			echo '<td>'. $row->courseName .'</td>';
			echo '<td>'. $row->courseDesc ."</td>";
			echo '<td>'. $row->courseStartDate .' - '. $row->courseEndDate .'</td>';
			echo '<td>'. $row->courseDay ."</td>";
			echo '<td>'. substr( $row->courseStartTime, 0, 5 ) .' - '. substr( $row->courseEndTime, 0, 5 ) .'</td>';
			echo '<td>'. $row->coursePrice .':-</td>';
			echo '<td>'. $places .'</td>';
			echo '</tr>';
		}
	}
}
?>
</tbody>
</table>
<?php
}

/* Validate the registration form
 * @param $name
 * @param $email
 * @param $courses
 * @return $errorMessage (array)
 */
function cm_validate_regform( $name, $email, $courses ) {
	$errorMessage = array();

	// Check participant
	$valid = cm_validate_participant( $name, $email );
	$errorMessage = array_merge( $errorMessage, $valid );

	// Check if any courses is selected
	if( count( $courses ) == 0 )
		$errorMessage[] = __( "Please select at least one of the courses", "coursemanagement" );

	return $errorMessage;
}

/* Validate add/edit course
 * @param $name
 * @param $day
 * @param $startDate
 * @param $endDate
 * @param $startTime
 * @param $endTime
 * @param $price
 * @param $places
 * @return $errorMessage (array)
 */
function cm_validate_course( $name, $day, $startDate, $endDate, $startTime, $endTime, $price, $places ) {
	$errorMessages = array();
	$chars = "abcdefghijklmnopqrstuvxyzÃ¥Ã¤Ã¶ABCDEFGHIJKLMNOPQRSTUVXYZÃ…Ã„Ã–!#Â¤%&/()=?Â£$.,@";

	if( empty( $name ) )
		$errorMessage[] = __( "Please fill in a name of the course", "coursemanagement" );
	if( empty( $day ) )
		$errorMessage[] = __( "Please fill in a day or days", "coursemanagement" );
	if( empty( $startTime ) )
		$errorMessage[] = __( "Please fill in a startTime", "coursemanagement" );
	if( empty( $endTime ) )
		$errorMessage[] = __( "Please fill in an endTime", "coursemanagement" );
	if( empty( $price ) )
		$errorMessage[] = __( "Please fill in a price", "coursemanagement" );

	if( !is_numeric( $price ) )
		$errorMessage[] = __( "The value for price is not valid", "coursemanagement" );
	if( !empty( $places ) )
		if( !is_numeric( $places ) )
			$errorMessage[] = __( "The value for places is not valid", "coursemanagement" );
	if( preg_match( "/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/", $startTime ) == 0 )
		$errorMessage[] = __("The value for startTime is not valid", "coursemanagement");
	if( preg_match( "/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/", $endTime ) == 0 )
		$errorMessage[] = __( "The value for endTime is not valid", "coursemanagement" );
	if( !empty($startDate) )
		if( preg_match("/^((((19|20)(([02468][048])|([13579][26]))-02-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0[1-9])|(1[0-2]))-((0[1-9])|(1\d)|(2[0-8])))|((((0[13578])|(1[02]))-31)|(((0[1,3-9])|(1[0-2]))-(29|30)))))$/", $startDate) == 0 )
			$errorMessage[] = __( "The value for startDate is not valid", "coursemanagement" );
	if( !empty($endDate) )
		if( preg_match("/^((((19|20)(([02468][048])|([13579][26]))-02-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0[1-9])|(1[0-2]))-((0[1-9])|(1\d)|(2[0-8])))|((((0[13578])|(1[02]))-31)|(((0[1,3-9])|(1[0-2]))-(29|30)))))$/", $endDate) == 0 )
			$errorMessage[] = __( "The value for endDate is not valid", "coursemanagement" );

	return $errorMessage;
}

/* Validate participant
 * @param $name
 * @param $email
 * @return $errorMessage (array)
 */
function cm_validate_participant( $name, $email ) {
	$errorMessage = array();
	// Check if the name is empty
	if( $name == "" )
		$errorMessage[] = __( "Please fill in a name", "coursemanagement" );

	// Check if the email is empty
	if( $email == "" ) {
		$errorMessage[] = __( "Please fill in an email", "coursemanagement" );
	} else {
		// Check valid email
		if( !cm_check_email( $email ) ) {
			$errorMessage[] = __( "Please fill in a valid email", "coursemanagement" );
		}
	}

	return $errorMessage;
}

/* Taken from http://myphpsource.blogspot.com/2010/01/php-validate-email-address-email.html
 * @param $email
 * @return true if valid, otherwise false
 */
function cm_check_email( $email ) {
	//check for all the non-printable codes in the standard ASCII set,
	//including null bytes and newlines, and exit immediately if any are found.
	if ( preg_match( "/[\\000-\\037]/", $email ) ) {
		return false;
	}
	$pattern = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";
	if ( !preg_match( $pattern, $email ) ){
		return false;
	}
	// Validate the domain exists with a DNS check
	// if the checks cannot be made (soft fail over to true)
	list( $user, $domain ) = explode( '@', $email );
	if ( function_exists( 'checkdnsrr' ) ) {
		if( !checkdnsrr( $domain,"MX" ) ) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
			return false;
		}
	} else if ( function_exists( "getmxrr" ) ) {
		if ( !getmxrr( $domain, $mxhosts ) ) {
			return false;
		}
	}
   return true;
}

/* Helper function for install-link
 * @return the number of tables found
 */
function cm_check_tables() {
	global $wpdb;
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;
	$nrOfTbls = 0;

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblCourses'" ) == $tblCourses )
		$nrOfTbls++;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblParticipants'" ) == $tblParticipants )
		$nrOfTbls++;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblCourseParticipant'" ) == $tblCourseParticipant )
		$nrOfTbls++;

	return $nrOfTbls;
}

function cm_check_update() {
	global $wpdb;
	
	$settings = get_option( "coursemanagement_settings" );
	$installed_ver = $settings['db_version'];
	
	if ($installed_ver != CM_VERSION) {
		cm_install();
	}
}

/*
 * Install the courseManagement and create the tables in database
 */
function cm_install() {
	global $wpdb;
	$cmDBVersion = "0.5";

	// The tables to create
	$tblCourses           = $wpdb->prefix . 'cm_courses';
	$tblParticipants      = $wpdb->prefix . 'cm_participants';
	$tblCourseParticipant = $wpdb->prefix . 'cm_courseparticipant';

	// version 0.3
	// Add participantDate to the table participant
	$sql = "SHOW COLUMNS FROM " . $tblParticipants . " LIKE 'c'";
	$test = $wpdb->query( $wpdb->prepare( $sql ) );
	if ( $test == '0' ) {
		$sql = "ALTER TABLE ". $tblParticipants ." ADD COLUMN 'participantDate' DATETIME NOT NULL AFTER 'participantEmail'";
		$wpdb->query( $wpdb->prepare( $sql ) );
	}

	// Remove courseparticipantDate from the table coursparticipant
	$sql = "SHOW COLUMNS FROM ". $tblCourseParticipant ." LIKE 'courseparticipantDate'";
	$test = $wpdb->query( $wpdb->prepare( $sql ) );
	if ( $test == '1' ) {
		$wpdb->query( "ALTER TABLE ". $tblCourseParticipant ." DROP 'courseParticipantDate'" );
	}

	// version 0.5
	// Add Date to the table courses
	$sql = "SHOW COLUMNS FROM " . $tblCourses . " LIKE 'course%Date%'";
	$test = $wpdb->query( $wpdb->prepare( $sql ) );
	if ( $test == '0' ) {
		$sql = "ALTER TABLE ". $tblCourses ."
				ADD COLUMN 'courseEndDate' DATE NOT NULL AFTER 'courseDay',
				ADD COLUMN 'courseStartDate' DATE NOT NULL AFTER 'courseDay'";
		$wpdb->query( $wpdb->prepare( $sql ) );
	}
	// Add Description to the table courses
	$sql = "SHOW COLUMNS FROM " . $tblCourses . " LIKE 'courseDesc'";
	$test = $wpdb->query( $wpdb->prepare( $sql ) );
	if ( $test == '0' ) {
		$sql = "ALTER TABLE ". $tblCourses ."
				ADD COLUMN 'courseDesc' VARCHAR(255) AFTER 'courseName'";
		$wpdb->query( $wpdb->prepare( $sql ) );
	}

	// charset & collate like WordPress
	$charset_collate = '';
	if ( $wpdb->supports_collation() ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = " DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	// Create the table that handles courses
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblCourses'" ) != $tblCourses ) {
		$sql = "CREATE TABLE " . $tblCourses . " (
					`courseID` INT NOT NULL AUTO_INCREMENT,
					`courseName` VARCHAR(255) NOT NULL,
					`courseDesc` VARCHAR(255),
					`courseDay` VARCHAR(45) NOT NULL,
					`courseStartDate` DATE,
					`courseEndDate` DATE,
					`courseStartTime` TIME NOT NULL,
					`courseEndTime` TIME NOT NULL,
					`coursePrice` INT NOT NULL,
					`coursePlaces` INT,
					`courseFreePlaces` INT,
					`courseVisible` INT NOT NULL,
					PRIMARY KEY  (courseID)
				)".$charset_collate.";";

		require_once(ABSPATH . 'wp-admin/upgrade.php');
		dbDelta( $sql );
	}

	// Create the table that handles participants
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblParticipants'" ) != $tblParticipants ) {
		$sql = "CREATE TABLE " . $tblParticipants . " (
					`participantID` INT NOT NULL AUTO_INCREMENT,
					`participantName` VARCHAR(45) NOT NULL,
					`participantEmail` VARCHAR(255) NOT NULL,
					`participantDate` DATETIME NOT NULL,
					PRIMARY KEY  (participantID)
				)".$charset_collate.";";

		require_once(ABSPATH . 'wp-admin/upgrade.php');
		dbDelta( $sql );
	}

	// Create the table that handles relationsships between course and participant
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$tblCourseParticipant'" ) != $tblCourseParticipant ) {
		$sql = "CREATE TABLE " . $tblCourseParticipant . " (
					`courseparticipant_courseID` INT NOT NULL,
					`courseparticipant_participantID` INT NOT NULL,
					FOREIGN KEY (courseparticipant_courseID) REFERENCES " . $tblCourses . "(courseID),
					FOREIGN KEY (courseparticipant_participantID) REFERENCES " . $tblParticipants . "(participantID),
					PRIMARY KEY  (courseparticipant_courseID, courseparticipant_participantID)
				)".$charset_collate.";";

		require_once(ABSPATH . 'wp-admin/upgrade.php');
		dbDelta( $sql );
	}

	// Add settings array as option
	if( !get_option( "coursemanagement_settings" ) ) {
		// Create the new settings in an array and add it to database
		$settings = array(
			"db_version" => "0.5",
			"send_mail" => "0",
			"adm_mail" => get_option( "admin_email" ),
		);
		add_option( "coursemanagement_settings", $settings );
		// Delete the other options
		delete_option( "cm_db_version" );
		delete_option( "cm_send_mail" );
		delete_option( "cm_adm_email" );
	} else {
		$settings['db_version'] = "0.5";
		update_option( "coursemanagement_settings", $settings );
	}

	return true;
}

/*
 * Upgrade the courseManagement if a newer exists
 */
function cm_upgrade() {
	global $wpdb;
	global $cmDBVersion;
	// Database tables
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;
	$installed_ver = get_option( "cm_db_version" );
	if( !$installed_ver ) {
		$settings = get_option( "coursemanagement_settings" );
		$installed_ver = $settings['db_version'];
	}

	if( $installed_ver != "" ) {
		if ( $installed_ver != $cmDBVersion ) {

			// version 0.3
			// Add participantDate to the table participant
			$sql = "SHOW COLUMNS FROM " . $tblParticipants . " LIKE 'c'";
			$test = $wpdb->query( $wpdb->prepare( $sql ) );
			if ( $test == '0' ) {
				$sql = "ALTER TABLE ". $tblParticipants ." ADD COLUMN 'participantDate' DATETIME NOT NULL AFTER 'participantEmail'";
				$wpdb->query( $wpdb->prepare( $sql ) );
			}

			// Remove courseparticipantDate from the table coursparticipant
			$sql = "SHOW COLUMNS FROM ". $tblCourseParticipant ." LIKE 'courseparticipantDate'";
			$test = $wpdb->query( $wpdb->prepare( $sql ) );
			if ( $test == '1' ) {
				$wpdb->query( "ALTER TABLE ". $tblCourseParticipant ." DROP 'courseParticipantDate'" );
			}

			// version 0.4
			// Add settings array as option
			if( !get_option( "coursemanagement_settings" ) ) {
				// Create the new settings in an array and add it to database
				$settings = array(
					"db_version" => "0.5",
					"send_mail" => "0",
					"adm_mail" => get_option( "admin_email" ),
				);
				add_option( "coursemanagement_settings", $settings );
				// Delete the other options
				delete_option( "cm_db_version" );
				delete_option( "cm_send_mail" );
				delete_option( "cm_adm_email" );
			}

			// version 0.5
			// Add Date to the table courses
			$sql = "SHOW COLUMNS FROM " . $tblCourses . " LIKE 'course%Date%'";
			$test = $wpdb->query( $wpdb->prepare( $sql ) );
			if ( $test == '0' ) {
				$sql = "ALTER TABLE ". $tblCourses ."
						ADD COLUMN 'courseEndDate' DATE NOT NULL AFTER 'courseDay',
						ADD COLUMN 'courseStartDate' DATE NOT NULL AFTER 'courseDay'";
				$wpdb->query( $wpdb->prepare( $sql ) );
			}
			// Add Description to the table courses
			$sql = "SHOW COLUMNS FROM " . $tblCourses . " LIKE 'courseDesc'";
			$test = $wpdb->query( $wpdb->prepare( $sql ) );
			if ( $test == '0' ) {
				$sql = "ALTER TABLE ". $tblCourses ."
						ADD COLUMN 'courseDesc' VARCHAR(255) AFTER 'courseName'";
				$wpdb->query( $wpdb->prepare( $sql ) );
			}

			$settings['db_version'] = "0.5";
			update_option( "coursemanagement_settings", $settings );

			return true;
		}
	}
}

/*
 * Delete the tables from database
 */
function cm_uninstall() {
	if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

	global $wpdb;

	// The tables to delete
	global $tblCourses;
	global $tblParticipants;
	global $tblCourseParticipant;

	$result = $wpdb->query( "DROP TABLE ". $tblCourses .", ". $tblParticipants .", ". $tblCourseParticipant .";" );

	delete_option( "coursemanagement_settings" );

	if ( $result > 0 && $result != false )
		return true;
	else if ( $result == 0 )
		return true;
	else
		return false;
}

/*
 * load language files
 */
function cm_language_support() {
	$plugin_dir = basename ( dirname( __FILE__ ) ) . "/languages";
	load_plugin_textdomain( 'coursemanagement' , false, $plugin_dir );
}

register_activation_hook( __FILE__, 'cm_install' );

add_action( 'plugins_loaded', 'cm_check_update' );
add_action( 'init', 'cm_language_support' );
add_action( 'admin_menu', 'cm_add_menu' );
add_action( 'the_content', 'cm_display_registration_form' );
add_action( 'the_content', 'cm_display_course_overview' );
?>