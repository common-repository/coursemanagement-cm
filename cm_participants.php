<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_participants.php
 * Description: File that displays the current participants and 
 *              handles the deleting of a participant
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( "manage_options" ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

$participants = cm_get_all_participants();
$action = isset($_GET['action']) ? $_GET['action'] : '';
$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : '';

if ( $action == 'delete' ) {
	if ( cm_delete_participant( $_GET['participantID'] ) ) {
		cm_get_message( __( "Participant deleted", "coursemanagement" ), "updated" );
	} else {
		cm_get_message( __( "Couldn't delete participant", "coursemanagement" ), "error" );
	}
}
if ( $sortby == 'Sort by' ) {
	$orderby = $_GET['orderby'];
	$participants = cm_get_all_participants($orderby);
}


?>
<div class="wrap">
<h2>CM <?php _e( "Participants", "coursemanagement" ); ?> <a class="button add-new-h2" href="?page=coursemanagement/cm_addparticipant.php"><?php _e( "New Participant","coursemanagement" ); ?></a></h2>
<?php
if ( cm_check_tables() > 0 ) {
?>
<div class="tablenav">
<div class="alignleft actions">
<form name="order_filter" action="?page=coursemanagement/cm_participants.php" method="get"> 
<input type="hidden" name="page" value="coursemanagement/cm_participants.php" />
<select name="orderby">
<option value="participantID">ID</option>
<option value="participantName"><?php _e( "Name", "coursemanagement" ); ?></option>
<option value="participantEmail"><?php _e( "Email","coursemanagement" ); ?></option>
<option value="participantRegistered"><?php _e( "Registered","coursemanagement" ); ?></option>
</select>
<input class="button-secondary" name="sortby" type="submit" value="<?php _e( "Sort by","coursemanagement"); ?>" />
</form>
</div>
</div>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
	  	<th>ID</th>
      	<th><?php _e( "Name", "coursemanagement" ); ?></th>
    	<th><?php _e( "Email", "coursemanagement" ); ?></th>
		<th><?php _e( "Registered", "coursemanagement" ); ?></th>
		<th><?php _e( "Actions", "coursemanagement" ); ?></th>
    </tr>
    </thead>
    <tbody>
	<?php
	if ( $participants == 0 ) {
		echo '<tr><td colspan="9"><strong>'. __( "Sorry, no entries.","coursemanagement" ) .'</strong></td></tr>';
	} else {
		foreach( $participants as $row ) {
			echo '<tr>';
			echo '<td>'. $row->participantID .'</td>';
			echo '<td>'. $row->participantName .'</td>';
			echo '<td>'. $row->participantEmail .'</td>';
			echo '<td>'. $row->participantDate .'</td>';
			echo '<td><a href="admin.php?page=coursemanagement/cm_show.php&action=showparticipant&participantID='. $row->participantID .'">'. __( "Show", "coursemanagement" ) .'</a> | 
			<a href="admin.php?page=coursemanagement/cm_editparticipant.php&participantID='. $row->participantID .'">'. __( "Edit", "coursemanagement" ) .'</a> | 
			<span class="trash"><a href="admin.php?page='. $_GET['page'] .'&action=delete&participantID='. $row->participantID .'" onclick="if ( confirm(\''. __( "Do you want to delete this participant?", "coursemanagement" ) .'\') ) { return true;}return false;">'. __( "Delete", "coursemanagement" ) .'</a></span></td>';
			echo '</tr>';
		}
	}
	?>
</tbody>
</table>
<?php
} else {
	cm_get_message( __( "CM isn't installed. Please go to settings and install it", "coursemanagement" ), "error fade" );
}
?>
</div>