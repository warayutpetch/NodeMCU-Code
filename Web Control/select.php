<?php
require_once("config.php");

$sql1 = "select max(id) as MAXs from log ";
$sql_query = mysqli_query($connect,$sql1);
$array1 = array();

$i = mysqli_fetch_assoc($sql_query);

$max = $i["MAXs"]-24;


$sql2 = "select moisture,dt,t from log where id>$max ";
$sql_query = mysqli_query($connect,$sql2);
$array2 = array();

while($i = mysqli_fetch_assoc($sql_query)){
	$array2[] = $i;
}
print json_encode($array2);
?>