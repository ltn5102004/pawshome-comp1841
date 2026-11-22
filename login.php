<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - PHP Group (no date) password_verify, PHP Manual. https://www.php.net/manual/en/function.password-verify.php
//   - PHP Group (no date) session_regenerate_id, PHP Manual. https://www.php.net/manual/en/function.session-regenerate-id.php
include 'includes/Functions.php';

// Someone who is already logged in has no reason to see the log in form (they would only end up in two accounts)
if (isLoggedIn()) {
	redirect('myaccount.php');
}

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$email = '';
	$errors = [];

	if (isset($_POST['email'])) {
		requireCsrfToken('login.php');
		$email = getValue($_POST, 'email');
		$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		$user = findUserByEmail($pdo, $email);

		if (recentFailedLogins($pdo, $email, $ip) >= MAX_LOGIN_FAILURES) {
			// Too many wrong passwords: the password is not even checked (not even the right one is accepted for now)
			$errors[] = 'Too many failed attempts. Please wait ' . LOGIN_WINDOW_MINUTES . ' minutes and try again.';
		// password_verify compares the typed password with the hash stored in the database
		} elseif ($user && password_verify(getValue($_POST, 'password', '', false), $user['password'])) {
			clearFailedLogins($pdo, $email, $ip);
			session_regenerate_id(true);
			$_SESSION['userid'] = $user['id'];
			setFlash('Welcome back, ' . $user['name'] . '!');
			redirect($user['role'] == 'admin' ? 'admin/index.php' : 'myaccount.php');
		} else {
			// Same message for both cases so nobody can find out which emails are registered
			recordFailedLogin($pdo, $email, $ip);
			$errors[] = 'The email or password is incorrect.';
		}
	}

	$title = 'Log in';
	$output = loadTemplate('login.html.php', ['email' => $email, 'errors' => $errors]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
