<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - PHP Group (no date) array_column, PHP Manual. https://www.php.net/manual/en/function.array-column.php
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$title = 'My account';
	$favourites = favouritePets($pdo, currentUserId());
	// Requests other people sent for pets this user looks after (0 for adopters)
	$incoming = countRequestsToHandle($pdo, currentUserId(), currentShelterId());
	$myPets = array_filter(petsByUser($pdo, currentUserId()), function ($pet) {
		return !isShelterStaff() || $pet['shelterid'] != currentShelterId();
	});
	$output = loadTemplate('myaccount.html.php', [
		'role' => accountRole(!empty($myPets) || $incoming['total'] > 0),
		'incoming' => $incoming,
		'user' => currentUser(),   // includes the name and verified flag of the staff member's shelter
		'profile' => findAdopterProfile($pdo, currentUserId()),
		'favourites' => $favourites,
		// Every pet in this list is a favourite, so the heart buttons show as "saved"
		'favouriteIds' => array_column($favourites, 'id'),
		// Staff see every pet of their shelter in their own section, so those are left out of "Pets I have listed"
		'myPets' => $myPets,
		// null for people who are not shelter staff; 'approval' => 'any' includes pets that are not approved yet
		'shelterPets' => isShelterStaff() ? allPets($pdo, ['shelterid' => currentShelterId(), 'approval' => 'any', 'sort' => 'name_asc']) : null,
		'myRequests' => requestsByUser($pdo, currentUserId())
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
