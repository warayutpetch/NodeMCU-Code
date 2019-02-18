<?php
session_start(); //เปิด session
$ses_userid =$_SESSION[ses_userid];                                          //สร้าง session สำหรับเก็บค่า ID
$ses_username = $_SESSION[ses_username];                          //สร้าง session สำหรับเก็บค่า username
//ตรวจสอบว่าทำการ Login เข้าสู่ระบบมารึยัง
if($ses_userid <> session_id() or $ses_username ==""){
	echo "Please Login to system<br />";
}


//ตรวจสอบสถานะว่าใช่ admin รึเปล่า ถ้าไม่ใช่ให้หยุดอยู่แค่นี้
if($_SESSION[ses_status] != "admin") {
	echo "This page for Admin only!";
	echo "<a href=index.php>Back</a>";
	echo "<meta http-equiv='refresh' content='1;URL=index.php'>";
	exit();
}
?>


<!doctype html>
<html><head>
<meta charset="utf-8">
<title>ระบบควบคุมวาล์วน้ำ</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<!-- Le styles -->
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />

<link href="css/main.css" rel="stylesheet">
<link href="css/font-style.css" rel="stylesheet">
<link href="css/register.css" rel="stylesheet">


<script type="text/javascript" src="js/jquery.js"></script>    
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>


<script src="../amcharts/amcharts.js" type="text/javascript"></script>
<script src="../amcharts/serial.js" type="text/javascript"></script>


<!-- ///////////-->
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />

<link href="css/main.css" rel="stylesheet">
<link href="css/font-style.css" rel="stylesheet">
<link href="css/flexslider.css" rel="stylesheet">

<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/mqttws31.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/i18n/defaults-*.min.js"></script-->

<script src="js/raphael-2.1.4.min.js"></script>
<script src="js/justgage.js"></script>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.css"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css"></script>
<script src="js/progressbar.js"></script>
<script src="js/progressbar.min.js"></script>


<style type="text/css">
	body {
		padding-top: 90px;
	}

	.modal-body,.modal-footer,.modal-content {
		background-color: #1f1f1f;
	}


</style>
</style>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->


<!-- Google Fonts call. Font Used Open Sans & Raleway -->
<link href="http://fonts.googleapis.com/css?family=Raleway:400,300" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

<script>
	
	


	var config = {
		mqtt_server: "m21.cloudmqtt.com",
		mqtt_websockets_port: 38307,
		mqtt_user: "Test",
		mqtt_password: "12345678"
	};
	var g1, g2, g3, g4;

	$(document).ready(function(e) {

		var bar = new ProgressBar.Line(bb, {
			strokeWidth: 4,
			//easing: 'easeInOut',
			duration: 1400,
			color: '#FFEA82',
			trailColor: '#eee',
			trailWidth: 1,
			svgStyle: {width: '100%', height: '100%'},
			from: {color: '#FFEA82'},
			to: {color: '#ED6A5A'},
			step: (state, bar) => {
				bar.path.setAttribute('stroke', state.color);
			}
			
		});



		$('#onoff1').bootstrapToggle('disable');
		$('#onoff2').bootstrapToggle('disable');
		$('#onoff3').bootstrapToggle('disable');
		$('#onoff4').bootstrapToggle('disable');
		var timeset_old;
		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});
		var g1 = new JustGage({
			id: "g1",
			value: 0,
			min: 0,
			max: 100,
			title: "Moisture of Soil",
			label: "%"
		});
		//$('.mdb-select').material_select();
	// Create a client instance
	client = new Paho.MQTT.Client(config.mqtt_server, config.mqtt_websockets_port, "web_" + parseInt(Math.random() * 100, 10)); 
	//Example client = new Paho.MQTT.Client("m11.cloudmqtt.com", 32903, "web_" + parseInt(Math.random() * 100, 10));
	
	// connect the client
	client.connect({
		useSSL: true,
		userName: config.mqtt_user,
		password: config.mqtt_password,
		onSuccess: function() {
			// Once a connection has been made, make a subscription and send a message.
			// console.log("onConnect");
			$("#status").text("เชื่อมต่อสำเร็จ").removeClass().addClass("connected");
			$("#value1").removeClass().addClass("connected");
			client.subscribe("/ESP/LED");
			client.subscribe("/setdevice1");
			client.subscribe("/setdevice2");
			client.subscribe("/setdevice3");
			client.subscribe("/setdevice4");
			client.subscribe("/ESP/temp");
			//client.subscribe("/ESP/flow");
			client.subscribe("/ESP/SMART");
			mqttSend("/ESP/LED", "GET-DATA");
			mqttSend("/setdevice1", "GET-TIME");
			mqttSend("/ESP/LED","Soil");


		},
		onFailure: function(e) {
			$("#status").text("ผิดพลาด : " + e).removeClass().addClass("error");
			// console.log(e);
		}
	});
	
	client.onConnectionLost = function(responseObject) {
		if (responseObject.errorCode !== 0) {
			$("#status").text("onConnectionLost:" + responseObject.errorMessage).removeClass().addClass("connect");
			setTimeout(function() { client.connect() }, 1000);
		}
	}
	
	client.onMessageArrived = function(message) {
		// $("#status").text("onMessageArrived:" + message.payloadString).removeClass().addClass("error");
		//destinationName >>>> topic
		console.log(message.destinationName);
		console.log(message.payloadString);
		console.log(message.payloadString.length);
		if(message.destinationName =="/ESP/SMART"){

			if(message.payloadString == "ON"){ 
				$('#smart').bootstrapToggle('on');
				

			}
			if(message.payloadString == "OFF") {
				$('#smart').bootstrapToggle('off');
			}
		}

	/*	if(message.destinationName == "/ESP/flow"){
			var rate = message.payloadString;
			rate = parseInt(rate);
			$("#ct").text(rate);
			if(rate>60){
				rate = 60;
			}
			rate=(rate *100)/60;

			rate = rate/100;

			bar.animate(rate)

		}*/

		// if(message.destinationName =="/ESP/LED" && message.payloadString.length == 112){
		// 	var k,j=0,l;
		// 	var text;
		// 	for(k=0;k<16;k++){
		// 		if(message.payloadString[0+j] == "1"){
		// 			text +="จ "
		// 		}
		// 		if(message.payloadString[1+j] == "1"){
		// 			text +="อ "
		// 		}
		// 		if(message.payloadString[2+j] == "1"){
		// 			text +="พ "
		// 		}
		// 		if(message.payloadString[3+j] == "1"){
		// 			text +="พฤ "
		// 		}
		// 		if(message.payloadString[4+j] == "1"){
		// 			text +="ศ "
		// 		}
		// 		if(message.payloadString[5+j] == "1"){
		// 			text +="ส "
		// 		}
		// 		if(message.payloadString[6+j] == "1"){
		// 			text +="อา "
		// 		}

		// 		if(k=0){
		// 			$("#week1-1").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=1){
		// 			$("#week1-2").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=2){
		// 			$("#week1-3").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=3){
		// 			$("#week1-4").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=4){
		// 			$("#week2-1").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=5){
		// 			$("#week2-2").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=6){
		// 			$("#week2-3").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=7){
		// 			$("#week2-4").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=8){
		// 			$("#week3-1").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=9){
		// 			$("#week3-2").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=10){
		// 			$("#week3-3").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=11){
		// 			$("#week3-4").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=12){
		// 			$("#week4-1").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=13){
		// 			$("#week4-2").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=14){
		// 			$("#week4-3").text(text).removeClass().addClass("connected");;
		// 		}
		// 		if(k=15){
		// 			$("#week4-4").text(text).removeClass().addClass("connected");;
		// 		}
		// 		j+=7;
		// 		text="";
		// 	}


		// }
		if(message.destinationName =="/ESP/SMART" && message.payloadString.length == 8){
			$("#s_moiture").text("ทำงานเมื่อความชื้นตำกว่า "+message.payloadString[0]+message.payloadString[1]+" เปอร์เซ็นต์").removeClass().addClass("connected");
			
			
			if(message.payloadString[4] == "1") $('#V1').bootstrapToggle('on');
			if(message.payloadString[5] == "1") $('#V2').bootstrapToggle('on');
			if(message.payloadString[6] == "1") $('#V3').bootstrapToggle('on');
			if(message.payloadString[7] == "1") $('#V4').bootstrapToggle('on');

			if(message.payloadString[4] == "0") $('#V1').bootstrapToggle('off');
			if(message.payloadString[5] == "0") $('#V2').bootstrapToggle('off');
			if(message.payloadString[6] == "0") $('#V3').bootstrapToggle('off');
			if(message.payloadString[7] == "0") $('#V4').bootstrapToggle('off');


		}
		if(message.payloadString.length==16 && message.destinationName == "/ESP/SMART"){

			if(message.payloadString[0] == "1") $('#timmerON1').bootstrapToggle('on');
			if(message.payloadString[1] == "1") $('#timmerON2').bootstrapToggle('on');
			if(message.payloadString[2] == "1") $('#timmerON3').bootstrapToggle('on');
			if(message.payloadString[3] == "1") $('#timmerON4').bootstrapToggle('on');

			if(message.payloadString[0] == "0") $('#timmerON1').bootstrapToggle('off');
			if(message.payloadString[1] == "0") $('#timmerON2').bootstrapToggle('off');
			if(message.payloadString[2] == "0") $('#timmerON3').bootstrapToggle('off');
			if(message.payloadString[3] == "0") $('#timmerON4').bootstrapToggle('off');

			if(message.payloadString[4] == "1") $('#timmerON5').bootstrapToggle('on');
			if(message.payloadString[5] == "1") $('#timmerON6').bootstrapToggle('on');
			if(message.payloadString[6] == "1") $('#timmerON7').bootstrapToggle('on');
			if(message.payloadString[7] == "1") $('#timmerON8').bootstrapToggle('on');

			if(message.payloadString[4] == "0") $('#timmerON5').bootstrapToggle('off');
			if(message.payloadString[5] == "0") $('#timmerON6').bootstrapToggle('off');
			if(message.payloadString[6] == "0") $('#timmerON7').bootstrapToggle('off');
			if(message.payloadString[7] == "0") $('#timmerON8').bootstrapToggle('off');

			if(message.payloadString[8] == "1") $('#timmerON9').bootstrapToggle('on');
			if(message.payloadString[9] == "1") $('#timmerON10').bootstrapToggle('on');
			if(message.payloadString[10] == "1") $('#timmerON11').bootstrapToggle('on');
			if(message.payloadString[11] == "1") $('#timmerON12').bootstrapToggle('on');

			if(message.payloadString[8] == "0") $('#timmerON9').bootstrapToggle('off');
			if(message.payloadString[9] == "0") $('#timmerON10').bootstrapToggle('off');
			if(message.payloadString[10] == "0") $('#timmerON11').bootstrapToggle('off');
			if(message.payloadString[11] == "0") $('#timmerON12').bootstrapToggle('off');


			if(message.payloadString[12] == "1") $('#timmerON13').bootstrapToggle('on');
			if(message.payloadString[13] == "1") $('#timmerON14').bootstrapToggle('on');
			if(message.payloadString[14] == "1") $('#timmerON15').bootstrapToggle('on');
			if(message.payloadString[15] == "1") $('#timmerON16').bootstrapToggle('on');

			if(message.payloadString[12] == "0") $('#timmerON13').bootstrapToggle('off');
			if(message.payloadString[13] == "0") $('#timmerON14').bootstrapToggle('off');
			if(message.payloadString[14] == "0") $('#timmerON15').bootstrapToggle('off');
			if(message.payloadString[15] == "0") $('#timmerON16').bootstrapToggle('off');

		}		
		if(message.payloadString == "LEDON"){
			if($('#onoff1').prop("checked") != true){
				$('#onoff1').bootstrapToggle('on');

			}
		}

		if(message.payloadString == "LEDOFF"){
			if($('#onoff1').prop("checked") == true){

				$('#onoff1').bootstrapToggle('off');
			}
		}

		if(message.payloadString == "LEDON1"){
			if($('#onoff2').prop("checked") != true){
				$('#onoff2').bootstrapToggle('on');

			}
		}

		if(message.payloadString == "LEDOFF1"){
			if($('#onoff2').prop("checked") == true){

				$('#onoff2').bootstrapToggle('off');
			}
		}

		if(message.payloadString == "LEDON2"){
			if($('#onoff3').prop("checked") != true){
				$('#onoff3').bootstrapToggle('on');

			}
		}

		if(message.payloadString == "LEDOFF2"){
			if($('#onoff3').prop("checked") == true){

				$('#onoff3').bootstrapToggle('off');
			}
		}

		if(message.payloadString == "LEDON3"){
			if($('#onoff4').prop("checked") != true){
				$('#onoff4').bootstrapToggle('on');

			}
		}

		if(message.payloadString == "LEDOFF3"){
			if($('#onoff4').prop("checked") == true){

				$('#onoff4').bootstrapToggle('off');
			}
		}
		if(message.destinationName =="/ESP/LED" && message.payloadString.length == 4){
			$('#onoff1').bootstrapToggle('enable');
			$('#onoff2').bootstrapToggle('enable');
			$('#onoff3').bootstrapToggle('enable');
			$('#onoff4').bootstrapToggle('enable');
			if(message.payloadString[0] == "1"){ 
				$('#onoff1').bootstrapToggle('on');
				$('#value1').text("วาล์ว 1 เปิด");	
				alert("วาล์ว 1 เปิด");
			}
			if(message.payloadString[1] == "1" && message.payloadString[0] !="T") {
				$('#onoff2').bootstrapToggle('on');
				$('#value2').text("วาล์ว 2 เปิด");
				alert("วาล์ว 2 เปิด");
			}
			if(message.payloadString[2] == "1"){
				$('#onoff3').bootstrapToggle('on');
				$('#value3').text("วาล์ว 3 เปิด");
				alert("วาล์ว 3 เปิด");
			}
			if(message.payloadString[3] == "1") {
				$('#onoff4').bootstrapToggle('on');
				$('#value3').text("วาล์ว 4 เปิด");
				alert("วาล์ว 4 เปิด");
			}

			if(message.payloadString[0] == "0") {
				$('#onoff1').bootstrapToggle('off');
				$('#value1').text("วาล์ว 1 ปิด");
			}
			if(message.payloadString[1] == "0") {
				$('#onoff2').bootstrapToggle('off');
				$('#value2').text("วาล์ว 2 ปิด");

			}
			if(message.payloadString[2] == "0") {
				$('#onoff3').bootstrapToggle('off');
				$('#value3').text("วาล์ว 3 ปิด");
			}
			if(message.payloadString[3] == "0") {
				$('#onoff4').bootstrapToggle('off');
				$('#value4').text("วาล์ว 4 ปิด");
			}
		}
		if(message.destinationName == "/ESP/temp" && message.payloadString !="Soil" && message.payloadString != "Closed"){
			var percent = message.payloadString[3];
			percent += message.payloadString[4]
			percent = parseInt(percent);
			g1.refresh(percent);
			console.log(percent);


			var rate = message.payloadString[0];
			rate += message.payloadString[1]
			rate += message.payloadString[2]
			rate = parseInt(rate);
			$("#ct").text(rate);
			if(rate>60){
				rate = 60;
			}
			rate=(rate *100)/60;

			rate = rate/100;

			bar.animate(rate);
		}
		if(message.destinationName == "/setdevice1" && message.payloadString.length == 128){

			timeset_old = message.payloadString;

			if(message.payloadString[0] != "9"){
				$("#showtime1-1").text("เปิด "+message.payloadString[0]+message.payloadString[1]+":"+message.payloadString[2]+message.payloadString[3]+" ปิด "+message.payloadString[4]+message.payloadString[5]+":"+message.payloadString[6]+message.payloadString[7]).removeClass().addClass("connected");
				//document.getElementById("cbox_device1on").checked = true;
			}
			if(message.payloadString[8] != "9"){
				$("#showtime1-2").text("เปิด "+message.payloadString[8]+message.payloadString[9]+":"+message.payloadString[10]+message.payloadString[11]+" ปิด "+message.payloadString[12]+message.payloadString[13]+":"+message.payloadString[14]+message.payloadString[15]).removeClass().addClass("connected");
				//document.getElementById("cbox_device2on").checked = true;
			}

			if(message.payloadString[0+16] != "9"){
				$("#showtime1-3").text("เปิด "+message.payloadString[0+16]+message.payloadString[1+16]+":"+message.payloadString[2+16]+message.payloadString[3+16]+" ปิด "+message.payloadString[4+16]+message.payloadString[5+16]+":"+message.payloadString[6+16]+message.payloadString[7+16]).removeClass().addClass("connected");
				//document.getElementById("cbox_device3on").checked = true;
			}
			if(message.payloadString[0+24] != "9"){
				$("#showtime1-4").text("เปิด "+message.payloadString[0+24]+message.payloadString[1+24]+":"+message.payloadString[2+24]+message.payloadString[3+24]+" ปิด "+message.payloadString[4+24]+message.payloadString[5+24]+":"+message.payloadString[6+24]+message.payloadString[7+24]).removeClass().addClass("connected");
				//document.getElementById("cbox_device4on").checked = true;
			}

			if(message.payloadString[0+32] != "9"){
				$("#showtime2-1").text("เปิด "+message.payloadString[0+32]+message.payloadString[1+32]+":"+message.payloadString[2+32]+message.payloadString[3+32]+" ปิด "+message.payloadString[4+32]+message.payloadString[5+32]+":"+message.payloadString[6+32]+message.payloadString[7+32]).removeClass().addClass("connected");
				//document.getElementById("cbox_device1on").checked = true;
			}
			if(message.payloadString[0+40] != "9"){
				$("#showtime2-2").text("เปิด "+message.payloadString[0+40]+message.payloadString[1+40]+":"+message.payloadString[2+40]+message.payloadString[3+40]+" ปิด "+message.payloadString[4+40]+message.payloadString[5+40]+":"+message.payloadString[6+40]+message.payloadString[7+40]).removeClass().addClass("connected");
				//document.getElementById("cbox_device2on").checked = true;
			}

			if(message.payloadString[0+48] != "9"){
				$("#showtime2-3").text("เปิด "+message.payloadString[0+48]+message.payloadString[1+48]+":"+message.payloadString[2+48]+message.payloadString[3+48]+" ปิด "+message.payloadString[4+48]+message.payloadString[5+48]+":"+message.payloadString[6+48]+message.payloadString[7+48]).removeClass().addClass("connected");
				//document.getElementById("cbox_device3on").checked = true;
			}

			if(message.payloadString[0+56] != "9"){
				$("#showtime2-4").text("เปิด "+message.payloadString[0+56]+message.payloadString[1+56]+":"+message.payloadString[2+56]+message.payloadString[3+56]+" ปิด "+message.payloadString[4+56]+message.payloadString[5+56]+":"+message.payloadString[6+56]+message.payloadString[7+56]).removeClass().addClass("connected");
				//document.getElementById("cbox_device4on").checked = true;

			}
			if(message.payloadString[0+64] != "9"){
				$("#showtime3-1").text("เปิด "+message.payloadString[0+64]+message.payloadString[1+64]+":"+message.payloadString[2+64]+message.payloadString[3+64]+" ปิด "+message.payloadString[4+64]+message.payloadString[5+64]+":"+message.payloadString[6+64]+message.payloadString[7+64]).removeClass().addClass("connected");
				//document.getElementById("cbox_device1on").checked = true;

			}
			if(message.payloadString[0+72] != "9"){
				$("#showtime3-2").text("เปิด "+message.payloadString[0+72]+message.payloadString[1+72]+":"+message.payloadString[2+72]+message.payloadString[3+72]+" ปิด "+message.payloadString[4+72]+message.payloadString[5+72]+":"+message.payloadString[6+72]+message.payloadString[7+72]).removeClass().addClass("connected");
				//document.getElementById("cbox_device2on").checked = true;

			}
			if(message.payloadString[0+80] != "9"){
				$("#showtime3-3").text("เปิด "+message.payloadString[0+80]+message.payloadString[1+80]+":"+message.payloadString[2+80]+message.payloadString[3+80]+" ปิด "+message.payloadString[4+80]+message.payloadString[5+80]+":"+message.payloadString[6+80]+message.payloadString[7+80]).removeClass().addClass("connected");
				//document.getElementById("cbox_device3on").checked = true;
			}
			if(message.payloadString[0+88] != "9"){
				$("#showtime3-4").text("เปิด "+message.payloadString[0+88]+message.payloadString[1+88]+":"+message.payloadString[2+88]+message.payloadString[3+88]+" ปิด "+message.payloadString[4+88]+message.payloadString[5+88]+":"+message.payloadString[6+88]+message.payloadString[7+88]).removeClass().addClass("connected");
				//document.getElementById("cbox_device4on").checked = true;
			}

			if(message.payloadString[0+96] != "9"){

				$("#showtime4-1").text("เปิด "+message.payloadString[0+96]+message.payloadString[1+96]+":"+message.payloadString[2+96]+message.payloadString[3+96]+" ปิด "+message.payloadString[4+96]+message.payloadString[5+96]+":"+message.payloadString[6+96]+message.payloadString[7+96]).removeClass().addClass("connected");
				//document.getElementById("cbox_device1on").checked = true;
			}

			if(message.payloadString[0+104] != "9"){
				$("#showtime4-2").text("เปิด "+message.payloadString[0+104]+message.payloadString[1+104]+":"+message.payloadString[2+104]+message.payloadString[3+104]+" ปิด "+message.payloadString[4+104]+message.payloadString[5+104]+":"+message.payloadString[6+104]+message.payloadString[7+104]).removeClass().addClass("connected");
				//document.getElementById("cbox_device2on").checked = true;

			}
			if(message.payloadString[0+112] != "9"){
				$("#showtime4-3").text("เปิด "+message.payloadString[0+112]+message.payloadString[1+112]+":"+message.payloadString[2+112]+message.payloadString[3+112]+" ปิด "+message.payloadString[4+112]+message.payloadString[5+112]+":"+message.payloadString[6+112]+message.payloadString[7+112]).removeClass().addClass("connected");
				//document.getElementById("cbox_device3on").checked = true;
			}
			if(message.payloadString[0+112] != "9"){
				$("#showtime4-4").text("เปิด "+message.payloadString[0+120]+message.payloadString[1+120]+":"+message.payloadString[2+120]+message.payloadString[3+120]+" ปิด "+message.payloadString[4+120]+message.payloadString[5+120]+":"+message.payloadString[6+120]+message.payloadString[7+120]).removeClass().addClass("connected");
				//document.getElementById("cbox_device4on").checked = true;
			}
		}



	}


	$("#set-time1").click(function(e){

		var val_No = document.getElementById("val_No");
		var slot_No = document.getElementById("slot_No");

		var val_No_txt = val_No.options[val_No.selectedIndex].text;
		var slot_No_txt = slot_No.options[slot_No.selectedIndex].text;



		var h_on = document.getElementById("sethouron1");
		var m_on = document.getElementById("setminon1");


		var h_off = document.getElementById("sethouroff1");
		var m_off = document.getElementById("setminoff1");

		var h_txt_on = h_on.options[h_on.selectedIndex].text;
		var m_txt_on = m_on.options[m_on.selectedIndex].text;

		var h_txt_off = h_off.options[h_off.selectedIndex].text;
		var m_txt_off = m_off.options[m_off.selectedIndex].text;

		var time_set = h_txt_on+m_txt_on+h_txt_off+m_txt_off;

		var dayweek = [0,0,0,0,0,0,0];

		if($("#allday").is(':checked')) dayweek= [1,1,1,1,1,1,1];
		if($("#mon").is(':checked')) 	dayweek[0]=1;
		if($("#tue").is(':checked')) 	dayweek[1]=1;
		if($("#wed").is(':checked')) 	dayweek[2]=1;
		if($("#thurs").is(':checked')) 	dayweek[3]=1;
		if($("#fri").is(':checked')) 	dayweek[4]=1;
		if($("#sat").is(':checked')) 	dayweek[5]=1;
		if($("#sun").is(':checked')) 	dayweek[6]=1;


		if(val_No_txt == "1"){


			mqttSend("/setdevice1",h_txt_on+m_txt_on+h_txt_off+m_txt_off+slot_No_txt+dayweek[0]+dayweek[1]+dayweek[2]+dayweek[3]+dayweek[4]+dayweek[5]+dayweek[6]);

			if(slot_No_txt == "1"){

				$("#showtime1-1").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "2"){

				$("#showtime1-2").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "3"){

				$("#showtime1-3").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "4"){

				$("#showtime1-4").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
		}

		if(val_No_txt == "2"){


			mqttSend("/setdevice2",h_txt_on+m_txt_on+h_txt_off+m_txt_off+slot_No_txt);
			if(slot_No_txt == "1"){

				$("#showtime2-1").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "2"){

				$("#showtime2-2").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "3"){

				$("#showtime2-3").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "4"){

				$("#showtime2-4").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}

		}

		if(val_No_txt == "3"){



			mqttSend("/setdevice3",h_txt_on+m_txt_on+h_txt_off+m_txt_off+slot_No_txt);
			if(slot_No_txt == "1"){

				$("#showtime3-1").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "2"){

				$("#showtime3-2").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "3"){

				$("#showtime3-3").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "4"){

				$("#showtime3-4").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
		}

		if(val_No_txt == "4"){


			mqttSend("/setdevice4",h_txt_on+m_txt_on+h_txt_off+m_txt_off+slot_No_txt);
			if(slot_No_txt == "1"){

				$("#showtime4-1").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "2"){

				$("#showtime4-2").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "3"){

				$("#showtime4-3").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
			if(slot_No_txt == "4"){

				$("#showtime4-4").text("เปิด "+h_txt_on+":"+m_txt_on+" ปิด "+h_txt_off+":"+m_txt_off);

			}
		}

		
	});




	$('#onoff1').change(function() {

		if(this.checked) {
			mqttSend("/ESP/LED", "LEDON");
			$('#value1').text("วาล์ว 1 เปิด");
			$('#onoff2').bootstrapToggle('disable');
			$('#onoff3').bootstrapToggle('disable');
			$('#onoff4').bootstrapToggle('disable');

		}
		else{
			mqttSend("/ESP/LED", "LEDOFF");
			$('#value1').text("วาล์ว 1 ปิด");
			$('#onoff2').bootstrapToggle('enable');
			$('#onoff3').bootstrapToggle('enable');
			$('#onoff4').bootstrapToggle('enable');
		}
	});

	$('#onoff2').change(function() {

		if(this.checked) {
			mqttSend("/ESP/LED", "LEDON1");
			$('#value2').text("วาล์ว 2 เปิด");
			$('#onoff1').bootstrapToggle('disable');
			$('#onoff3').bootstrapToggle('disable');
			$('#onoff4').bootstrapToggle('disable');
		}
		else{
			mqttSend("/ESP/LED", "LEDOFF1");
			$('#value2').text("วาล์ว 2 ปิด");
			$('#onoff1').bootstrapToggle('enable');
			$('#onoff3').bootstrapToggle('enable');
			$('#onoff4').bootstrapToggle('enable');
		}
	});

	$('#onoff3').change(function() {

		if(this.checked) {
			mqttSend("/ESP/LED", "LEDON2");
			$('#value3').text("วาล์ว 3 เปิด");
			$('#onoff1').bootstrapToggle('disable');
			$('#onoff2').bootstrapToggle('disable');
			$('#onoff4').bootstrapToggle('disable');
		}
		else{
			mqttSend("/ESP/LED", "LEDOFF2");
			$('#value3').text("วาล์ว 3 ปิด");
			$('#onoff2').bootstrapToggle('enable');
			$('#onoff1').bootstrapToggle('enable');
			$('#onoff4').bootstrapToggle('enable');
		}
	});

	$('#onoff4').change(function() {

		if(this.checked) {
			mqttSend("/ESP/LED", "LEDON3");
			$('#value4').text("วาล์ว 4 เปิด");
			$('#onoff1').bootstrapToggle('disable');
			$('#onoff3').bootstrapToggle('disable');
			$('#onoff2').bootstrapToggle('disable');
		}
		else{
			mqttSend("/ESP/LED", "LEDOFF3");
			$('#value4').text("วาล์ว 4 ปิด");
			$('#onoff2').bootstrapToggle('enable');
			$('#onoff3').bootstrapToggle('enable');
			$('#onoff1').bootstrapToggle('enable');
		}
	});




	$('#smart').change(function() {

		if(this.checked) {
			mqttSend("/ESP/SMART", "O");


		}
		else{
			mqttSend("/ESP/SMART", "F");
		}
	});




	$("#Smart_Save").click(function(e){
		var moi = document.getElementById("setmoisture");
		
		var moi_txt = moi.options[moi.selectedIndex].text;

		var v1="0";
		var v2="0";
		var v3="0";
		var v4="0";

		if($("#V1").is(':checked')) v1="1";
		if($("#V2").is(':checked')) v2="1";
		if($("#V3").is(':checked')) v3="1";
		if($("#V4").is(':checked')) v4="1";
		mqttSend("/ESP/SMART",moi_txt+"00"+v1+v2+v3+v4);

	});

	$("#timmerON1").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T1ON");

		}
		else {
			mqttSend("/ESP/LED","T1OFF");

		}
	});

	$("#timmerON2").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T2ON");

		}
		else {
			mqttSend("/ESP/LED","T2OFF");

		}
	});

	$("#timmerON3").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T3ON");

		}
		else {
			mqttSend("/ESP/LED","T3OFF");

		}
	});


	$("#timmerON4").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T4ON");

		}
		else {
			mqttSend("/ESP/LED","T4OFF");

		}
	});

	$("#timmerON5").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T5ON");

		}
		else {
			mqttSend("/ESP/LED","T5OFF");

		}
	});

	$("#timmerON6").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T6ON");

		}
		else {
			mqttSend("/ESP/LED","T6OFF");

		}
	});
	$("#timmerON7").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T7ON");

		}
		else {
			mqttSend("/ESP/LED","T7OFF");

		}
	});

	$("#timmerON8").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T8ON");

		}
		else {
			mqttSend("/ESP/LED","T8OFF");

		}
	});

	$("#timmerON9").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T9ON");

		}
		else {
			mqttSend("/ESP/LED","T9OFF");

		}
	});

	$("#timmerON10").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T10ON");

		}
		else {
			mqttSend("/ESP/LED","T10OFF");

		}
	});
	$("#timmerON11").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T11ON");

		}
		else {
			mqttSend("/ESP/LED","T11OFF");

		}
	});



	$("#timmerON12").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T12ON");

		}
		else {
			mqttSend("/ESP/LED","T12OFF");

		}
	});	

	$("#timmerON13").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T13ON");

		}
		else {
			mqttSend("/ESP/LED","T13OFF");

		}
	});	



	$("#timmerON14").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T14ON");

		}
		else {
			mqttSend("/ESP/LED","T14OFF");

		}
	});	

	$("#timmerON15").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T15ON");

		}
		else {
			mqttSend("/ESP/LED","T15OFF");

		}
	});	
	$("#timmerON16").change(function(){

		if(this.checked){

			mqttSend("/ESP/LED","T16ON");

		}
		else {
			mqttSend("/ESP/LED","T16OFF");

		}
	});	

	$("#allday").change(function(){

		if(this.checked){

			$('#mon').bootstrapToggle('disable');
			$('#tue').bootstrapToggle('disable');
			$('#wed').bootstrapToggle('disable');
			$('#thurs').bootstrapToggle('disable');
			$('#fri').bootstrapToggle('disable');
			$('#sat').bootstrapToggle('disable');
			$('#sun').bootstrapToggle('disable');

		}
		else {
			$('#mon').bootstrapToggle('enable');
			$('#tue').bootstrapToggle('enable');
			$('#wed').bootstrapToggle('enable');
			$('#thurs').bootstrapToggle('enable');
			$('#fri').bootstrapToggle('enable');
			$('#sat').bootstrapToggle('enable');
			$('#sun').bootstrapToggle('enable');

		}
	});
/*
		$('.modal').on('show.bs.modal', function (e) {
			mqttSend("/ESP/SMART", "SMART");
			
		})

		*/
		$(window).on("unload", function(e) {
			mqttSend("/ESP/LED", "Closed");
		});


	});

var mqttSend = function(topic, msg) {
	var message = new Paho.MQTT.Message(msg);
	message.destinationName = topic;
	client.send(message); 
}
function Home(){

	mqttSend("/ESP/LED","Soil");
}

function V1(){
	mqttSend("/ESP/LED", "GET1");
	mqttSend("/setdevice1", "GET-TIME");
	mqttSend("/ESP/LED", "Closed");
}
function V2(){

	mqttSend("/ESP/LED", "GET2");
	mqttSend("/setdevice2", "GET-TIME");
	mqttSend("/ESP/LED", "Closed");
}
function V3(){
	mqttSend("/ESP/LED", "GET3");
	mqttSend("/setdevice3", "GET-TIME");
	mqttSend("/ESP/LED", "Closed");
}
function V4(){
	mqttSend("/ESP/LED", "GET4");
	mqttSend("/setdevice4", "GET-TIME");
	mqttSend("/ESP/LED", "Closed");
}

function Smart(){
	mqttSend("/ESP/LED", "SMART");
	mqttSend("/ESP/LED", "Closed");
}

function display_c(){
var refresh=1000; // Refresh rate in milli seconds
mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
	var strcount
	var x = new Date()
	var x1; 
	x1 =x.getHours( )+ ":" + x.getMinutes() + ":" + x.getSeconds();
	document.getElementById('ct').innerHTML = x1;

	tt=display_c();

	document.getElementById('ct').style.fontSize='30px';
	
}

</script>
<script>
	var chart;
	var day = 0;
	var chartData = [];

	AmCharts.ready(function () {

                // first we generate some random data
                generateChartData();

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();

                chart.dataProvider = chartData;
                chart.categoryField = "date";
                chart.dataDateFormat = "DD-MM-YYYY, JJ:NN:SS";
                // data updated event will be fired when chart is first displayed,
                // also when data will be updated. We'll use it to set some
                // initial zoom
                chart.addListener("dataUpdated", zoomChart);

                // AXES
                // Category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // in order char to understand dates, we should set parseDates to true
                categoryAxis.minPeriod = "mm"; // as we have data with minute interval, we have to set "mm" here.
                categoryAxis.gridAlpha = 0.07;
                categoryAxis.axisColor = "#DADADA";

                // Value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.gridAlpha = 0.07;
                valueAxis.title = "ความชื้น";
                chart.addValueAxis(valueAxis);

                // GRAPH
                var graph = new AmCharts.AmGraph();
                graph.type = "line"; // try to change it to "column"
                graph.title = "red line";
                graph.valueField = "visits";
                graph.lineAlpha = 1;
                graph.lineColor = "#d1cf2a";
                graph.fillAlphas = 0.3; // setting fillAlphas to > 0 value makes it area graph
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chartCursor.categoryBalloonDateFormat = "JJ:NN, DD MMMM YYYY";
                chart.addChartCursor(chartCursor);

                // SCROLLBAR
                var chartScrollbar = new AmCharts.ChartScrollbar();

                chart.addChartScrollbar(chartScrollbar);

                // WRITE
                chart.write("chartdiv");
            });

            // generate some random data, quite different range
            function generateChartData() {

            	$.ajax({

            		url:'select.php',
            		type:'get',
            		dataType:'json'

            	})

            	.done(function(s){

            		$.each(s,function(index,el){

            			val = parseInt(el.moisture);  
                     //var newDate = new Date(2013,08,01,15,30);
                     var st1 = el.dt;
                     var st2 = el.t;
                     var st3 = st1+", "+st2;
                     chartData.push({
                     	date: st3,
                     	visits: val,
                     }); 
                     chart.validateData();   
                     
                 });



            	});

            }

            // this method is called when chart is first inited as we listen for "dataUpdated" event
            function zoomChart() {
                // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
                chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
            }

        </script>
    </head>
    <body style="background:url('images/space.jpg') center center;">
    	<div id="console-event"></div>
    	<!-- NAVIGATION MENU -->

    	<div class="navbar-nav navbar-inverse navbar-fixed-top">
    		<div class="container">
    			<div class="navbar-header">
    				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    					<span class="icon-bar"></span>
    					<span class="icon-bar"></span>
    					<span class="icon-bar"></span>
    				</button>
    				<a class="navbar-brand" href="admin.php"><alt=""> WEB CONTROL SOLINOID </a>
    			</div> 
    			<div class="navbar-collapse collapse">
    				<ul class="nav navbar-nav">
    					<li class="active"><a href="admin.php"><i class="icon-home icon-white"></i> หน้าแรก</a></li>   
    					<li class="active"><a data-toggle="modal" href="#myModal"><i class="icon-home icon-white"></i> ตั้งค่าโหมดอัจฉริยะ</a></li>
    					<li class="active"><a data-toggle="modal" href="#myModal1"><i class="icon-home icon-white"></i> ตั้งเวลา</a></li>       
    					<li class="active"><a href="logout.php"><i class="icon-home icon-white"></i> ออกจากระบบ</a></li>                           
    				</ul>
    			</div><!--/.nav-collapse -->
    		</div>
    	</div>

    	<div class="container">

    		<!-- FIRST ROW OF BLOCKS -->     
    		<div class="row">

    			<!-- USER PROFILE BLOCK -->
    			<div class="col-sm-3 col-lg-3">
    				<div class="half-unit">
    					<dtitle><b>สถานะ</b></dtitle>
    					<hr>
    					<div class="clockcenter">
    						<digiclock id="status" class="connect">กำลังเชื่อมต่อ</digiclock>
    					</div>
    				</div>
    				<div class="half-unit">
    					<dtitle><b>อัตตราการไหล</b></dtitle>
    					<hr>
    					<div class="clockcenter">
    						<div id="bb" class="clockcenter"></div>
    						<digiclock id="ct">0</digiclock><digiclock id="ct1"> ลิตร/นาที</digiclock>
    					</div>
    				</div>
    			</div>

    			<!-- DONUT CHART BLOCK -->
    			<div class="col-sm-3 col-lg-3">
    				<div class="dash-unit">
    					<dtitle><b>สถานะแต่ละวาล์ว</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_settings  fs2"></span>
    					</div>
    					<div id="status-text" class="text clockcenter" >
    						<p id="value1">วาล์ว 1 OFFLINE</p>
    						<p id="value2">วาล์ว 2 OFFLINE</p>
    						<p id="value3">วาล์ว 3 OFFLINE</p>
    						<p id="value4">วาล์ว 4 OFFLINE</p>
    					</div>
    				</div>
    			</div>

    			<!-- DONUT CHART BLOCK -->
    			<div class="col-sm-3 col-lg-3">
    				<div class="dash-unit">
    					<dtitle><b>ความชื้นในดิน</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_lab fs2"></span>
    					</div>
    					<div class="clockcenter">
    						<div id="g1"></div>
    						<!--	<input id="mois" data-onstyle="info" data-toggle="toggle" data-on="ความชื้น 1" data-off="ความชื้น 2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-size="small"> -->
    					</div>

    				</div>
    			</div>

    			<div class="col-sm-3 col-lg-3">
    				<!-- LOCAL TIME BLOCK -->
    				<div class="dash-unit"  >
    					<dtitle><b>ควบคุมการทำงาน</b></dtitle>
    					<hr>
    					<div class="clockcenter">
    						<div class="info-user">
    							<span aria-hidden="true" class="li_params fs2"></span>
    						</div>
    						<span> วาล์ว 1</span> <input id="onoff1" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-offstyle="danger" data-size="small"> 
    						<br></br>
    						<span> วาล์ว 2</span> <input id="onoff2" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-offstyle="danger" data-size="small"> 
    						<br></br>
    						<span> วาล์ว 3</span> <input id="onoff3" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-offstyle="danger" data-size="small"> 
    						<br></br> 
    						<span> วาล์ว 4</span> <input id="onoff4" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-offstyle="danger" data-size="small">  
    						<br></br>
    					</div>
    				</div><!-- /dash-unit -->
    			</div>
    		</div><!-- /row -->


    		<!-- SECOND ROW OF BLOCKS -->     
    		<div class="row">
    			<div class="col-sm-3 col-lg-3">
    				<!-- MAIL BLOCK -->
    				<div class="dash-unit">
    					<dtitle><b>เวลาที่ตั้งไว้ วาล์ว 1</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_clock fs2"></span>
    					</div>
    					<div class="clockcenter">
    						<span id="showtime1-1">ยังไม่ตั้งเวลา</span><span id="week1-1"></span> <br> <input id="timmerON1" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime1-2">ยังไม่ตั้งเวลา</span><span id="week1-2"></span> <br> <input id="timmerON2" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime1-3">ยังไม่ตั้งเวลา</span><span id="week1-3"></span> <br> <input id="timmerON3" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime1-4">ยังไม่ตั้งเวลา</span><span id="week1-4"></span> <br> <input id="timmerON4" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger">
    					</div> 					
    				</div><!-- /dash-unit -->
    			</div><!-- /span3 -->

    			<!-- GRAPH CHART - lineandbars.js file -->     
    			<div class="col-sm-3 col-lg-3">
    				<div class="dash-unit">
    					<dtitle><b>เวลาที่ตั้งไว้ วาล์ว 2</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_clock fs2"></span>
    					</div>
    					<div class="clockcenter">
    						<span id="showtime2-1">ยังไม่ตั้งเวลา</span><span id="week2-1"></span> <br> <input id="timmerON5" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime2-2">ยังไม่ตั้งเวลา</span><span id="week2-2"></span><br> <input id="timmerON6" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime2-3">ยังไม่ตั้งเวลา</span><span id="week2-3"></span> <br> <input id="timmerON7" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime2-4">ยังไม่ตั้งเวลา</span><span id="week2-4"></span> <br> <input id="timmerON8" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger">
    					</div> 
    				</div>
    			</div>

    			<!-- LAST MONTH REVENUE -->     
    			<div class="col-sm-3 col-lg-3">
    				<div class="dash-unit">
    					<dtitle><b>เวลาที่ตั้งไว้ วาล์ว 3</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_clock fs2"></span>
    					</div>
    					<div class="clockcenter">
    						<span id="showtime3-1">ยังไม่ตั้งเวลา</span><span id="week3-1"></span><br> <input id="timmerON9" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime3-2">ยังไม่ตั้งเวลา</span><span id="week3-2"></span> <br> <input id="timmerON10" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime3-3">ยังไม่ตั้งเวลา</span><span id="week3-3"></span> <br> <input id="timmerON11" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime3-4">ยังไม่ตั้งเวลา</span><span id="week3-4"></span> <br> <input id="timmerON12" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger">
    					</div> 

    				</div>
    			</div>

    			<!-- 30 DAYS STATS - CAROUSEL FLEXSLIDER -->     
    			<div class="col-sm-3 col-lg-3">
    				<div class="dash-unit">
    					<dtitle><b>เวลาที่ตั้งไว้ วาล์ว 4</b></dtitle>
    					<hr>
    					<div class="info-user">
    						<span aria-hidden="true" class="li_clock fs2"></span>
    					</div>
    					<div class="clockcenter">
    						<span id="showtime4-1">ยังไม่ตั้งเวลา</span><span id="week4-1"></span> <br> <input id="timmerON13" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime4-2">ยังไม่ตั้งเวลา</span><span id="week4-2"></span> <br> <input id="timmerON14" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime4-3">ยังไม่ตั้งเวลา</span><span id="week4-3"></span> <br> <input id="timmerON15" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger"><br>
    						<span id="showtime4-4">ยังไม่ตั้งเวลา</span><span id="week4-4"></span> <br> <input id="timmerON16" data-toggle="toggle" type="checkbox" data-size="mini" data-on="ใช้งาน" data-off="ไม่ใช้งาน" data-onstyle="success" data-offstyle="danger">

    						<!--modal 1 -->
    						<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    							<div class="modal-dialog">
    								<div class="modal-content">
    									<div class="modal-header">
    										<button id="smart-active" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    										<h4 class="modal-title">ตั้งค่าโหมดอัจฉริยะ</h4>
    									</div>
    									<div class="modal-body">
    										<span> โหมดอัจฉริยะ</span> <input id="smart" data-onstyle="info" data-toggle="toggle" data-on="ทำงาน" data-off="ไม่ทำงาน" type="checkbox" data-onstyle="success" data-offstyle="danger" data-width="100"> 
    										<br>
    										<br>
    										<span id="s_moiture"> ความชื้น OFFLINE</span><br>
    										<span>ทำงานเมื่อความชื้นต่ำกว่า</span> 
    										<select id="setmoisture"  class="selectpicker col-2">
    											<option value="00">00</option>
    											<option value="10">10</option>
    											<option value="20">20</option>
    											<option value="30">30</option>
    											<option value="40">40</option>
    											<option value="50">50</option>
    											<option value="60">60</option>
    											<option value="70">70</option>
    											<option value="80">80</option>
    											<option value="90">90</option>
    											<option value="100">100</option>
    										</select>

    										<br></br>
    										<span> วาว์ล 1</span> <input id="V1" data-onstyle="info" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox"> 

    										<span> วาว์ล 2</span> <input id="V2" data-onstyle="info" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox"> 
    										<br></br>
    										<span> วาว์ล 3</span> <input id="V3" data-onstyle="info" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox"> 

    										<span> วาว์ล 4</span> <input id="V4" data-onstyle="info" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox"> 
    										<br></br>
    										<!--button id="Smart_Save" class="btn btn-default">บันทึก</button-->
    									</div>
    									<div class="modal-footer">
    										<button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
    										<button id="Smart_Save" type="submit" class="btn btn-primary" data-dismiss="modal">บันทึกค่า</button>
    									</div>
    								</div><!-- /.modal-content -->
    							</div><!-- /.modal-dialog -->
    						</div><!-- /.modal -->

    						<!--modal 1 -->
    						<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    							<div class="modal-dialog">
    								<div class="modal-content">
    									<div class="modal-header">
    										<button id="smart-active" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    										<h4 class="modal-title">ตั้งเวลาเปิด ปิด</h4>
    									</div>
    									<div class="modal-body">
    										<span>วาล์ว</span> 
    										<select id="val_No" class="selectpicker col-2">
    											<option>1</option>
    											<option>2</option>
    											<option>3</option>
    											<option>4</option>
    										</select>
    										<span>ช่อง</span> 
    										<select id="slot_No" class="selectpicker col-2">
    											<option>1</option>
    											<option>2</option>
    											<option>3</option>
    											<option>4</option>
    										</select>
    										<br>
    										<span>ตั้งเวลาเปิด</span><br>
    										<select id="sethouron1" class="selectpicker col-2">
    											<option value="00">00</option>
    											<option value="01">01</option>
    											<option value="02">02</option>
    											<option value="03">03</option>
    											<option value="04">04</option>
    											<option value="05">05</option>
    											<option value="06">06</option>
    											<option value="07">07</option>
    											<option value="08">08</option>
    											<option value="09">09</option>
    											<option value="10">10</option>
    											<option value="11">11</option>
    											<option value="12">12</option>
    											<option value="13">13</option>
    											<option value="14">14</option>
    											<option value="15">15</option>
    											<option value="16">16</option>
    											<option value="17">17</option>
    											<option value="18">18</option>
    											<option value="19">19</option>
    											<option value="20">20</option>
    											<option value="21">21</option>
    											<option value="22">22</option>
    											<option value="23">23</option>
    										</select> 
    										&nbsp;
    										<select id="setminon1" class="selectpicker col-2">
    											<option value="00">00</option>
    											<option value="01">01</option>
    											<option value="02">02</option>
    											<option value="03">03</option>
    											<option value="04">04</option>
    											<option value="05">05</option>
    											<option value="06">06</option>
    											<option value="07">07</option>
    											<option value="08">08</option>
    											<option value="09">09</option>
    											<option value="10">10</option>
    											<option value="11">11</option>
    											<option value="12">12</option>
    											<option value="13">13</option>
    											<option value="14">14</option>
    											<option value="15">15</option>
    											<option value="16">16</option>
    											<option value="17">17</option>
    											<option value="18">18</option>
    											<option value="19">19</option>
    											<option value="20">20</option>
    											<option value="21">21</option>
    											<option value="22">22</option>
    											<option value="23">23</option>
    											<option value="24">24</option>
    											<option value="25">25</option>
    											<option value="26">26</option>
    											<option value="27">27</option>
    											<option value="28">28</option>
    											<option value="29">29</option>
    											<option value="30">30</option>
    											<option value="31">31</option>
    											<option value="32">32</option>
    											<option value="33">33</option>
    											<option value="34">34</option>
    											<option value="35">35</option>
    											<option value="36">36</option>
    											<option value="37">37</option>
    											<option value="38">38</option>
    											<option value="39">39</option>
    											<option value="40">40</option>
    											<option value="41">41</option>
    											<option value="42">42</option>
    											<option value="43">43</option>
    											<option value="44">44</option>
    											<option value="45">45</option>
    											<option value="46">46</option>
    											<option value="47">47</option>
    											<option value="48">48</option>
    											<option value="49">49</option>
    											<option value="50">50</option>
    											<option value="51">51</option>
    											<option value="52">52</option>
    											<option value="53">53</option>
    											<option value="54">54</option>
    											<option value="55">55</option>
    											<option value="56">56</option>
    											<option value="57">57</option>
    											<option value="58">58</option>
    											<option value="59">59</option>
    										</select>
    										<br>
    										<!--<span><input type="checkbox" id="cbox_device1off" value="device1_off"> ตั้งเวลา</span>-->
    										<span>ตั้งเวลาปิด</span><br>
    										<select id="sethouroff1"  class="selectpicker col-2">
    											<option value="00">00</option>
    											<option value="01">01</option>
    											<option value="02">02</option>
    											<option value="03">03</option>
    											<option value="04">04</option>
    											<option value="05">05</option>
    											<option value="06">06</option>
    											<option value="07">07</option>
    											<option value="08">08</option>
    											<option value="09">09</option>
    											<option value="10">10</option>
    											<option value="11">11</option>
    											<option value="12">12</option>
    											<option value="13">13</option>
    											<option value="14">14</option>
    											<option value="15">15</option>
    											<option value="16">16</option>
    											<option value="17">17</option>
    											<option value="18">18</option>
    											<option value="19">19</option>
    											<option value="20">20</option>
    											<option value="21">21</option>
    											<option value="22">22</option>
    											<option value="23">23</option>
    										</select> 
    										&nbsp;
    										<select id="setminoff1" class="selectpicker col-2">
    											<option value="00">00</option>
    											<option value="01">01</option>
    											<option value="02">02</option>
    											<option value="03">03</option>
    											<option value="04">04</option>
    											<option value="05">05</option>
    											<option value="06">06</option>
    											<option value="07">07</option>
    											<option value="08">08</option>
    											<option value="09">09</option>
    											<option value="10">10</option>
    											<option value="11">11</option>
    											<option value="12">12</option>
    											<option value="13">13</option>
    											<option value="14">14</option>
    											<option value="15">15</option>
    											<option value="16">16</option>
    											<option value="17">17</option>
    											<option value="18">18</option>
    											<option value="19">19</option>
    											<option value="20">20</option>
    											<option value="21">21</option>
    											<option value="22">22</option>
    											<option value="23">23</option>
    											<option value="24">24</option>
    											<option value="25">25</option>
    											<option value="26">26</option>
    											<option value="27">27</option>
    											<option value="28">28</option>
    											<option value="29">29</option>
    											<option value="30">30</option>
    											<option value="31">31</option>
    											<option value="32">32</option>
    											<option value="33">33</option>
    											<option value="34">34</option>
    											<option value="35">35</option>
    											<option value="36">36</option>
    											<option value="37">37</option>
    											<option value="38">38</option>
    											<option value="39">39</option>
    											<option value="40">40</option>
    											<option value="41">41</option>
    											<option value="42">42</option>
    											<option value="43">43</option>
    											<option value="44">44</option>
    											<option value="45">45</option>
    											<option value="46">46</option>
    											<option value="47">47</option>
    											<option value="48">48</option>
    											<option value="49">49</option>
    											<option value="50">50</option>
    											<option value="51">51</option>
    											<option value="52">52</option>
    											<option value="53">53</option>
    											<option value="54">54</option>
    											<option value="55">55</option>
    											<option value="56">56</option>
    											<option value="57">57</option>
    											<option value="58">58</option>
    											<option value="59">59</option>
    										</select>

    										<br></br>


<!--
    										<span>ทุกวัน</span><br> <input id="allday" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success"  data-size="small"> 
    										<br>
    										<span>จันทร์</span><br> <input id="mon" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success"  data-size="small"> 
    										<br>
    										<span>อังคาร</span><br> <input id="tue" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success"  data-size="small"> 
    										<br>
    										<span>พุธ</span> <br><input id="wed" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-size="small"> 
    										<br>
    										<span>พฤหัสบดี</span><br> <input id="thurs" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-size="small"> 
    										<br>
    										<span>ศุกร์</span><br> <input id="fri" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-size="small"> 
    										<br>
    										<span>เสาร์</span> <br><input id="sat" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-size="small"> 
    										<br>
    										<span>อาทิตย์</span><br> <input id="sun" data-onstyle="info" data-toggle="toggle" data-on="เปิด" data-off="ปิด" type="checkbox" data-onstyle="success" data-size="small"> 
    										<br>-->
    									</div>
    									<div class="modal-footer">
    										<button id="set-time1" type="submit" class="btn btn-primary btn-sm" data-dismiss="modal">ตั้งเวลา</button>
    									</div>
    								</div><!-- /.modal-content -->
    							</div><!-- /.modal-dialog -->
    						</div><!-- /.modal -->

    					</div> 
    				</div>


    			</div><!-- /row -->

    		</div><!-- /container -->	

    		<div class="container">
    			<div class="row">


    				<div class=".col-xs-12 .col-sm-6 .col-md-8">
    					<div id="register-wraper">

    						<legend>บันทึกความชื้นล่าสุด 24 ชั่วโมง</legend>

    						<div class="body">
    							<div id="chartdiv" style="width:100%; height:400px;"></div>

    						</div>


    					</div>
    				</div>

    			</div>
    		</div>

    	</div><!-- /footerwrap -->

    </body></html>
    <style type="text/css">


    	#status {
    		background: #333;
    		color: #FFF;
    		border-radius: 3px;
    		font-weight: bold;
    		padding: 3px 6px;
    	}
    	#status.connect {
    		background: #E18C1A;
    		color: #FFF;
    	}
    	#status.connected {
    		background: #00AE04;
    		color: #FFF;
    	}
    	#status.error {
    		background: #F00;
    		color: #FFF;
    	}
    </style>

