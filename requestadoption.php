<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic insert() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) header() - redirect with Location then exit, PHP Manual. https://www.php.net/manual/en/function.header.php
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('pets.php');
	}

	$pet = findById($pdo, 'pet', 'id', getValue($_POST, 'petid', 0));
	$message = getValue($_POST, 'message');

	if (!$pet) {
		setFlash('That pet could not be found.', 'error');
		redirect('pets.php');
	}

	if ($pet['approval'] != 'Approved') {
		setFlash('That pet is not open for adoption requests.', 'error');
		redirect('pets.php');
	}

	// Nobody adopts a pet they manage themselves (the owner, an admin, or the staff of the pet's shelter)
	if (canManagePet($pet)) {
		setFlash('You manage this listing, so you cannot send an adoption request for it.', 'error');
		redirect('pet.php?id=' . $pet['id']);
	}

	if ($pet['status'] == 'Adopted') {
		setFlash($pet['name'] . ' has already been adopted.', 'error');
	} elseif (hasRequested($pdo, $pet['id'], currentUserId())) {
		setFlash('You have already sent a request for ' . $pet['name'] . '.', 'error');
	} elseif (strlen($message) < 10 || strlen($message) > 1000) {
		setFlash('Your message must be between 10 and 1000 characters.', 'error');
	} else {
		try {
			insert($pdo, 'adoptionrequest', [
				'message' => $message,
				'requestdate' => new DateTime(),
				'petid' => $pet['id'],
				'userid' => currentUserId()
			]);
			setFlash('Your request for ' . $pet['name'] . ' has been sent. ' . (empty($pet['shelterid']) ? 'The owner' : 'The shelter') . ' will be in touch.');
		} catch (PDOException $e) {
			// The database allows only one request per person per pet (unique key onerequestperpet). This happens
			// when the button is pressed twice quickly: the second request arrives before the first one is saved.
			if ($e->getCode() == 23000) {
				setFlash('You have already sent a request for ' . $pet['name'] . '.', 'error');
			} else {
				throw $e;
			}
		}
	}

	redirect('pet.php?id=' . $pet['id']);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
