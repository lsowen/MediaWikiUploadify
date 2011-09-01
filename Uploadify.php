<?php

$wgExtensionCredits["other"][] = array(
	"name" => "Uploadify Extension",
	"author" => "Tobias Strebitzer",
	"version" => "0.2.0",
	"url" => "http://www.strebitzer.at",
	"description" => "uploadify-desc"
 );

$wgHooks['BeforePageDisplay'][] = 'tsUploadifyScripts';
$wgHooks['SkinBuildSidebar'][] = 'tsUploadifyBox';

$dir = dirname(__FILE__) . '/';
$wgAvailableRights[] = 'uploadify';
$wgSpecialPages['UploadifyHandler'] = 'UploadifyHandler';
$wgAutoloadClasses['UploadifyHandler'] = $dir . "UploadifyHandler.php";
$wgExtensionMessagesFiles['Uploadify'] = $dir . 'Uploadify.i18n.php';


function tsUploadifyScripts( &$out, &$sk )
{
	global $wgOut, $wgUser, $wgScriptPath, $wgScript;
	
        if ( !$wgUser->isAllowed( 'uploadify' ) ) {
                return true;
        }

        // Add Scripts
        $wgOut->addScriptFile("$wgScriptPath/extensions/Uploadify/public/jquery-1.5.min.js");
        $wgOut->addScriptFile("$wgScriptPath/extensions/Uploadify/public/swfobject.js");
        $wgOut->addScriptFile("$wgScriptPath/extensions/Uploadify/public/jquery.uploadify.v2.1.4.min.js");


        // Add Init Script
        $script  = "$(document).ready(function() {";
        $script .= "$('#uploadify').uploadify({";
        $script .= "'uploader'    : '$wgScriptPath/extensions/Uploadify/public/uploadify.swf',";
        $script .= "'cancelImg'   : '$wgScriptPath/extensions/Uploadify/public/cancel.png',";
        $script .= "'script'      : '$wgScript/Special:UploadifyHandler',";
        $script .= "'folder'      : '$wgScriptPath/uploads',";
        $script .= "'auto'        : true,";
        $script .= "'multi'       : true,";
        $script .= "'buttonText'  : '" . wfMsg('uploadify-button-text') . "',";
        $script .= "'removeCompleted': false,";
        $script .= "'scriptData': { 'PHPSESSIONID': '" . session_id() . "' },";
        $script .= "'onComplete': function(event, id, fileObj, response, data) { $(\"div#uploadify\"+id).html(\"<div class='uploadify-mediawiki-result'>\" + response + \"</div>\"); }";
        $script .= "});";
        $script .= "});";

        $wgOut->addScript("<script language='javascript' type='text/javascript'>$script</script>");

        // Add Css
        $wgOut->addStyle("$wgScriptPath/extensions/Uploadify/public/uploadify.css", "screen");


	return true;
}

function tsUploadifyBox( $skin, &$bar ) {
  	global $wgUser, $wgScript;


        if ( !$wgUser->isAllowed( 'uploadify' ) ) {
                return true;
        }


	$out  = "<div style='padding: 12px 6px;'>";
	$out .= "<input id='uploadify' name='uploadify' type='file' />";
	$out .= '</div>';
	$out .= "<div class='uploadify-file-list-link'><a href='$wgScript/Special:NewFiles' target='_parent'>";
	$out .= wfMsg('uploadify-file-list');
	$out .= "</a>";
	$out .= "</div>";

	 
	$bar['Upload'] = $out;
	return true;
}

