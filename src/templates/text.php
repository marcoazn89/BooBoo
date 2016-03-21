status: <?php echo $response->getStatusCode() . "\n" ?>
description: <?php echo $response->getReasonPhrase() . "\n"; php?>
message: "<?php if (!empty($message)) echo $message; else echo 'Please try again later'; ?>"
