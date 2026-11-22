<?php
// Create / edit the adopter profile (controller).
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Generic insert() / update() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - OWASP (no date) Unvalidated Redirects and Forwards Cheat Sheet (safeBackUrl). https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	// Where to go after saving (e.g. back to the pet the user wanted to adopt)
	$back = safeBackUrl(getValue($_GET, 'back', getValue($_POST, 'back')), 'myaccount.php');

	$profile = findAdopterProfile($pdo, currentUserId()) ?: [
		'hometype' => '', 'householdagrees' => 0, 'experience' => '', 'otherpets' => '', 'budget' => ''
	];
	$errors = [];

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('adopterprofile.php');
		$profile = getValues($_POST, ['hometype', 'householdagrees', 'experience', 'otherpets', 'budget']);
		$errors = validateAdopterProfile($profile);

		if (empty($errors)) {
			saveAdopterProfile($pdo, currentUserId(), [
				'hometype' => $profile['hometype'],
				// A tick box is only sent when it is ticked
				'householdagrees' => $profile['householdagrees'] != '' ? 1 : 0,
				'experience' => $profile['experience'],
				'otherpets' => $profile['otherpets'] != '' ? $profile['otherpets'] : null,
				'budget' => $profile['budget']
			]);
			setFlash('Your adopter profile has been saved.');
			redirect($back);
		}
	}

	$title = 'Adopter profile';
	$output = loadTemplate('adopterprofile.html.php', ['profile' => $profile, 'errors' => $errors, 'back' => $back]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
