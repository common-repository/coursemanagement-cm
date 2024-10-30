<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_editcourse.php
 * Description: File that displays the form and handles edit course
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

$courseID = $_GET['courseID'];

if ( isset( $_POST['submitted'] ) ) {
	$id			= $_POST['id'];
	$name 		= $_POST['name'];
	$desc       = $_POST['desc'];
	$day 		= $_POST['day'];
	$startdate 	= $_POST['startdate'];
	$enddate 	= $_POST['enddate'];
	$starttime 	= $_POST['starttime'];
	$endtime 	= $_POST['endtime'];
	$price 		= $_POST['price'];
	$places 	= $_POST['places'];
	$visible 	= $_POST['visible'];
	
	$course = cm_get_course( $courseID );
	$valid = cm_update_course( $id, $name, $desc, $day, $startdate, $enddate, $starttime, $endtime, $price, $places, $places, $visible );
	
	if ( is_array( $valid ) ) {
		$message = __( "Couldn't update course", "coursemanagement" ) ."<br />". implode( "<br />", $valid );
		cm_get_message($message, "error fade");
	} else {
		cm_get_message( __( "Course updated", "coursemanagement" ), "updated fade" );
	}
	
	$course = cm_get_course( $id );
} else {
	$course = cm_get_course( $courseID );
}

?>
<div class="wrap">
<h2><?php echo __( "Editing", "coursemanagement" ) ." ". $course->courseName; ?></h2>
<p><a class="button" href="?page=coursemanagement/cm_courses.php"><?php _e( "Back to Courses", "coursemanagement" ); ?></a></p>
<form id="editcourse" name="editcourse" method="post" action="<?php echo $PHP_SELF ?>">
    <table class="widefat">
		<thead>
		<tr>
            <th><label for="name"><?php _e( "Course name","coursemanagement" ); ?></label></th>
            <td><input name="name" type="text" id="name" size="50" value="<?php echo $course->courseName; ?>" /></td>
        </tr>
        <tr>
            <th><label for="desc"><?php _e("Course description","coursemanagement"); ?></label></th>
            <td><textarea name="desc" id="desc" rows="8" cols="40"><?php echo $course->courseDesc; ?></textarea></td>
        </tr>
        <tr>
            <th><label for="day"><?php _e( "Day","coursemanagement" ); ?></label></th>
            <td><input name="day" type="text" id="day" size="50" value="<?php echo $course->courseDay; ?>" /> <em><?php _e("Mondays, Tuesdays", "coursemanagement"); ?> | <?php _e("Max length", "coursemanagement"); ?>: 45</em></td>
        </tr>
        <tr>
            <th><label for="startdate"><?php _e( "Startdate","coursemanagement" ); ?></label></th>
            <td><input name="startdate" type="text" id="startdate" size="50" value="<?php echo $course->courseStartDate; ?>" /> <em>YYYY-MM-DD</em></td>
        </tr>
        <tr>
            <th><label for="enddate"><?php _e( "Enddate","coursemanagement" ); ?></label></th>
            <td><input name="enddate" type="text" id="enddate" size="50" value="<?php echo $course->courseEndDate; ?>" /> <em>YYYY-MM-DD</em></td>
        </tr>
        <tr>
            <th><label for="starttime"><?php _e( "Starttime","coursemanagement" ); ?></label></th>
            <td><input name="starttime" type="text" id="starttime" size="50" value="<?php echo $course->courseStartTime; ?>" /> <em>00:00</em></td>
        </tr>
        <tr>
            <th><label for="endtime"><?php _e( "Endtime","coursemanagement" ); ?></label></th>
            <td><input name="endtime" type="text" id="endtime" size="50" value="<?php echo $course->courseEndTime; ?>" /> <em>00:00</em></td>
        </tr>
        <tr>
            <th><label for="price"><?php _e( "Price","coursemanagement" ); ?></label></th>
            <td><input name="price" type="text" id="price" size="6" value="<?php echo $course->coursePrice; ?>" /></td>
        </tr>
        <tr>
            <th><label for="places"><?php _e( "Number of places","coursemanagement" ); ?></label></th>
			<td>
			<?php if( $course->courseFreePlaces != $course->coursePlaces ) { ?>
				<input name="places" type="text" id="places" size="6" disabled="disabled" value="<?php echo $course->coursePlaces; ?>" /><em><?php _e("You can't edit places when there are participants to the course", "coursemanagement")?></em>
			<?php } else { ?>
				<input name="places" type="text" id="places" size="6" value="<?php echo $course->coursePlaces; ?>" />
			<?php } ?>
			</td>
        </tr>
		<tr>
            <th><label for="visible"><?php _e( "Visible", "coursemanagement" ); ?></label></th>
            <td><select name="visible" id="visible">
			<?php 
			
			if ( $course->courseVisible == 1 ) {
                echo '<option value="1" selected="selected">'.  __( "Yes", "coursemanagement" ) .'</option>
                <option value="0">'. __( "No", "coursemanagement" ) .'</option>';
			} else {
				echo '<option value="1">'. __( "Yes", "coursemanagement" ) .'</option>
                <option value="0" selected="selected">'.  __( "No", "coursemanagement" ) .'</option>';
			}
			?>
            </select></td>
        </tr>
		</thead>
    </table>
    <p>
        <input name="submitted" type="submit" id="submitted" value="<?php _e( "Update", "coursemanagement" ); ?>" class="button-primary" />
        <input type="hidden" name="id" value="<?php echo $course->courseID; ?>" />
    </p>
</form>
</div>