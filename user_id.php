<?php

include 'common.php';

if(isset($_GET["user_name"])) {
  $user_name = $_GET["user_name"];
}

$query = <<<EOD
SELECT user_id
FROM user WHERE user_name = $user_name
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>
