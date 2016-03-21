{
    "status": <?php echo $response->getStatusCode();?>,
    "description": "<?php echo $response->getReasonPhrase(); ?>",
    "message": "<?php if (!empty($message)) echo $message; else echo 'Please try again later'; ?>",
    "data": <?php if (!empty($data)) echo json_encode($data); else echo null; ?>
}
