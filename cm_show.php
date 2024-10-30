<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_show.php
 * Description: File that displays either a specific course or participant.
 *				Also handling delete of a course or participant.
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}
$action = $_GET['action'];
	
switch ( $action ) {
	case 'showcourse':
		cm_show_course();
		break;
	case 'showparticipant':
		cm_show_participant();
		break;
	case 'deleteparticipantfromcourse':
		if( cm_delete_participant_from_course($_GET['participantID'], $_GET['courseID'] ) )
			cm_get_message( __( "Deleted from course", "coursemanagement"), "updated" );
		else
			cm_get_message( __( "Couldn't delete participant from course", "coursemanagement" ), "error" );
		cm_show_course();
		break;
}

function cm_show_course() {
	$courseID 		= $_GET['courseID'];
	$course 		= cm_get_course( $courseID );
	$participants 	= cm_get_all_participants( $courseID );
?>
<div class="wrap">
<h2><?php echo __( "Viewing", "coursemanagement" ) ." ". $course->courseName; ?></h2>
<p><a class="button" href="?page=coursemanagement/cm_courses.php"><?php _e( "Back to Courses", "coursemanagement" ); ?></a> <a class="button" href="?page=coursemanagement/cm_editcourse.php&courseID=<?php echo $courseID; ?>"><?php _e( "Edit", "coursemanagement" ); ?></a></p>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
		<th style="width: 30%;">ID</th>
		<td><?php echo $course->courseID; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Course name","coursemanagement" ); ?></th>
		<td><?php echo $course->courseName; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Course description","coursemanagement" ); ?></th>
		<td><?php echo $course->courseDesc; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Day","coursemanagement" ); ?></th>
		<td><?php echo $course->courseDay; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Startdate","coursemanagement" ); ?></th>
		<td><?php echo $course->courseStartDate; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Enddate","coursemanagement" ); ?></th>
		<td><?php echo $course->courseEndDate; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Starttime","coursemanagement" ); ?></th>
		<td><?php echo $course->courseStartTime; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Endtime","coursemanagement" ); ?></th>
		<td><?php echo $course->courseEndTime; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Price","coursemanagement" ); ?></th>
		<td><?php echo $course->coursePrice; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Number of places","coursemanagement" ); ?></th>
		<td><?php echo $course->coursePlaces; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Number of free places","coursemanagement" ); ?></th>
		<td><?php echo $course->courseFreePlaces; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Visible", "coursemanagement" ); ?></th>
		<td>
		<?php
		if ( $course->courseVisible == 1 )
			_e( "Yes", "coursemanagement" );
		else
			_e( "No", "coursemanagement" );
		?>
		</td>
	</tr>
	</thead>
</table>
<h3>CM <?php _e( "Participants", "coursemanagement" ); ?></h3>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
		<th><?php _e( "Name", "coursemanagement" ); ?></th>
		<th><?php _e( "Email", "coursemanagement" ); ?></th>
		<th><?php _e( "Registered", "coursemanagement" ); ?></th>
		<th><?php _e( "Actions", "coursemanagement" ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$participants = cm_get_participants_of_course( $courseID );
	if ( $participants == 0 ) {
		echo '<tr><td colspan="9"><strong>'. __( "Sorry, no entries.","coursemanagement" ) .'</strong></td></tr>';
	} else {
		foreach ( $participants as $row ) {
			echo '<tr>';
			echo '<td>'. $row->participantName .'</td>';
			echo '<td>'. $row->participantEmail .'</td>';
			echo '<td>'. $row->participantDate .'</td>';
			echo '<td><span class="trash"><a href="admin.php?page='. $_GET['page'] .'&action=deleteparticipantfromcourse&participantID='. $row->participantID .'&courseID='. $courseID .'" onclick="if ( confirm(\'' . __( "Do you want to delete this participant from this course?\n(It does NOT delete the participant)", "coursemanagement" ) .'\') ) { return true;}return false;" class="delete">'. __( "Delete", "coursemanagement" ) .'</a></span></td>';
			echo '</tr>';
		}
	}
	?>
	</tbody>
</table>
</div>
<?php
}

function cm_show_participant() {
	$participantID  = $_GET['participantID'];
	$participant 	= cm_get_participant( $participantID );
	$courses 		= cm_get_participant_courses( $participantID );
?>
<div class="wrap">
<h2><?php echo __( "Viewing", "coursemanagement" ) ." ". $participant->participantName; ?></h2>
<p><a class="button" href="?page=coursemanagement/cm_participants.php"><?php _e( "Back to Participants", "coursemanagement" ); ?></a> <a class="button" href="?page=coursemanagement/cm_editparticipant.php&participantID=<?php echo $participantID; ?>"><?php _e( "Edit", "coursemanagement" ); ?></a></p>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
		<th style="width: 30%;">ID</th>
		<td><?php echo $participant->participantID; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Name","coursemanagement" ); ?></th>
		<td><?php echo $participant->participantName; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Email","coursemanagement" ); ?></th>
		<td><?php echo $participant->participantEmail; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Registered","coursemanagement" ); ?></th>
		<td><?php echo $participant->participantDate; ?></td>
	</tr>
	<tr>
		<th><?php _e( "Courses","coursemanagement" ); ?></th>
		<td>
		<?php 
			if ( $courses > 0 ) {
				foreach ( $courses as $course ) {
					echo $course->courseName .', '. $course->courseDay .'<br />';
				}
			} else {
				echo '<strong>' . __("Sorry, no entries.","coursemanagement") . '</strong>';
			}
		?>
		</td>
	</tr>
	</thead>
</table>
</div>
<?php
}
?>