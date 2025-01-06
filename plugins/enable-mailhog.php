<?php
/**
 * Plugin Name: Enable Mailhog
 */
function prefixConfigMailhog($phpmailer) {
	$phpmailer->isSMTP();
	$phpmailer->SMTPAuth    = false;
	$phpmailer->SMTPSecure  = '';
	$phpmailer->SMTPAutoTLS = false;
	$phpmailer->Host        = 'mailhog-service';
	$phpmailer->Port        = '1025';
	$phpmailer->Username    = null;
	$phpmailer->Password    = null;
}

add_action('phpmailer_init', 'prefixConfigMailhog', 10, 1);
