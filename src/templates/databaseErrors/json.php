<?php
use HTTP\Response\Status;
$status = Status::getInstance();
?>

{
	"status": <?php echo $status->getCode();?>,
	"description": "<?php echo $status->getMessage(); ?>",
	"message": "Please try again later <?php echo $data ?>".
}
