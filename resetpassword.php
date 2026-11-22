<?php
// Choose a new password from the emailed link (controller) - LIM-03 fix, 22 Nov 2026.
// The token is checked on the GET and again on the POST; after a successful reset every link of that user stops working.
// Referrer-Policy: no-referrer stops the token in the address bar being sent to any other site.
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Forgot Password Cheat Sheet (single use, expiry, Referrer-Policy). https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html
//   - PHP Group (no date) password_hash, PHP Manual. https://www.php.net/manual/en/function.password-hash.php
//   - MDN Web Docs (no date) Referrer-Policy header. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Referrer-Policy
include 'includes/Functions.php';
header('Referrer-Policy: no-referrer');

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$token = getValue($_POST, 'token', getValue($_GET, 'token'));
	$reset = findValidReset($pdo, $token);
	$errors = [];

	if ($reset && $_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('resetpassword.php?token=' . urlencode($token));
		$password = getValue($_POST, 'password', '', false);
		$errors = validatePassword($password, getValue($_POST, 'confirm', '', false));
		if (empty($errors)) {
			finishPasswordReset($pdo, $reset, $password);
			// a person who was logged in elsewhere in this browser starts again with a fresh session
			logOut();
			setFlash('Your password has been changed. You can now log in with the new password.');
			redirect('login.php');
		}
	}

	$title = 'Choose a new password';
	$output = loadTemplate('resetpassword.html.php', ['reset' => $reset, 'token' => $token, 'errors' => $errors]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
