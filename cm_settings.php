<?php
/* Plugin: courseManagement (CM)
 * Version: 0.5
 * Filename: cm_settings.php
 * Description: Settings page for CM
 * Author: Jonna T&auml;rneberg
 */
//must check that the user has the required capability 
if ( !current_user_can( 'manage_options' ) ) {
  wp_die( __( "You do not have sufficient permissions to access this page." ) );
}

$settings 	   = get_option( "coursemanagement_settings" );
$installed_ver = $settings['db_version'];
$version 	   = cm_get_version();
$adm_email 	   = $settings['adm_mail'];
$send_mail 	   = $settings['send_mail'];
$action 	   = $_GET['action'];

switch ( $action ) {
	case "upgrade":
		if ( cm_upgrade() )
			cm_get_message( __( "Upgraded with success", "coursemanagement" ), "updated fade" );
			$settings = get_option( "coursemanagement_settings" );
			$installed_ver = $settings['db_version'];
			$version = cm_get_version();
		break;
	case "install":
		if ( cm_install() ) 
			cm_get_message( __( "Installed with success", "coursemanagement" ), "updated fade" );
		break;
}

if ( isset( $_POST['save_settings'] ) ) {
	$send_mail = $_POST['send_mail'];
	$adm_email = $_POST['adm_email'];
	$uninstall = $_POST['drop_cm'];
	
	if ( $uninstall == '1' ) {
		if ( cm_uninstall() )
			cm_get_message( __( "Uninstalled the tables with success", "coursemanagement" ), "updated fade" );
	} else {
		$valid = cm_check_email( $adm_email );
		
		if ( is_array( $valid ) ) { 
			cm_get_message( __( "Please fill in a valid email", "coursemanagement" ), "error fade" );
		} else {
			$settings["adm_mail"] = $adm_email;
			$settings["send_mail"] = $send_mail;
			update_option( "coursemanagement_settings", $settings );
			cm_get_message( __( "Settings saved", "coursemanagement" ), "updated fade" );
		}
	}
}
?>
<div class="wrap">
<h2>CM <?php _e("Settings", "coursemanagement"); ?></h2>
<?php
if( cm_check_tables() == 0 ) {
	echo '<a href="admin.php?page=coursemanagement/cm_settings.php&action=install">'. __( "Install", "coursemanagement" ) .'</a>';
} else {
?>
<form id="form1" name="form1" method="post" action="<?php echo $PHP_SELF ?>">
<table class="form-table">
	<tr valign="top">
		<th scope="row">CM <?php _e( "Version", "coursemanagement" ); ?></th>
		<td>
		<?php
		if ( $installed_ver == $version ) { 
			echo $version .' <span style="color:#00FF00; font-weight:bold;">&radic;</span>';	
		} else {
			echo $installed_ver .' <span style="color:#FF0000; font-weight:bold;">X</span> | <a href="admin.php?page=coursemanagement/cm_settings.php&action=upgrade"><strong>'. __( "Upgrade to","coursemanagement" ) .' '. $version .'</strong></a>';
		}
		?>
		</td>
		<td><em><?php _e( "Version check", "coursemanagement" ); ?></em></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e( "Send mail to admin","coursemanagement" ); ?></th>
		<td>
		<?php
		if ( $send_mail == 0 ) {
			echo '<input type="radio" name="send_mail" value="1" id="send_mail_0" />
					<label for="send_mail_0">'. __( "Yes","coursemanagement" ) .'</label>
					<input type="radio" name="send_mail" value="0" id="send_mail_1" checked="checked" />
					<label for="send_mail_1">'. __( "No","coursemanagement" ) .'</label>';
		} else {
			echo '<input type="radio" name="send_mail" value="1" id="send_mail_0" checked="checked" />
					<label for="send_mail_0">'. __( "Yes","coursemanagement" ) .'</label>
					<input type="radio" name="send_mail" value="0" id="send_mail_1"  />
					<label for="send_mail_1">'. __( "No","coursemanagement" ) .'</label>';
		}
		?>
		</td>
		<td><em><?php _e( "Admin gets a mail when a new registration is made","coursemanagement" ); ?></em></td>
	</tr>
	<tr valign="top">
		<th scope="row">Admin <?php _e( "Email", "coursemanagement" ); ?></th>
		<td><input type="text" name="adm_email" id="adm_email" value="<?php echo $adm_email; ?>" /></td>
		<td><em><?php _e( "Email address", "coursemanagement" ); ?></em></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e( "Uninstall", "coursemanagement" ); ?></th>
		<td>
			<input type="radio" name="drop_cm" value="1" id="drop_cm_0" />
			<label for="drop_cm_0"><?php _e( "Yes", "coursemanagement" ); ?></label>
			<input type="radio" name="drop_cm" value="0" id="drop_cm_1" checked="checked" />
			<label for="drop_cm_1"><?php _e( "No", "coursemanagement" ); ?></label>
		</td>
		<td><em><?php _e( "Removes courseManagement from the database", "coursemanagement" ); ?></em></td>
	</tr>
</table>
<p><input name="save_settings" type="submit" id="save_settings" value="<?php _e( "Save", "coursemanagement" ); ?>" class="button-primary" /></p>
</form>
<?php
}
?>
</div>