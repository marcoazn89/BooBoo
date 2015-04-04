<html>
	<head>
		<title>BooBoo - <?php echo $this->statusCode; ?></title>
<link href='https://www.dropbox.com/static/css/error.css' rel='stylesheet' type='text/css'>
<link rel='shortcut icon' href='brokentslogo.jpg'>
</head>
<body>
<div class='figure'>
<img src='templates/databaseErrors/booboo.png' width='300' alt=<?php echo "'Error ".$this->statusCode."'"; ?>>
</div>
<div id='errorbox'>
<h1>Error (<?php echo $this->statusCode; ?>)</h1>
<p>Oh no it's a <a href="http://github.com/marcoazn89/BooBoo">BooBoo!</a> Something went terribly wrong and we are sorry. Please try again later.</p>
<p>Thanks,</p>
<p>A good looking production BooBoo</p>
</div>
</body>
</html>
