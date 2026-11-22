<?php
// Log out (controller).
// Source: adapted from PHP Group (no date) session_destroy - Example #1 "Destroying a session with $_SESSION", PHP Manual.
//         Available at: https://www.php.net/manual/en/function.session-destroy.php
include 'includes/Functions.php';   // starts the session (session_start)

// The three steps (unset, delete the cookie, destroy) are in logOut() in includes/Functions.php.
// session_regenerate_id inside it gives the fresh session a new id.
// Source: PHP Group (no date) session_regenerate_id, PHP Manual. Available at: https://www.php.net/manual/en/function.session-regenerate-id.php
logOut();
setFlash('You have been logged out.');
redirect('index.php');
