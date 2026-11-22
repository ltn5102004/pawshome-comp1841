<?php
// "Forgot your password?" (controller) - LIM-03 fix, 22 Nov 2026.
// The page always shows the SAME message, whether or not the email has an account, so it cannot be used to find
// out who is registered. The email holds a one-time link that works for 30 minutes.
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Forgot Password Cheat Sheet (same response for every email, random token, expiry, single use).
//     https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html
//   - PHP Group (no date) filter_var with FILTER_VALIDATE_EMAIL, PHP Manual. https://www.php.net/manual/en/filter.examples.validation.php
include 'includes/Functions.php';

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$email = '';
	$errors = [];
	$sent = false;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('forgotpassword.php');
		$email = getValue($_POST, 'email');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Please enter a valid email address.';
		} else {
			$user = findUserByEmail($pdo, $email);
			if ($user && resetsInLastHour($pdo, $user['id']) < RESET_MAX_PER_HOUR) {
				$token = createPasswordReset($pdo, $user['id']);
				$link = SITE_URL . '/resetpassword.php?token=' . $token;
				sendEmail($user['email'], 'Reset your password',
					"Hello " . $user['name'] . ",\n\nSomeone asked to reset the password of your PawsHome account.\n"
					. "To choose a new password, open this link within " . RESET_MINUTES . " minutes:\n\n" . $link . "\n\n"
					. "If it was not you, ignore this email - your password stays the same.\n");
			}
			$sent = true;   // same answer for every email address
		}
	}

	$title = 'Forgot your password?';
	$output = loadTemplate('forgotpassword.html.php', ['email' => $email, 'errors' => $errors, 'sent' => $sent]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
