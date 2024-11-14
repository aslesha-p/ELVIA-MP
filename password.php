<?php
echo password_hash("password123", PASSWORD_DEFAULT); // for password123
echo "<br>";
echo password_hash("securepassword", PASSWORD_DEFAULT); // for securepassword
echo "<br>";
echo password_hash("mypassword", PASSWORD_DEFAULT); // for mypassword
echo "<br>";
echo password_hash("bobpassword", PASSWORD_DEFAULT); // for bobpassword
echo "<br>";
echo password_hash("charlie123", PASSWORD_DEFAULT); // for charlie123
?>