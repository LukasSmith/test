<?php
$uploads_dir = './uploads/';

if( $_FILES['Filedata']['error'] == 0 ){
	if( move_uploaded_file( $_FILES['Filedata']['tmp_name'], $uploads_dir.$_FILES['Filedata']['name'] ) ){
		echo 'ok';
		exit();
	}
}
echo 'error';
exit();
?>