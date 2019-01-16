<?php
// Multiple recipients
$to = 'email1@email.com, email2@email.com';

// Subject
$subject = 'Subject';

// Message
$message = '
<html>
<head>
  <title>Test Email</title>
</head>
<body>
  <p>Here is the test Email!</p>
</body>
</html>
';

// Mail it
mail($to, $subject, $message, "");
?>