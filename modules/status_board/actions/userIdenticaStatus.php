<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/engine/identica.lib.php");

function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
    
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
    
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}



$identicaUsername = UserSettings::get_setting($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'], "identi.ca_username");
$identicaPassword = UserSettings::get_setting($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'], "identi.ca_password");

$identica = new Identica($identicaUsername->setting_value, $identicaPassword->setting_value);

// update status
//$identica->updateStatus("is trying to update using the http://code.google.com/p/identica-php library");

$identicaIDs = array("172174", "172175");

echo ("<br />");

foreach ($identicaIDs AS $userID) {
	$userInfo = $identica->getUserTimeline("xml", $id = $userID, $count = 1);
	
	// convert Identi.ca xml data into proper PHP array
	$xmlObj = simplexml_load_string($userInfo);
	$arrXml = objectsIntoArray($xmlObj);
	
	// display only the status
	$imgURL = $arrXml['status']['user']['profile_image_url'];
	$imgTag  = ("<img ");
	$imgTag .= ("title=\"" . $arrXml['status']['user']['name'] . "\" ");
	$imgTag .= ("src=\"" . $imgURL . "\" ");
	$imgTag .= ("width=\"20\" height=\"20\">");
	
	echo ($imgTag . $arrXml['status']['text']);
	echo ("&nbsp&nbsp&nbsp&nbsp&nbsp");
}




?>