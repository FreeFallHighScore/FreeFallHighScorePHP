<?php
require_once('recaptchalib.php');
require_once('common.php');

	if(isset($_POST['submitForm'])) {
		$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
		
		if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
			echo '<div class="errormsgboxunder">The reCAPTCHA wasn\'t entered correctly. Go back and try it again.';
			exit();
		}

		if (!defined("PHP_EOL"))
    		define("PHP_EOL", "\r\n");
		
		function isEmail($email) { // Email address verification, do not edit.
		    return (preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
		}
		
		$url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$filename = explode("/", $url);
		for( $i = 0; $i < (count($filename) - 1); ++$i ) {
		$baseurl .= $filename[$i].'/';
		}
		
		// Person Info
		$fname = htmlentities($_POST['fname'], ENT_QUOTES, 'UTF-8');
		$lname = htmlentities($_POST['lname'], ENT_QUOTES, 'UTF-8');
		$fullname = $fname." ".$lname;
		$email = str_replace( "\r\n", '', htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8') );
		$city = $_POST['city'];
		// Preserver Info
		$preservername = htmlentities($_POST['preservername'], ENT_QUOTES, 'UTF-8');
		$description = htmlentities($_POST['description'], ENT_QUOTES, 'UTF-8');
		$file1 = $baseurl.'uploads/'.htmlentities($_POST['file1'], ENT_QUOTES, 'UTF-8');
		$file2 = ($_POST['file2'] == '') ? '' : $baseurl.'uploads/'.htmlentities($_POST['file2'], ENT_QUOTES, 'UTF-8');
		$file3 = ($_POST['file3'] == '') ? '' : $baseurl.'uploads/'.htmlentities($_POST['file3'], ENT_QUOTES, 'UTF-8');
		$detaillink = htmlentities($_POST['detaillink'], ENT_QUOTES, 'UTF-8');
		// Additional Info
		$comments = htmlentities($_POST['comments'], ENT_QUOTES, 'UTF-8');
		$attend = htmlentities($_POST['attend'], ENT_QUOTES, 'UTF-8');
		
				
		if (trim($fname) == '') {
		    echo '<div class="errormsgboxunder">Attention! You must enter your first name.</div>';
		    exit();
		} else if (trim($lname) == '') {
		    echo '<div class="errormsgboxunder">Attention! You must enter your last name.</div>';
		    exit();
		} else if (trim($email) == '') {
		    echo '<div class="errormsgboxunder">Attention! Please enter a valid email address.</div>';
		    exit();
		} else if (!isEmail($email)) {
		    echo '<div class="errormsgboxunder">Attention! You have entered an invalid e-mail address, try again.</div>';
		    exit();
		}
		
		if (get_magic_quotes_gpc()) {
	        $comments = stripslashes($comments);
	        $description = stripslashes($description);
	        $detaillink = stripslashes($detaillink);
	    }
		
	    $e_subject = 'PRESERVER CONTEST: '.$fullname;

	    $e_body = "$fullname has submitted the folowing information for the FFHS Preserver Contest:".PHP_EOL.PHP_EOL;
	    $e_content = "$fullname".PHP_EOL."$email".PHP_EOL."$city".PHP_EOL.PHP_EOL."Preserver Name:".PHP_EOL."$preservername".PHP_EOL.PHP_EOL."Description: ".PHP_EOL."\"$description\"".PHP_EOL.PHP_EOL."Image 1: ";
	    $e_content2 = "$file1".PHP_EOL."Image 2: $file2".PHP_EOL."Image 3: $file3".PHP_EOL.PHP_EOL."More Details: $detaillink".PHP_EOL.PHP_EOL."Comments: ".PHP_EOL."\"$comments\"".PHP_EOL.PHP_EOL."Attendance: ".$attend.PHP_EOL.PHP_EOL;
	    
	    $msg = wordwrap($e_body.$e_content.$e_content2, 70);
		/*$safefiles = str_replace(' ','-',$fname);
		$safefiles = $safefiles.'-'.str_replace(' ','-',$lname);
		$safefiles = $safefiles.'-'.str_replace(' ','-',$preservername);

		$entryFileName = $safefiles.'.txt';
		$entryDir = 'entries/';
		$fullFilePath = $entryDir.$entryFileName;
		$entryFile = fopen($fullFilePath,'w') or die('can\'t open the file');
		fwrite($entryFile, $msg);
		fclose($entryFile);*/
		
		$headers .= "From: $address".PHP_EOL;
	    $headers .= "Reply-To: $address".PHP_EOL;
	    $headers .= "MIME-Version: 1.0".PHP_EOL;
	    $headers .= "Content-type: text/plain; charset=utf-8".PHP_EOL;
	    $headers .= "Content-Transfer-Encoding: quoted-printable".PHP_EOL;
	    
	    if (mail($address, $e_subject, $msg, $headers)) {
	       		echo "<div class='successbox'>Thank you for submitting your preserver, $fname. You will hear from us soon!</div>";
			exit(0);
		} else {
	    		echo '<div class="errormsgboxunder">Attention! There was an error while submitting the form, try again later.</div>';
			exit(0);
	    	}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Freefall Highscore Design Contest | Submit</title>
		<link rel="stylesheet" href="css/contest.css" type="text/css" media="screen" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
		<script src="js/scroll.js" type="text/javascript"></script>
		<script src="js/form.js" type="text/javascript"></script>
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/fileuploader.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				/*
				$('#left_sb').stickyScroll({
					container : $('#wrapper')
				});
				$('#right_sb').stickyScroll({
					container : $('#wrapper')
				});
			 	*/
			 	
			 	$("#contestForm").validate({
			 		rules: {
						fname: "required",
						lname: "required",
						email: {
							required: true,
							email: true
						},
						city: "required",
						preservername: "required",
						file1: "required",
						attend: "required"
					},
					messages: {
						fname: "Please enter your first name",
						lname: "Please enter your last name",
						email: "Please enter a valid email address",
						city: "Please enter your city name",
						preservername: "Please enter your preserver's name",
						file1: "Please upload at least 1 image",
						attend: "Please specify your attendance"
					},
					errorContainer: $('#errorContainer'),
					errorLabelContainer: $('#errorContainer ol'),
					wrapper: 'li',
   					submitHandler: function(form) {
	   					$("#contestForm").ajaxSubmit({ 
					        target: '.formMessage', 
					        //beforeSubmit:  nothing, 
					        //success:       showResponse, 
					        clearForm: false, 
					        resetForm: false 
					 	});
					 	return false;
	   				}
	   			});
	   			
	   			var uploader1 = new qq.FileUploader({
	                element: document.getElementById('file-uploader-1'),
	                action: 'upload.php',
	                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
	                multiple:false,
	                onComplete: function(id, fileName, responseJSON){
						if(responseJSON.success == true) {
							$('#hiddenFile1').val(responseJSON.filename);
							$('#file-uploader-1 .qq-upload-button').addClass('qq-success-button').bind('click', function() {
	                			return false;
	                		});
	                		$("#contestForm").valid();
						} else {
							$('#file-uploader-1 .qq-upload-list li.qq-upload-fail').delay(4000).fadeOut(500, function() {
								$(this).remove();
							});
						};
	                }
	            });
	   			
	   			var uploader2 = new qq.FileUploader({
	                element: document.getElementById('file-uploader-2'),
	                action: 'upload.php',
	                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
	                onComplete: function(id, fileName, responseJSON){
	                	if(responseJSON.success == true) {
							$('#hiddenFile2').val(responseJSON.filename);
							$('#file-uploader-2 .qq-upload-button').addClass('qq-success-button').bind('click', function() {
	                			return false;
	                		});
	                		$("#contestForm").valid();
						} else {
							$('#file-uploader-2 .qq-upload-list li.qq-upload-fail').delay(4000).fadeOut(500, function() {
								$(this).remove();
							});
						};
	                }
	            });
	   			
	   			var uploader3 = new qq.FileUploader({
	                element: document.getElementById('file-uploader-3'),
	                action: 'upload.php',
	                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
	                onComplete: function(id, fileName, responseJSON){
	                	if(responseJSON.success == true) {
							$('#hiddenFile3').val(responseJSON.filename);
							$('#file-uploader-3 .qq-upload-button').addClass('qq-success-button').bind('click', function() {
	                			return false;
	                		});
	                		$("#contestForm").valid();
						} else {
							$('#file-uploader-3 .qq-upload-list li.qq-upload-fail').delay(4000).fadeOut(500, function() {
								$(this).remove();
							});
						};
	                }
	            });   
			});

		</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="left_sb" >
				<div id="logo"></div>
				<div id="social">
					<ul>
						<li><a href="http://www.facebook.com/freefallhighscore"><img src="images/facebook.png"></a></li>
						<li><a href="https://twitter.com/#!/freefallhiscore"><img src="images/twitter.png"></a></li>
						<!--
						<li><a href="http://twitter.com"><img src="images/twitter.png"></a></li>
						<li><a href="http://google.com"><img src="images/google.png"></a></li>
						-->
					</ul>
				</div>
				<div id="navigation">
					<a href="contest.html#contest" class="navi">OVERVIEW</a>
					<a href="contest.html#prizes" class="navi">PRIZES</a>
					<a href="contest.html#judging" class="navi">JUDGING CRITERIA</a>
					<a href="contest.html#constraints" class="navi">CONSTRAINTS</a>
					<a href="contest.html#fineprint" class="navi">FINE PRINT</a>
					<a href="contest.html#contactus" class="navi">CONTACT US</a>
				</div>
				<!--
				<div id="examples">
					<div class="example_item">
						<img src="images/preserver1.png">
					</div>
				</div>
				-->
			</div>
			<!-- end left_sb -->
			<div id="content">
				<form action="submit.php" enctype="multipart/form-data" id="contestForm" method="post">
					<h2>Your Info</h2>
					<p>
						<strong>If you are not in New York City area you'll need to be able to ship the preserver to us by the time of the event</strong>
					</p>
					
					<br clear="all">
					<p>
						<label class="inputlabel" for="fname">First Name: </label><input type="text" name="fname">
					</p>
					<p>
						<label class="inputlabel" for="lname">Last Name: </label><input type="text" name="lname">
					</p>
					<p>
						<label class="inputlabel" for="email">Email: </label><input type="text" name="email">
					</p>
					<p>
						<label class="inputlabel" for="city">City: </label><input type="text" name="city">
					</p>
					<br clear="all">
					<br clear="all">
					<h2>Preserver Info</h2>
					<br clear="all">
					<p>
						<strong>Remember this call is for documentation finished 
						preservers only. Concepts and sketches are not accepted. It has got to look like it's ready to drop!</strong>
					</p>

					<p>
						<label class="inputlabel">Preserver Name: </label><input type="text" name="preservername">
					</p>
					<p>
						Describe how you made your preserver and how it works.
					</p>
					<p>
						<textarea cols="60" rows="10" name="description"></textarea>
					</p>
					<p>
						Upload up to three images of your preserver.
					</p>
					<br clear="all">
					<div id="file-uploader-1" class="uploadContainer"></div>
					<input type="hidden" name="file1" id="hiddenFile1" />
					<br clear="all">
					<div id="file-uploader-2" class="uploadContainer"></div>
					<input type="hidden" name="file2" id="hiddenFile2" />
					<br clear="all">
					<div id="file-uploader-3" class="uploadContainer"></div>
					<input type="hidden" name="file3" id="hiddenFile3" />
					<br clear="all">
					<p>
						Paste in an optional youtube or vimeo link for more detail.
					</p>
					<p>
						<input type="text" name="detaillink" style="width: 440px;">
					</p>
					<p>
						Do you have any additional comments or stuff we should know?
					</p>
					<p>
						<textarea cols="60" rows="10" name="comments"></textarea>
					</p>
					<br/>
					<p>
						Can you come to the event on October 13th?&nbsp;&nbsp;
						<label for="attend_yes">Yes </label><input type="radio" id="attend_yes" name="attend" value="yes" />&nbsp;&nbsp;<label for="attend_no">No </label><input type="radio" id="attend_no" name="attend" value="no" />
					</p>
					<p>
						<?php echo $captcha; ?>
					</p>
					<p>
						<input type="hidden" name="submitForm" value="Submit" />
						<input type="submit" value="Submit" />
					</p>
				</form>
				<br clear="all">
				<div class="formMessage"></div>
				<br clear="all" />
			</div>
			<!-- end content -->
			
			<div id="right_sb">
				<div id="sbHeader">
					<span>Overview</span>
				</div>
				<div id="sbBody">
					<span class="sbTextHeader"> Keep in mind </span>
					<ul class="bullet">
						<li>Points for smarts, style, and creativity.</li>
						<li>The Preserver cannot obstruct the camera from recording video</li>
						<li>Timelimit - 20 sec between hitting "go" and drop</li>
						<li>One shot per entry</li>
						<li>Survive a drop from upwards of 140 feet</li>
					</ul>
					<br>
					<span class="sbTextHeader"> Deadline </span>
					<br>
					<ul class="nobullet">
						<li>
							30 September, 2011
						</li>
					</ul>
				</div>
				<div id="sbFooter"></div>
				<br clear="all">
				<br clear="all" />
				<div id="errorContainer" class="errormsgbox">
					<ol></ol>
				</div>
			</div>
		</div>
		<!-- end right_sb -->
	</body>
</html>
