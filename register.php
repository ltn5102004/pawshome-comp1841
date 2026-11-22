<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - PHP Group (no date) password_hash, PHP Manual. https://www.php.net/manual/en/function.password-hash.php
//   - PHP Group (no date) filter_var with FILTER_VALIDATE_EMAIL, PHP Manual. https://www.php.net/manual/en/filter.examples.validation.php
include 'includes/Functions.php';

// Someone who is already logged in does not need a second account
if (isLoggedIn()) {
	redirect('myaccount.php');
}

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$user = ['name' => '', 'email' => ''];
	$errors = [];

	if (isset($_POST['email'])) {
		requireCsrfToken('register.php');
		$user['name'] = getValue($_POST, 'name');
		$user['email'] = getValue($_POST, 'email');
		$password = getValue($_POST, 'password', '', false);

		if ($user['name'] == '' || strlen($user['name']) > 100) {
			$errors[] = 'Please enter your name (up to 100 characters).';
		}
		if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Please enter a valid email address.';
		} elseif (findUserByEmail($pdo, $user['email'])) {
			$errors[] = 'An account with that email already exists.';
		}
		$errors = array_merge($errors, validatePassword($password, getValue($_POST, 'confirm', '', false)));

		if (empty($errors)) {
			$id = insert($pdo, 'user', [
				'name' => $user['name'],
				'email' => $user['email'],
				// Never store the real password, only a hash of it
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'role' => 'member',
				'datejoined' => new DateTime()
			]);
			session_regenerate_id(true);
			$_SESSION['userid'] = $id;
			setFlash('Your account has been created. Welcome to PawsHome!');
			redirect('myaccount.php');
		}
	}

	$title = 'Register';
	$output = loadTemplate('register.html.php', ['user' => $user, 'errors' => $errors]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
