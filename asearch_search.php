<?php
 
require_once ('./inc/func_main.php');
/* prevent direct access to this page */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Tady nemáš co dělat.';
  trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);
 
/* if the 'term' variable is not sent with the request, exit */
if ( !isset($_REQUEST['term']) ) {
	exit;
}
 
$mysqli = new MySQLi('localhost',$dbusr,$password,$dbusr);
 
/* Connect to database and set charset to UTF-8 */
if($mysqli->connect_error) {
  echo 'Chyba připojení k databázi.' . 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
  exit;
} else {
  $mysqli->set_charset('utf8');
  
}
 
/* retrieve the search term that autocomplete sends */
$term = trim(strip_tags($_GET['term'])); 
/* replace multiple spaces with one */
$term = preg_replace('/\s+/', ' ', $term);
 
$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Povolena jsou pouze písmena a čísla."));
$json_invalid = json_encode($a_json_invalid);
 
/* SECURITY HOLE *************************************************************** */
/* allow space, any unicode letter and digit, underscore and dash                */
if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
  print $json_invalid;
  exit;
}
/* ***************************************************************************** */
 
if ($data = $mysqli->query("SELECT * FROM nw_persons WHERE name LIKE '%$term%' OR surname LIKE '%$term%' ORDER BY name , surname")) {
	while($row = mysqli_fetch_array($data)) {
		$firstname = htmlspecialchars(stripslashes($row['name']));
		$lastname = htmlspecialchars(stripslashes($row['surname']));
		$customercode= htmlspecialchars(stripslashes($row['id']));
		$a_json_row["id"] = $customercode;
		$a_json_row["value"] = $firstname.' '.$lastname;
		$a_json_row["label"] = $firstname.' '.$lastname;
		array_push($a_json, $a_json_row);
	}
}
 
/* jQuery wants JSON data */
echo json_encode($a_json);
flush();
 
$mysqli->close();

?>