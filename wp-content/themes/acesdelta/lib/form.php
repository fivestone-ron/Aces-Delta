<?php
require_once 'vendor/autoload.php';
use phpseclib\Net\SFTP;

require 'defuse-crypto.phar';
define('RENUVEKEY','def00000073d1de35691785908e227e4ad8c08455a36e63b6ea48e948aa5ded3909b746e701752dd5c5f5b540a59d16fe2ef806b23c04085e9d039f784d356c2e867d16e');


add_action( 'gform_after_submission', 'send_to_acesdelta_sftp', 10, 2 );
function send_to_acesdelta_sftp($entry, $form) {
    //$submission      = WPCF7_Submission::get_instance();
	//$submission_data = $submission->get_posted_data();
    $form_id         = $form['id'];
	$post_id         = $entry['post_id'];
	$content_type    = get_the_terms( $post_id, 'content_type' );
	$page_title      = get_the_title( $post_id );
	// Update for new form ids as necessary
	if ($form_id == '1'):
		$form_name = 'Brochure';
	else:
		$form_name = 'Contact US';
	endif;
	$file_name = rgar( $entry, '2' ).'-'.rgar( $entry, '3' ).'-'.time().'.csv';

	$headers = array(
	    'First Name',
        'Last Name',
        'Title',
        'Company Name',
        'Company Type',
        'Country',
        'Email Address',
        'Phone',
        'Contact Me',
        'Comments',
        'Form Name',
        'Content Title',
    );

	$company_type = rgar( $entry, '6' );
	if($form_id == '1'):
    	//$message= rgar( $entry, '14' ); // in dev this is the same in prod it is different
		$message= rgar( $entry, '10' );
	else:
		$message = rgar( $entry, '14' );
	endif;
	$data = array(
		rgar( $entry, '2' ),  //First name
		rgar( $entry, '3' ),  //Last Name
		rgar( $entry, '4' ),  //Title
		rgar( $entry, '5' ),  //Company
		$company_type,
		rgar( $entry, '7' ), //Country
		rgar( $entry, '8' ), //Email Address
		rgar( $entry, '9' ), // Phone Number
        ($form_id == '2' || $form_id == '1') ? 1 : 0,
		empty( $message ) ? null : $message,
		empty( $form_name ) ? null : $form_name,
		empty( $page_title ) ? null : $page_title,
	);
    
	$file_info = create_csv( $file_name, $headers, $data );
	//setcookie('acesdelta_pc_enabled', 'true', time() + (10 * 365 * 24 * 60 * 60), "/", get_domain() ); //sets cookie for 10 years!!
	upload_to_acesdelta( $headers, $data, $file_info['file_name'], $file_info['file_dir'], $form_id );

}

function create_csv( $file_name, $headers, $data ) {

	$local_file_dir = get_template_directory().'/temp/';

	$file = fopen( $local_file_dir.$file_name, 'ab+' );
	fputcsv($file, $headers );
 	fputcsv($file, $data);
	fclose($file);
	return array( 'file_dir'=>$local_file_dir, 'file_name'=>$file_name, );
}

function upload_to_acesdelta( $headers, $data, $file_name, $file_dir, $subdirectory ) {
	$sftp = new SFTP('sftp.mhpowersystems.com');
	$dir  = '/XFER/SharedServices/CRM/mower/';
	//if( ! empty( $subdirectory ) ) {
	  //  $dir .= $subdirectory . '/';
   // }
	if (!$sftp->login('mower_user1', 'West-Plaster-Simple-Worry-189')) {
        error_log('### ### Not Logged in');
	}
    if( $sftp->put( $dir.$file_name, $file_dir.$file_name, SFTP::SOURCE_LOCAL_FILE) ) :
		error_log('### ### sent');
        unlink( $file_dir.$file_name );
	else:
        $errors = json_encode($sftp->getSFTPErrors());
        error_log("### ### file error ".$errors);
		error_log('### ### Not sent');
   endif;
}



