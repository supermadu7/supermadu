<?php
  /**
  * Contact form handler for index.html.
  * Responds with plain text: 'OK' on success (what assets/vendor/php-email-form/validate.js
  * expects), otherwise the error message to show the visitor.
  */

  $receiving_email_address = 'ifeanyi@madu.uk';

  // Must be an address on this domain so the server is allowed to send as it (SPF)
  $sending_email_address = 'ifeanyi@madu.uk';

  header('Content-Type: text/plain; charset=UTF-8');

  if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    die( 'Invalid request.');
  }

  // Honeypot: the "website" field is hidden from people, so only bots fill it in
  if( !empty($_POST['website']) ) {
    die( 'OK');
  }

  // Single-line fields end up in mail headers, so strip line breaks from them
  $name = trim(preg_replace('/[\r\n]+/', ' ', $_POST['name'] ?? ''));
  $email = trim($_POST['email'] ?? '');
  $subject = trim(preg_replace('/[\r\n]+/', ' ', $_POST['subject'] ?? ''));
  $message = trim($_POST['message'] ?? '');

  if( $name === '' || $subject === '' || $message === '' ) {
    die( 'Please fill in your name, a subject and a message.');
  }

  if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
    die( 'Please enter a valid email address.');
  }

  if( mb_strlen($name) > 100 || mb_strlen($subject) > 150 || mb_strlen($message) > 5000 ) {
    die( 'Your message is too long.');
  }

  $body = "From: $name\nEmail: $email\n\n$message\n";

  $headers = array(
    'From' => "ifeanyi.madu.uk contact form <$sending_email_address>",
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8'
  );

  $sent = mail($receiving_email_address, mb_encode_mimeheader($subject, 'UTF-8'), $body, $headers);

  echo $sent ? 'OK' : 'Sorry, your message could not be sent. Please email me directly.';
?>
