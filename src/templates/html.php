<html>
	<head>
		<title>BooBoo - <?php echo $response->getStatusCode();?></title>
<link href='https://www.dropbox.com/static/css/error.css' rel='stylesheet' type='text/css'>
<link rel='shortcut icon' href='./../src/templates/booboo.png'>
</head>
<body>
<div class='figure'>
<img src='./../src/templates/booboo.png' width='300'>
</div>
<div id='errorbox'>
<h1>Error (<?php echo $response->getStatusCode();?>)</h1>
<p>Oh no it's a <a href="http://github.com/marcoazn89/BooBoo">BooBoo!</a> <?php if (!empty($message)) echo $message; else echo 'Something went terrible wrong and we are sorry. Please try again later.'; ?></p>
<p>Thanks,</p>
<p>A good looking production BooBoo</p>
</div>
</body>
</html>
