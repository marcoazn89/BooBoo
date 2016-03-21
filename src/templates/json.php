{
    "status": <?php echo $response->getStatusCode();?>,
    "description": "<?php echo $response->getReasonPhrase(); ?>",
    "message": "<?php if (!empty($data)) echo $data; else echo 'Please try again later'; ?>"
}
