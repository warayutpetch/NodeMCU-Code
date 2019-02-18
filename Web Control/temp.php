
<?php
require_once("config.php");
$moisture = $_GET['moisture'];
$dt = $_GET['dt'];
$t = $_GET['t'];

$sql = "insert into log(moisture,dt,t) value('$moisture','$dt','$t');";
$sql_query = mysqli_query($connect,$sql);
if ($sql_query) {

} else {

}

?>
