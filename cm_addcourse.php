<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_addcourse.php
 * Description: File that displays the form and handles add course
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

if ( isset( $_POST['submitted'] ) ) {
	$name 		= $_POST['name'];
	$desc       = $_POST['desc'];
	$day 		= $_POST['day'];
	$startdate  = $_POST['startdate'];
	$enddate    = $_POST['enddate'];
	$starttime 	= $_POST['starttime'];
	$endtime 	= $_POST['endtime'];
	$price 		= $_POST['price'];
	$places 	= $_POST['places'];
	$visible 	= $_POST['visible'];
	
	$valid = cm_add_course( $name, $desc, $day, $startdate, $enddate, $starttime, $endtime, $price, $places, $visible );
	
	if ( is_array( $valid ) ) {
		$message = __( "Couldn't create course", "coursemanagement" ) ."<br />". implode( "<br />", $valid );
		cm_get_message( $message, "error fade" );
	} else {
		cm_get_message( __( "Course created", "coursemanagement" ), "updated fade" );
	}
}
?>
<div class="wrap">
<h2>CM <?php _e( "New Course", "coursemanagement" ); ?></h2><?php
if ( cm_check_tables() > 0 ) {
?>
<p><a class="button" href="?page=coursemanagement/cm_courses.php"><?php _e( "Back to Courses", "coursemanagement" ); ?></a></p>
<form id="addcourse" name="addcourse" method="post" action="<?php echo $PHP_SELF ?>">
    <table class="widefat">
		<thead>
		<tr>
            <th><label for="name"><?php _e("Course name","coursemanagement"); ?></label></th>
            <td><input name="name" type="text" id="name" size="50" /></td>
        </tr>
        <tr>
            <th><label for="desc"><?php _e("Course description","coursemanagement"); ?></label></th>
            <td><textarea name="desc" id="desc" rows="8" cols="40"></textarea></td>
        </tr>
        <tr>
            <th><label for="day"><?php _e("Day","coursemanagement"); ?></label></th>
            <td><input name="day" type="text" id="day" size="50" /> <em><?php _e( "Mondays, Tuesdays", "coursemanagement" ); ?> | <?php _e( "Max length", "coursemanagement" ); ?>: 45</em></td>
        </tr>
        <tr>
            <th><label for="startdate"><?php _e( "Startdate","coursemanagement" ); ?></label></th>
            <td><input name="startdate" type="text" id="startdate" size="50" /> <em>YYYY-MM-DD</em></td>
        </tr>
        <tr>
            <th><label for="enddate"><?php _e( "Enddate","coursemanagement" ); ?></label></th>
            <td><input name="enddate" type="text" id="enddate" size="50" /> <em>YYYY-MM-DD</em></td>
        </tr>
        <tr>
            <th><label for="starttime"><?php _e( "Starttime","coursemanagement" ); ?></label></th>
            <td><input name="starttime" type="text" id="starttime" size="50" /> <em>00:00</em></td>
        </tr>
        <tr>
            <th><label for="endtime"><?php _e( "Endtime","coursemanagement" ); ?></label></th>
            <td><input name="endtime" type="text" id="endtime" size="50" /> <em>00:00</em></td>
        </tr>
        <tr>
            <th><label for="price"><?php _e( "Price","coursemanagement" ); ?></label></th>
            <td><input name="price" type="text" id="price" size="6" /></td>
        </tr>
        <tr>
            <th><label for="places"><?php _e( "Number of places","coursemanagement" ); ?></label></th>
            <td><input name="places" type="text" id="places" size="6" /></td>
        </tr>
		<tr>
            <th><label for="visible"><?php _e( "Visible", "coursemanagement" ); ?></label></th>
            <td><select name="visible" id="visible">
                <option value="1"><?php _e( "Yes", "coursemanagement" ); ?></option>
                <option value="0"><?php _e( "No", "coursemanagement" ); ?></option>
            </select></td>
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