<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_courses.php
 * Description: File that displays the current courses and 
 *              handles the deleting of a course
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

$courses = cm_get_all_courses();
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ( $action == 'delete' ) {
	if ( cm_delete_course( $_GET['courseID'] ) )
		cm_get_message( __( "Course deleted", "coursemanagement" ), "updated fade" );
	else
		cm_get_message( __( "Couldn't deleted course", "coursemanagement" ), "error fade" );
}
if ( isset( $_GET['sortby'] ) ) {
	$orderby = $_GET['orderby'];
	$courses = cm_get_all_courses($orderby);
}

?>
<div class="wrap">
<h2>CM <?php _e( "Courses", "coursemanagement" ); ?> <a class="button add-new-h2" href="?page=coursemanagement/cm_addcourse.php"><?php _e( "New Course", "coursemanagement" ); ?></a></h2>
<?php
if ( cm_check_tables() > 0 ) {
?>
<div class="tablenav">
<div class="alignleft actions">
<form name="orderbyform" action="?page=coursemanagement/cm_courses.php" method="get"> 
<input type="hidden" name="page" value="coursemanagement/cm_courses.php" />
<select name="orderby">
<option value="courseID">ID</option>
<option value="courseName"><?php _e( "Name", "coursemanagement" ); ?></option>
<option value="courseDay"><?php _e( "Day", "coursemanagement" ); ?></option>
<option value="courseStartDate"><?php _e( "Startdate","coursemanagement" ); ?></option>
<option value="courseEndDate"><?php _e( "Enddate","coursemanagement" ); ?></option>
<option value="courseStartTime"><?php _e( "Starttime","coursemanagement" ); ?></option>
<option value="courseEndTime"><?php _e( "Endtime","coursemanagement" ); ?></option>
<option value="coursePrice"><?php _e( "Price","coursemanagement" ); ?></option>
<option value="coursePlaces"><?php _e( "Places","coursemanagement" ); ?></option>
<option value="courseFreePlaces"><?php _e( "Free Places", "coursemanagement" ); ?></option>
<option value="courseVisible"><?php _e( "Visible", "coursemanagement" ); ?></option>
</select>
<input class="button-secondary" name="sortby" type="submit" value="<?php _e( "Sort by","coursemanagement"); ?>" />
</form>
</div>
</div>
<table cellpadding="3" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
	  	<th>ID</th>
      	<th><?php _e( "Name", "coursemanagement" ); ?></th>
    	<th><?php _e( "Day", "coursemanagement" ); ?></th>
    	<th><?php _e( "Startdate","coursemanagement" ); ?></th> 
        <th><?php _e( "Enddate","coursemanagement" ); ?></th>
        <th><?php _e( "Starttime","coursemanagement" ); ?></th> 
        <th><?php _e( "Endtime","coursemanagement" ); ?></th>
        <th><?php _e( "Price","coursemanagement" ); ?></th>
        <th><?php _e( "Places","coursemanagement" ); ?></th>
		<th><?php _e( "Free Places", "coursemanagement" ); ?></th>
        <th><?php _e( "Visible", "coursemanagement" ); ?></th>
		<th><?php _e( "Actions", "coursemanagement" ); ?></th>
    </tr>
    </thead>
    <tbody>
	<?php
	if ( $courses == 0 ) {
		echo '<tr><td colspan="9"><strong>'. __( "Sorry, no entries.","coursemanagement" ) .'</strong></td></tr>';
	} else {
		foreach( $courses as $row ) {
			echo '<tr>';
			echo '<td>'. $row->courseID .'</td>';
			echo '<td>'. $row->courseName .'</td>';
			echo '<td>'. $row->courseDay .'</td>';
			echo '<td>'. $row->courseStartDate .'</td>';
			echo '<td>'. $row->courseEndDate .'</td>';
			echo '<td>'. $row->courseStartTime .'</td>';
			echo '<td>'. $row->courseEndTime .'</td>';
			echo '<td>'. $row->coursePrice . '</td>';
			echo '<td>'. $row->coursePlaces .'</td>';
			echo '<td>' . $row->courseFreePlaces . '</td>';
			if( $row->courseVisible == 1 )
				echo '<td>' . __( "Yes", "coursemanagement" ) . '</td>';
			else
				echo '<td>' . __( "No", "coursemanagement" ) . '</td>';
			echo '<td><a href="admin.php?page=coursemanagement/cm_show.php&action=showcourse&courseID='. $row->courseID .'">'. __( "Show", "coursemanagement" ) .'</a> | 
			<a href="admin.php?page=coursemanagement/cm_editcourse.php&courseID='. $row->courseID .'">'. __( "Edit", "coursemanagement" ) .'</a> | 
			<span class="trash"><a href="admin.php?page='. $_GET['page'] .'&action=delete&courseID='. $row->courseID .'" onclick="if ( confirm(\''. __( "Do you want to delete this course?", "coursemanagement" ) .'\') ) { return true;}return false;">'. __( "Delete", "coursemanagement" ) .'</a></span></td>';
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
