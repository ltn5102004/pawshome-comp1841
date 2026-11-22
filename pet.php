<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - PHP Group (no date) http_response_code (404 page), PHP Manual. https://www.php.net/manual/en/function.http-response-code.php
include 'includes/Functions.php';

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$pet = getPet($pdo, getValue($_GET, 'id', 0));

	// A pet that is not approved yet looks exactly like a pet that does not exist (404) to everyone but its owner and admins
	if (!$pet || !canViewPet($pet)) {
		http_response_code(404);
		$title = 'Pet not found';
		$output = loadTemplate('error.html.php', ['message' => 'Sorry, we could not find that pet. It may have been removed.']);
	} else {
		$title = $pet['name'];
		$output = loadTemplate('pet.html.php', [
			'pet' => $pet,
			'alreadyRequested' => isLoggedIn() && hasRequested($pdo, $pet['id'], currentUserId()),
			'favouriteIds' => isLoggedIn() ? favouriteIds($pdo, currentUserId()) : [],
			'profile' => isLoggedIn() ? findAdopterProfile($pdo, currentUserId()) : false
		]);
	}
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
