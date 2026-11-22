<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic insert() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - Image upload adapted from W3Schools (no date) PHP File Upload. https://www.w3schools.com/php/php_file_upload.asp
//   - PHP Group (no date) header() - redirect with Location then exit, PHP Manual. https://www.php.net/manual/en/function.header.php
include 'includes/Functions.php';
// Only members can add pets
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$pet = [];
	$errors = [];

	if (isset($_POST['pet'])) {
		requireCsrfToken('addpet.php');
		$pet = getValues($_POST['pet'], petFormKeys());
		// A new listing is always Available, whatever the form says (see allowedPetStatuses in Functions.php)
		$pet['status'] = 'Available';
		// The server decides the shelter (see listingPlace in Functions.php), never the form
		$place = listingPlace($pet);
		$errors = validatePet($pdo, $pet, $place);

		// Only try the upload when the rest of the form is valid
		if (empty($errors)) {
			$upload = uploadImage($_FILES['image'] ?? null);
			if ($upload['error']) {
				$errors[] = $upload['error'];
			}
		}

		if (empty($errors)) {
			// petFields() turns the form values into database columns (see includes/Functions.php)
			$fields = petFields($pet, $place);
			$fields['image'] = $upload['filename'];
			$fields['dateadded'] = new DateTime();
			$fields['userid'] = currentUserId();
			// Members' listings wait for an admin to approve them; staff of a verified shelter are approved
			// at once (see approvalForSave in Functions.php)
			$fields['approval'] = approvalForSave(null, $place['shelterid']);
			insert($pdo, 'pet', $fields);
			if ($fields['approval'] == 'Pending') {
				setFlash(trim($pet['name']) . ' has been sent for review. It will appear on the site once an admin has approved it.');
			} else {
				setFlash(trim($pet['name']) . ' has been listed. Thank you!');
			}
			redirect('myaccount.php');
		}
	}

	$title = 'Rehome a pet';
	$output = loadTemplate('petform.html.php', [
		'title' => $title,
		'pet' => $pet,
		'errors' => $errors,
		'species' => findAll($pdo, 'species', 'name'),
		'shelters' => findAll($pdo, 'shelter', 'name'),
		'canChooseShelter' => isAdmin(),                                   // only admins pick the shelter
		'fixedShelterId' => isShelterStaff() ? currentShelterId() : null,  // staff always list for their own shelter
		'statusOptions' => allowedPetStatuses(),                            // only 'Available' for a new pet
		'buttonText' => 'Add pet',
		'cancelUrl' => 'pets.php'
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
