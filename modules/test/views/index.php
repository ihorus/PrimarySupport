<?php
gatekeeper(3);

//$activity = Activity::display_relevant_activity($schoolUID = 3);

//print_r($activity);

//$test = Session::forgot_password("ab2394@wallingfosdfrd.oxon.sch.uk");






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













require_once($_SERVER["DOCUMENT_ROOT"] . "/engine/identica.lib.php");
$identica = new Identica("dox", "passport7");
//$identica->updateStatus("is trying to update using the http://code.google.com/p/identica-php library");

//echo $identica->showStatus("xml","22818391");
$status = $identica->getUserTimeline("xml", $id = 172174, $count = 1);








$xmlObj = simplexml_load_string($status);
$arrXml = objectsIntoArray($xmlObj);
printArray($arrXml['status']['text']);





//172174
//	echo ("you just updated your identi.ca status!");
//}

/*$user = User::find_by_uid($_SESSION['currentUser']['uid']);

$userSettings = UserSettings::get_settings($user->uid);

foreach ($userSettings as $setting) {
	echo ("Setting: " . $setting->settings_array);
	echo ("<br />");
}
*/
?>