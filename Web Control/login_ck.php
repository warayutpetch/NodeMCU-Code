<?php
error_reporting(0);
session_start(); //เปิด seesion เพื่อทำงาน
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
//กำหนดภาษาของเอกสารให้เป็น UTF-8
$username = $_REQUEST['Username'];
$password = $_REQUEST['Password'];

//ประกาศซตัวแปรชื่อ password โดยการรับค่ามาจากกล่อง password ที่หน้า Login
if($username == "") {                    //ถ้ายังไม่ได้กรอกข้อมูลที่ชื่อผู้ใช้ให้ทำงานดังต่อไปนี้
	echo "คุณยังไม่ได้กรอกชื่อผู้ใช้ครับ";
	echo "<meta http-equiv='refresh' content='1;URL=index.php'>";
} else if($password == "") {        //ถ้ายังไม่ได้กรอกรหัสผ่านให้ทำงานดังต่อไปนี้
	echo "คุณยังไม่ได้กรอกรหัสผ่านครับ";
	echo "<meta http-equiv='refresh' content='1;URL=index.php'>";
} else {                                               //ถ้ากรอกข้อมูลทั้งหมดแล้วให้ทำงานดังนี้
@include("connet.php");           //เรียก function สำหรับติดต่อฐานข้อมูลจากหน้า connect.php ขึ้นมา
$sql="SELECT * FROM u Where uname='".$username."' and pword='".$password."' ";
$check_log = mysqli_query($con,$sql);                         //ใช้ภาษา SQL ตรวจสอบข้อมูลในฐานข้อมูล
$num = mysqli_num_rows($check_log);
//ให้เอาค่าที่ได้ออกมาประกาศเป็นตัวแปรชื่อ $num
if($num <=0) {                                                           //ถ้าหากค่าที่ได้ออกมามีค่าต่ำกว่า 1
	echo "Login Fail...<br /><a href='index.php'>Back</a>";
	echo "<script>";
	echo "alert(\" Username หรือ  Password ไม่ถูกต้อง\");"; 
	echo "</script>";
	echo "<meta http-equiv='refresh' content='1;URL=index.php'>";

} else {
	while ($data = mysqli_fetch_array($check_log) ) {
//ถ้าค่ามีมากกว่า 0 ขึ้นไป ให้ดึงข้อมูลออกมาทั้งหมด
if($data[userlvl]==A){                          //ตรวจสอบสถานะของผู้ใช้ว่าเป็น Admin
echo "Hi Welcome Back Admin<br />";             //สร้าง session สำหรับให้ admin นำค่าไปใช้งาน
$_SESSION[ses_userid] = session_id();            //สร้าง session สำหรับเก็บค่า ID
$_SESSION[ses_username] = $username;      //สร้าง session สำหรับเก็บค่า Username
$_SESSION[ses_status] = "admin";                      //สร้าง session สำหรับเก็บค่า สถานะความเป็น Admin
echo "<meta http-equiv='refresh' content='1;URL=admin.php'>";
//ส่งค่าจากหน้านี้ไปหน้า index_admin.php
echo "Waiting..............................";
}elseif($data[userlvl]==U){                              //ตรวจสอบสถานะของผู้ใช้งานว่าเป็น user
$_SESSION[ses_userid] = session_id();                      //สร้าง session สำหรับให้ User นำไปใช้งาน
$_SESSION[ses_username] = $username;
$_SESSION[ses_status] = "user";
echo "<meta http-equiv='refresh' content='1;URL=index_user.php'>";
//ส่งค่าจากหน้านี้ไปหน้า index_user.php
echo "<br /> Waiting User..............................";
}else{
	echo "You Are Boss";
	$_SESSION[ses_userid] = session_id();
	$_SESSION[ses_username] = $username;
	$_SESSION[ses_status] = "boss";
	echo "<meta http-equiv='refresh' content='1;URL=index_boss.php'>";
	echo "<br /> Waiting Boss..............................";
}
}
}
}
?>