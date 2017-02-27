<?php
/**
WTD fvck inc
**/
?>
<!-- 
WTD fvck inc
-->
<style>
p {
    border: 1px solid;
    padding: 15px;
    max-width: 200px;
    text-align: center;
    cursor: pointer;
}
.turny {
	color: #27e833;
}
.turnw {
	color: #943434;
}
.list-group a {
    padding: 30px;
	text-align: center;
}
.list-group input {
    padding: 30px;
}
.list-group {
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.075);
    box-shadow: 0 1px 2px rgba(0,0,0,.075);
    max-width: 350px;
    margin: 0 auto;
}
.p {
	font-weight: bold;
	color: red;
}
div#status {
    text-align: center;
    margin: 20px;
}
div.overlay {
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 999;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    display: none;
}
.not-active {
   pointer-events: none;
   cursor: default;
}
</style>
<!-- Latest compiled and minified CSS 
<link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="http://10.6.40.125/css/system.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Optional theme 
<link rel="stylesheet" href="css/bootstrap/css/bootstrap-theme.min.css">
-->

<!-- Latest compiled and minified JavaScript 
<script src="css/bootstrap/js/bootstrap.min.js"></script> -->
<script src="js/jquery.min.js"></script>
<script>
curText = jQuery(".randNum .num1").text();

function generateSuite(exceptsuite) {
	random = Math.floor((Math.random() * 4) + 1);
	switch (random) {
		case 1:
			suite = "diamond";
			break;
		case 2:
			suite = "heart";
			break;
		case 3:
			suite = "spade";
			break;
		case 4:
			suite = "club";
			break;
	}	
	if (exceptsuite) {
		if (exceptsuite == suite) {
			generateSuite(exceptsuite);
		}
	}
	
	return suite;
}
</script>
<script>
var conn;
function load() {
	conn = new WebSocket('ws://192.168.36.247:8080');
	conn.onopen = function(e) {
		$("#input").text("BET!");
		$("#status").html(e.data);	

		
		p = jQuery(".p");			
		jQuery(".input-group").append(p);
		jQuery(".pat_holder").remove();
		jQuery(".input-group").append('<img class="pat_holder" src="http://10.6.40.125/css/images/pot-icon.png">');		
	};

	conn.onmessage = function(e) {
		//console.log(e.data);
		$("#status").html(e.data);
		
		//POT
		p = jQuery(".p");			
		jQuery(".input-group").append(p);
		jQuery(".pat_holder").remove();
		jQuery(".input-group").append('<img class="pat_holder" src="http://10.6.40.125/css/images/pot-icon.png">');
		//END OF POT
		
		//SUITE
		curNewText = jQuery(".randNum .num1").text();
	
		suite1 = generateSuite();
		suite2 = generateSuite(suite1);
		suite3 = generateSuite(suite2);
		if (!jQuery(".randSuite").length) {		
			jQuery(".randNum .num1").append("<img class='randSuite' src='http://10.6.40.125/css/images/" + suite1 + ".png'/>");
			//jQuery(".randNum .num2").append("<img class='randSuite' src='http://10.6.40.125/css/images/" + suite2 + ".png'/>");
			jQuery(".randNum .num3").append("<img class='randSuite' src='http://10.6.40.125/css/images/" + suite3 + ".png'/>");			
			p = jQuery(".p");			
			jQuery(".input-group").append(p);
			jQuery(".pat_holder").remove();
			jQuery(".input-group").append('<img class="pat_holder" src="http://10.6.40.125/css/images/pot-icon.png">');	
		}
		//END OF SUITE
		
		//CLIENT
		if (!jQuery(".user-container .y").length) {
			y = jQuery(".y");
			jQuery(".user-container").append(y);
		}					
		jQuery("#status .y").remove();
		//END OF CLIENT			
			
		//RANDNUM CLIENTSTATUS		
			youStatus = jQuery(".clientIndexNum").text();
			clientIncome = jQuery(".index:contains(" + youStatus + ")").siblings(".clientIncome").text();
			clientExpense = jQuery(".index:contains(" + youStatus + ")").siblings(".clientExpense").text();
			jQuery("<div class='clientStatus'><span class='income'>" + clientIncome + "</span> <span class='expense'>" + clientExpense + "</span></div>").insertAfter(".randNum");
			jQuery(".status .stat_label").text("");	
		//END OF RANDNUM
		
		//GAME OVER
		if (jQuery(".game-over").length) {
			alert("GAME OVER");		
			jQuery(".game-over").remove();
			$("a#k").removeClass("not-active");
			$("a#cancel").addClass("not-active");
			$("a#dl").addClass("not-active");
		}
		//END OF GAME OVER
		
		//DEAL IF STATUS IS VISIBLE		
		if (jQuery(".randNum .status").text() != "") {
			$("a#dl").removeClass("not-active");
			$("a#input").addClass("not-active");
			$("a#cancel").addClass("not-active");
		}
		else {
			$("a#dl").addClass("not-active");			
			$("a#input").removeClass("not-active");
			$("a#cancel").removeClass("not-active");
			//$("a#input").addClass("not-active");
		}
		//
		
		//OVERLAY WAITING
		if (jQuery(".turny").length) { 
			jQuery(".overlay").hide();
		} 
		else { 
			jQuery(".overlay").show();
		}
		//END OF OVERLAY
	
	};	
	console.log(conn);
}

window.onload = load;

setTimeout(function(){
	$("a#input").click(function(){
		console.log($("#input-text").val());
		conn.send($("#input-text").val());		
	});
	$("a#dl").click(function(){
		console.log($(this).attr("attr"));
		conn.send($(this).attr("attr"));
	});
	$("a#k").click(function(){
		console.log($(this).attr("attr"));
		var answer = confirm("Are you sure?");
		if (answer) {
			conn.send($(this).attr("attr"));
			if ($("a#k").attr("attr") == "K") {
				$("a#k").text("Restart").attr("attr","R");
				$("a#k").addClass("not-active");
			}
			else {
				$("a#k").text("Confirm").attr("attr","K");
				$("a#k").removeClass("not-active");
			}
		}
	});
	$("a#cancel").click(function(){
		console.log($(this).attr("attr"));
		conn.send($(this).attr("attr"));
	});
	$(document).bind('keydown keyup', function(e) {
		console.log(e.which);
		if(e.which === 116) {
		   console.log('blocked');
		   return false;
		}
		if(e.which === 82 && e.ctrlKey) {
		   console.log('blocked');
		   return false;
		}
		if(e.ctrlKey && e.which === 68) {
		   $("a#dl").click();
		}
		if(e.ctrlKey && e.which === 66) {
		   $("a#input").click();
		}
		if(e.ctrlKey && e.which === 67) {
		   $("a#cancel").click();
		}
	});
}, 100);


</script>
<body>
<!--
<input type="text" id="input-text"/>
<p id="input"></p>
<p id="dl">d</p>
<p id="k">K</p>
<p id="cancel">P</p>
<p id="status"></p>
-->
<div class="overlay"></div>
<div class="header"><img src="http://10.6.40.125/css/images/header.png"></div>
	<div class="list-group">
		<div class="input-group">
			<div class="user-container">
				
			</div>
			<label> PLACE YOUR BET: </label>
			<input type="text" id="input-text" class="form-control" placeholder="PHP" maxlength="8" aria-describedby="sizing-addon1">
			<a href="#" class="list-group-item" id="input"></a>
			<a href="#" class="list-group-item not-active" id="dl" attr="D">DEAL</a>
			<a href="#" class="list-group-item" id="cancel" attr="P">PASS</a>
			<a href="#" class="list-group-item" id="k" attr="K">CONFIRM</a>
		</div>
		<div id="status">
		</div>
	</div>
</body>