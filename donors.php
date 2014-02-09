<!DOCTYPE html>
<html>
<head>
	<title>Indiegogo contributor scroller.</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<meta charset="UTF-8">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="smoothmarquee.js"></script>
	<style type="text/css">
		@font-face {
  		font-family: Modata;
  		src: url(MgOpenModataBold.ttf);
  		font-weight: bold;
		}

		@font-face {
  		font-family: Moderna;
  		src: url(MgOpenModernaRegular.ttf);
  		font-weight:regular;
		}

		.marquee {
			color:<? $color = (isset($_REQUEST['color']))?$_REQUEST['color'] : "black"; echo $color.";\n"; ?>
			font-weight: bold;
			font-family:<? $font = (isset($_REQUEST['font']))?$_REQUEST['font'] : "Modata"; echo $font.";\n"; ?>
		}

		h1 {
			font-family: Modata;
			color: black;
		}

		#main {
			color:#484848;
			font-family: Moderna;
		}

		.indent {
			margin-left: 3%;
		}

		#example {
			font-family: Courier, monospace;
			color: Gray;
		}

		#footer {
			position:absolute;
			bottom:0;
			left:0;
			width:100%;
			height: 20px;
			background:AliceBlue;
			font-family:Moderna;
			font-size:12px;
			color:DimGray;
		}

		#credits {
			float:right;
			margin-right:3px;
			margin-top:3px;
		}

		#credits a {
			color:DeepSkyBlue;
		}
	</style>
</head>
<body>
<?php
if(isset($_REQUEST['fundraiser']) && isset($_REQUEST['count'])){
	$fundraiser = $_REQUEST['fundraiser'];
	$count = $_REQUEST['count'];
	$projecturl = "http://www.indiegogo.com/project/partial/".$fundraiser."?count=".$count."&amp;partial_name=activity_pledges";
	$handle = @fopen($projecturl, "r");
}
else{ echo "
	<h1>Error :)</h1>
	<div id='main'>
		You have not entered an Indiegogo project <em>'fundraiser'</em> value or a maximum pledges <em>'count'</em> value in your request.<br /><br />

		Other options include:<br />
		<div class='indent'>
			<em>font</em> - select your own font<br />
			<em>color</em> - select your own text color<br />
			<em>speed</em> - select your scroll speed (whole numbers only)<br />
			<em>prefix</em> - add a message at the list head<br />
			<em>mins</em> - select your update interval (minimum 5 minutes)
		</div><br />
		Example:<br />
		<div class='indent'>
			<a href='donors.php?fundraiser=broder-fundraiser&amp;count=20&amp;font=Moderna&amp;color=RosyBrown&amp;speed=1&amp;prefix=Latest%20at%20broder.tf&amp;mins=10' id='example'>
				/donors.php?fundraiser=broder-fundraiser&amp;count=20&amp;font=Moderna&amp;color=RosyBrown&amp;speed=1&amp;prefix=Latest%20at%20broder.tf&amp;mins=10
			</a>
		</div>
	</div>
	<div id='footer'>
		<div id='credits'>
			Created by @bcpk using PHP & jQuery. Credit to Remy Sharp for the <a href='http://remysharp.com/2008/09/10/the-silky-smooth-marquee/'>smooth marquee</a>. Intended for use with the <a href='https://obsproject.com/forum/viewtopic.php?f=11&t=3284'>OBS Browser Source Plugin</a> by faruton.
		</div>
	</div>
";}
?>

<script type="text/javascript">

function marqueeInsert() {
	$('body').prepend('<marquee class="result" loop="1" behavior="scroll" scrollamount="<? $speed = (isset($_REQUEST['speed']))?$_REQUEST['speed'] : 1; echo $speed; ?>" direction="left"><? $prefix = (isset($_REQUEST['prefix']))?$_REQUEST['prefix'].": " : ""; echo $prefix; ?></marquee>');
}

function donorsInsert(html, len) {
	$(".pledge-name span", html).each(function(index) {
                $(".result").append($.trim($(this).text()));
                if (index + 1 < len) {
                        $(".result").append(", ");
                }
        });
}

$(document).ready(function() {
	marqueeInsert();
	var timestamp = Math.floor((new Date()).getTime() / 1000);
	var pledgeHTML ='<?php
                if ($handle) {
                        while (!feof($handle)) {
                        $buffer = fgets($handle, 4096);
                        echo str_replace('\'','"', trim($buffer));
                	}
                	fclose($handle);
		}
		else {
			echo "no project handle";
		}
                ?>';
	if(pledgeHTML == 'no project handle') {
		return;
	}

        var html = $.parseHTML(pledgeHTML);
        var len = $(".pledge-name span", html).length;
	$(".pledge-name span", html).each(function(index) {
                $(".result").append($.trim($(this).text()));
                if (index + 1 < len) {
                        $(".result").append(", ");
                }
        });

	$('marquee').marquee("marquee");
	$(".marquee").on('stop', stopHandler);

	function stopHandler(){
                currenttimestamp = Math.floor((new Date()).getTime() / 1000);
                if(currenttimestamp - timestamp > (60*<? $mins = (isset($_REQUEST['mins']) && $_REQUEST['mins'] >= 5)?$_REQUEST['mins'] : 5; echo $mins; ?>)) {
                        location.reload();
                }
                else {
                        $('.marquee').remove();
			marqueeInsert();
			$(".pledge-name span", html).each(function(index) {
                		$(".result").append($.trim($(this).text()));
                		if (index + 1 < len) {
                        		$(".result").append(", ");
                		}
        		});
                        $('marquee').marquee("marquee");
			$(".marquee").on('stop', stopHandler);
                }
	}
});
</script>
</body>
</html>
