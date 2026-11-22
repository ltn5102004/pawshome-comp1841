<?php
// Log out (controller).
// Source: adapted from PHP Group (no date) session_destroy - Example #1 "Destroying a session with $_SESSION", PHP Manual.
//         Available at: https://www.php.net/manual/en/function.session-destroy.php
// LIM-01 fix (22 Nov 2026): logging out changes state, so it only happens on a POST with the CSRF token.
// A plain link (GET) - for example an <img src="logout.php"> on another site - can no longer log people out;
// it shows a small page with a "Log out" button instead.
// Source: OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (state-changing requests use POST + token).
//         https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//         MDN Web Docs (no date) POST request method. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods/POST
include 'includes/Functions.php';   // starts the session (session_start)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	requireCsrfToken('index.php');
	// The three steps (unset, delete the cookie, destroy) are in logOut() in includes/Functions.php.
	// session_regenerate_id inside it gives the fresh session a new id.
	// Source: PHP Group (no date) session_regenerate_id, PHP Manual. Available at: https://www.php.net/manual/en/function.session-regenerate-id.php
	logOut();
	setFlash('You have been logged out.');
	redirect('index.php');
}

if (!isLoggedIn()) {
	redirect('index.php');
}

$title = 'Log out';
$output = loadTemplate('logout.html.php', []);
include 'templates/layout.html.php';
