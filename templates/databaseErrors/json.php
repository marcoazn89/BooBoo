<?php
use HTTP\response\Status;
$status = Status::getInstance();
?>

{
	"status": <?php echo $status->code.$a ?>,
	"description": "<?php echo $status->message; ?>",
	"message": "Please try again later"
}
