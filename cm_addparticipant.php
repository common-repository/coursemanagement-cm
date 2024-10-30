<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_addparticpant.php
 * Description: File that displays the form and handles add participant
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

if ( isset($_POST['submitted'] ) ) {
	$name 		 = $_POST['name'];
	$email 		 = $_POST['email'];
	$courses 	 = $_POST['participantCourses'];
	$nrOfCourses = count( $courses );
	
	global $wpdb;
	$errors 		= 0;	
	$message 	 	= "";
	$registered		= array();
	
	// Check validation
	$valid = cm_validate_regform( $name, $email, $courses );
	$nrOfErrors = count( $valid );
	if ( $nrOfErrors > 0 )
		$error++;
	
	if ( $error == 0 ) {	
		// Check if the participant exists already
		$return = cm_check_participant( $name, $email );
		
		// if a participant is found save the id for later
		// otherwise add the participant to database and save the new id for later
		if ( $return != 0 ) {
			cm_get_message( __( "Participant already exists", "coursemanagement" ), "error" );
		} else {
			if ( cm_add_participant( $name, $email ) ) {	
				cm_get_message( __( "Participant created", "coursemanagement" ), "updated" );
				$participantID = $wpdb->insert_id;
				
				// Loop through the selected courses
				// and add the participant
				for ( $i = 0; $i < $nrOfCourses; $i++ ) {				
					$add = cm_add_participant_to_course( $participantID, $courses[$i] );
					$course = cm_get_course( $courses[$i] );	
					if ( $add ) {
						$registered[] = $course->courseName .", ". $course->courseDay;
						cm_update_course( $course->courseID, $course->courseName, $course->courseDay, $course->courseStartTime, $course->courseEndTime, $course->coursePrice, $course->coursePlaces, $course->courseFreePlaces-1, $course->courseVisible );
					}
				}

				// Output the courses that the participant have been registered to.
				if ( count( $registered ) > 0 ) {
					$message .= __( "Registered to", "coursemanagement" );
					foreach ( $registered as $reg ) {
						$message .= "<br />". $reg;
					}
					//Output the message.
					cm_get_message( $message, "updated" );
				}
				
			} else {
				cm_get_message( __( "Couldn't create participant", "coursemanagement" ), "error" );
			}			
		}
	} else {
		// Output a message if something went wrong
		$message = __( "Something went wrong", "coursemanagement" ) ."<br />";
		if ( $nrOfErrors > 0 )
			$message .= implode( "<br />", $valid );
		
		cm_get_message( $message, "error" );
	}
}
?>
<div class="wrap">
<h2>CM <?php _e( "New Participant", "coursemanagement" ); ?></h2>
<?php
if ( cm_check_tables() > 0 ) {
	$allcourses = cm_get_all_courses();
	if ( $allcourses != 0 )
		$nrOfAllCourses = count( $allcourses );
	else
		$nrOfAllCourses = 0;

	// Check if there are courses with free places.
	$nofree = 0;
	foreach( $allcourses as $row ) {
		if( $row->courseFreePlaces == 0 )
			$nofree++;
	}
?>
	<p><a class="button" href="?page=coursemanagement/cm_participants.php"><?php _e( "Back to Participants", "coursemanagement" ); ?></a></p>
	<form id="addparticipant" name="addparticipant" method="post" action="<?php echo $PHP_SELF ?>">
		<table class="widefat">
			<thead>
			<tr>
				<th><label for="name"><?php _e( "Name","coursemanagement" ); ?></label></th>
				<td><input name="name" type="text" id="name" size="50" maxlength="45" /> <em><?php _e( "Max length", "coursemanagement" ); ?>: 45</em></td>
			</tr>
			<tr>
				<th><label for="email"><?php _e( "Email", "coursemanagement" ); ?></label></th>
				<td><input name="email" type="text" id="email" size="50" /></td>
			</tr>
			<tr>
				<th><?php _e( "Courses", "coursemanagement" ); ?></th>
				<td>
					<?php
					if ( $nrOfAllCourses > 0 ) {
						if( $nofree == 0 || $nofree != $nrOfAllCourses ) {
							for ( $i = 0; $i < $nrOfAllCourses; $i++ ) {
								echo '<br /><input name="participantCourses[]" type="checkbox" id="participantCourses[]" value="'. $allcourses[$i]->courseID .'" /> '. $allcourses[$i]->courseName .', '. $allcourses[$i]->courseDay;
							}
						} else {
							echo '<strong>'. __( "All courses are full", "coursemanagement" ) .'</strong>';
						}
					} else {
						echo '<strong>'. __( "Sorry, no entries.", "coursemanagement" ) .'</strong>';
					}
					?>
				</td>
			</tr>
			</thead>
		</table>
		<p><input name="submitted" type="submit" id="submitted" value="<?php _e( "Create","coursemanagement" ); ?>" class="button-primary" /></p>
	</form>
<?php
} else {
	cm_get_message( __( "CM isn't installed. Please go to settings and install it", "coursemanagement" ), "error fade" );
}
?>
</div>