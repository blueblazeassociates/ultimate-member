<?php
/*
 * Modified by Blue Blaze Associates, LLC
 *
 * Changes include:
 *
 * egifford 2015_08_21: Patch AJAX issue with um-file-upload and um-image-upload.php.
 *   See https://github.com/ultimatemember/ultimatemember/issues/7
 */

// BEGIN egifford 2015_08_21: Patch AJAX issue with um-file-upload and um-image-upload.php.
// START REPLACED CODE
//$i = 0;
//$dirname = dirname( __FILE__ );
//do {
//	$dirname = dirname( $dirname );
//	$wp_load = "{$dirname}/wp-load.php";
//}
//while( ++$i < 10 && !file_exists( $wp_load ) );
// END REPLACED CODE

// The logic in this file is not loaded via WordPress's built-in AJAX mechanism: https://codex.wordpress.org/AJAX_in_Plugins
// Instead, this file is called directly.
// Because of this, this file needs to manually locate the WordPress installation in order to load the WordPress environment.
// Let's try to find it!
$wp_load = '';

if ( ! empty( $_REQUEST['wp_path'] ) ) {
	// Technique #1: See if WordPress's location has been explicitly set in the request via the wp_path parameter.
	$wp_path = $_REQUEST['wp_path'];
	$wp_path = urldecode( $wp_path );
	$wp_path = $_SERVER['DOCUMENT_ROOT'] . $wp_path;

	$wp_load = $wp_path . 'wp-load.php';
} else {
	// Technique #2: Try to find WordPress by starting with the current file's location and moving up the directory tree.
	$i = 0;
	$dirname = dirname( __FILE__ );
	do {
		$dirname = dirname( $dirname );
		$wp_load = "{$dirname}/wp-load.php";
	}
	while( ++$i < 10 && !file_exists( $wp_load ) );
}

// WordPress has (probably) been found! Load it.
// END egifford 2015_08_21: Patch AJAX issue with um-file-upload and um-image-upload.php.
require_once( $wp_load );
global $ultimatemember;

$id = $_POST['key'];
$ultimatemember->fields->set_id = $_POST['set_id'];
$ultimatemember->fields->set_mode = $_POST['set_mode'];

$ret['error'] = null;
$ret = array();

if(isset($_FILES[$id]['name'])) {

    if(!is_array($_FILES[$id]['name'])) {

		$temp = $_FILES[$id]["tmp_name"];
		$file = $_FILES[$id]["name"];
		$file = sanitize_file_name($file);
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		$error = $ultimatemember->files->check_file_upload( $temp, $extension, $id );
		if ( $error ){
			$ret['error'] = $error;
		} else {
			$ret[] = $ultimatemember->files->new_file_upload_temp( $temp, $file );
			$ret['icon'] = $ultimatemember->files->get_fonticon_by_ext( $extension );
			$ret['icon_bg'] = $ultimatemember->files->get_fonticon_bg_by_ext( $extension );
			$ret['filename'] = $file;
		}

    }

} else {
	$ret['error'] = __('A theme or plugin compatibility issue','ultimatemember');
}
echo json_encode($ret);