<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_editparticipant.php
 * Description: File that displays the form and handles edit participant
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

$participantID = $_GET['participantID'];

if ( isset( $_POST['submitted'] ) ) {
	$id			= $_POST['id'];
	$name 		= $_POST['name'];
	$email 		= $_POST['email'];
	$year 		= $_POST['year'];
	$month		= $_POST['month'];
	$day		= $_POST['day'];
	$hour		= $_POST['hour'];
	$minute		= $_POST['minute'];
	$courses	= $_POST['participantCourses'];
	
	$date = $year.$month.$day.$hour.$minute."00";
	
	$updated = cm_update_participant( $id, $name, $email, $date, $courses );
	if ( $updated == 0 ) {
		cm_get_message( __( "Participant updated", "coursemanagement" ), "updated fade" );
	} else {
		$message = __( "Couldn't update participant", "coursemanagement" ) ."<br />". implode( "<br />", $updated );
		cm_get_message($message, "error fade");
	}
	
	$participant = cm_get_participant( $id );
} else {
	$participant = cm_get_participant( $participantID );
}

$courses = cm_get_participant_courses( $participant->participantID );
$allcourses = cm_get_all_courses();
if ( $allcourses != 0 )
	$nrOfAllCourses = count( $allcourses );
else
	$nrOfAllCourses = 0;
if ( $courses != 0 )
	$nrOfCourses = count( $courses );
else 
	$nrOfCourses = 0;
for ( $i = 0; $i < $nrOfCourses; $i++ ) {
	$courseIDs[] = $courses[$i]->courseID;
}
$tmp = explode( " ", $participant->participantDate );
$date = explode( "-", $tmp[0] );
$time = explode( ":", $tmp[1] );
?>
<div class="wrap">
<h2><?php echo __( "Editing", "coursemanagement" ) ." ". $participant->participantName; ?></h2>
<p><a class="button" href="?page=coursemanagement/cm_participants.php"><?php _e( "Back to Participants", "coursemanagement" ); ?></a></p>
<form id="editparticipant" name="editparticipant" method="post" action="<?php echo $PHP_SELF ?>">
    <table class="widefat">
		<thead>
		<tr>
            <th><label for="name"><?php _e( "Name","coursemanagement" ); ?></label></th>
            <td><input name="name" type="text" id="name" size="50" maxlength="45" value="<?php echo $participant->participantName; ?>" /> <em><?php _e( "Max length", "coursemanagement" ); ?>: 45</em></td>
        </tr>
        <tr>
            <th><label for="email"><?php _e( "Email", "coursemanagement" ); ?></label></th>
            <td><input name="email" type="text" id="email" size="50" value="<?php echo $participant->participantEmail; ?>" /></td>
        </tr>
		<tr>
            <th><label for="date"><?php _e( "Date", "coursemanagement" ); ?></label></th>
            <td><input name="year" type="text" id="year" size="4" maxlength="4" value="<?php echo $date[0]; ?>" />-
			<input name="month" type="text" id="month" size="2" maxlength="2" value="<?php echo $date[1]; ?>" />-
			<input name="day" type="text" id="day" size="2" maxlength="2" value="<?php echo $date[2]; ?>" /> @ 
			<input name="hour" type="text" id="hour" size="2" maxlength="2" value="<?php echo $time[0]; ?>" />:
			<input name="minute" type="text" id="minute" size="2" maxlength="2" value="<?php echo $time[1]; ?>" /></td>
        </tr>
        <tr>
			<th><?php _e( "Courses", "coursemanagement" ); ?></th>
            <td>
				<?php
				if ( $nrOfAllCourses > 0 ) {
					for ( $i = 0; $i < $nrOfAllCourses; $i++ ) {
						if ( $nrOfCourses > 0 ) {
							if ( in_array( $allcourses[$i]->courseID, $courseIDs ) ) {
								echo '<br /><input name="participantCourses[]" type="checkbox" id="participantCourses[]" value="'. $allcourses[$i]->courseID .'" checked="checked" /> '. $allcourses[$i]->courseName .', '. $allcourses[$i]->courseDay;
								$counter++;
							} else {
								echo '<br /><input name="participantCourses[]" type="checkbox" id="participantCourses[]" value="'. $allcourses[$i]->courseID .'" /> '. $allcourses[$i]->courseName .', '. $allcourses[$i]->courseDay;
								$counter++;
							}
						} else {
							echo '<br /><input name="participantCourses[]" type="checkbox" id="participantCourses[]" value="'. $allcourses[$i]->courseID .'" /> '. $allcourses[$i]->courseName .', '. $allcourses[$i]->courseDay;
						}
					}
				} else {
					echo '<strong>'. __( "Sorry, no entries.","coursemanagement" ) .'</strong>';
				}
				?>
			</td>
        </tr>
		</thead>
    </table>
    <p>
        <input name="submitted" type="submit" id="submitted" value="<?php _e( "Update", "coursemanagement" ); ?>" class="button-primary" />
        <input type="hidden" name="id" value="<?php echo $participant->participantID; ?>" />
    </p>
</form>
</div>