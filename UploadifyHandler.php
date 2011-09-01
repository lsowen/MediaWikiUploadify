<?php

class UploadifyHandler extends SpecialPage {
  function __construct() {
    parent::__construct( 'UploadifyHandler', 'UploadifyHandler' );
  }
  
  function execute( $par ) {

    if( isset($_POST["PHPSESSIONID"]) )
      {
	$session_key = $_POST["PHPSESSIONID"];
	wfSetupSession($session_key);
      }
   
    global $wgRequest, $wgOut, $wgFileExtensions, $wgUploadDirectory, $wgMaxUploadFiles, $IP, $wgUser;

    // Plain output
    $wgOut->setArticleBodyOnly(true);
    
    // Check if there are Files for upload
    if (!empty($_FILES)) {
      
      $tempFile = $_FILES['Filedata']['tmp_name'];
      $uploadDir = dirname(__FILE__) . "/upload/";
      $targetFile =  $uploadDir . $_FILES['Filedata']['name'];
      $fileParts  = pathinfo($_FILES['Filedata']['name']);
      $userName = $wgUser->getName();
      
      // Check if upload directory is available
      if( file_exists($uploadDir) && is_writeable($uploadDir) ) {
	// Check if extension is valid
	if (in_array(strtolower($fileParts['extension']),$wgFileExtensions)) {
	  
	  // Copy File
	  move_uploaded_file($tempFile, $targetFile);
	  
	  $shell_command = "php '{$IP}/maintenance/importImages.php' --user='{$userName}' '{$uploadDir}'";
	  // Import File
	  $result = shell_exec($shell_command);
	  
	  // Delete File
	  unlink($targetFile);				
	  
	  // Check if file exists
	  if(stripos($result, "exists, skipping") !== false) {
	    $wgOut->addHTML(wfMsg('file-already-exists'));
	  }else{
	    // Report success
	    $wgOut->addHTML("<input onclick='this.select();' class='uploadify-result' type='text' value='[[File:" . ucfirst($_FILES['Filedata']['name']) . "]]' />");
	  }
	  
	} else {
	  $wgOut->addHTML(wfMsg('invalid-file-type'));
	}

      } else {
	$wgOut->addHTML(wfMsg('uploadify-missing-directory'));
      }

      
    }
  }
}
