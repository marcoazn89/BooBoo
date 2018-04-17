{
    "status": <?php echo $response->getStatusCode();?>,
    "description": <?php echo json_encode($response->getReasonPhrase()); ?>,
    "message": <?php if (!empty($message)) echo json_encode($message); else echo json_encode('Please try again later'); ?>,
    "data": <?php if (!empty($data)) echo json_encode($data); else echo 'null'; ?>
}
