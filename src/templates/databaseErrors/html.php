<html>
	<head>
		<title>BooBoo - <?php echo $statusCode; ?></title>
<link href='https://www.dropbox.com/static/css/error.css' rel='stylesheet' type='text/css'>
<link rel='shortcut icon' href='./../src/templates/DatabaseErrors/booboo.png>
</head>
<body>
<div class='figure'>
<img src='./../src/templates/DatabaseErrors/booboo.png' width='300' alt=<?php echo "'Error ".$statusCode."'"; ?>>
</div>
<div id='errorbox'>
<h1>Error (<?php echo $statusCode; ?>)</h1>
<p>Oh no it's a <a href="http://github.com/marcoazn89/BooBoo">BooBoo!</a> Something went terribly wrong and we are sorry. Please try again later. <?php echo $data; ?></p>
<p>Thanks,</p>
<p>A good looking production BooBoo</p>
</div>
</body>
</html>
