<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic update() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - Image upload adapted from W3Schools (no date) PHP File Upload. https://www.w3schools.com/php/php_file_upload.asp
//   - PHP Group (no date) header() - redirect with Location then exit, PHP Manual. https://www.php.net/manual/en/function.header.php
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$id = isset($_POST['pet']) ? getValue($_POST['pet'], 'id', 0) : getValue($_GET, 'id', 0);
	$saved = findById($pdo, 'pet', 'id', $id);

	// Stop if the pet does not exist or belongs to someone else
	if (!$saved || !canManagePet($saved)) {
		setFlash('You cannot edit that pet.', 'error');
		redirect('pets.php');
	}

	$pet = $saved;
	$errors = [];

	if (isset($_POST['pet'])) {
		requireCsrfToken('editpet.php?id=' . $id);
		$pet = getValues($_POST['pet'], petFormKeys());
		$pet['image'] = $saved['image'];
		// The server decides the shelter; a non-admin cannot move a pet to another shelter
		$place = listingPlace($pet, $saved);
		$errors = validatePet($pdo, $pet, $place, $saved);
		// A pet with an approved adoption request cannot simply be made Available again: withdraw the request first
		// (the Requests page then frees the pet by itself)
		if (empty($errors) && $pet['status'] == 'Available' && $saved['status'] != 'Available' && hasApprovedRequest($pdo, $saved['id'])) {
			$errors[] = 'An adoption request for this pet has been approved. Withdraw that request on the Requests page and the pet becomes Available again.';
		}

		$image = $saved['image'];
		// The photo credit belongs to the old photo, so it is cleared when the photo changes
		$imageCredit = $saved['imagecredit'];
		$imageSource = $saved['imagesource'];
		if (empty($errors)) {
			$upload = uploadImage($_FILES['image'] ?? null);
			if ($upload['error']) {
				$errors[] = $upload['error'];
			} elseif ($upload['filename']) {
				// A new photo replaces the old one
				deleteImage($saved['image']);
				$image = $upload['filename'];
				$imageCredit = null;
				$imageSource = null;
			} elseif (isset($_POST['removeimage'])) {
				deleteImage($saved['image']);
				$image = null;
				$imageCredit = null;
				$imageSource = null;
			}
		}

		if (empty($errors)) {
			// petFields() turns the form values into database columns (see includes/Functions.php)
			$fields = petFields($pet, $place);
			$fields['id'] = $saved['id'];
			$fields['image'] = $image;
			$fields['imagecredit'] = $imageCredit;
			$fields['imagesource'] = $imageSource;
			// Editing sends a member's listing back for review (an admin's edit keeps the current decision)
			$fields['approval'] = approvalForSave($saved['approval'], $place['shelterid']);
			// A new review starts with no note; an admin's edit keeps the note that is already there
			$fields['reviewnote'] = isAdmin() ? $saved['reviewnote'] : null;
			update($pdo, 'pet', 'id', $fields);
			if ($fields['approval'] == 'Pending' && $saved['approval'] == 'Approved') {
				setFlash(trim($pet['name']) . ' has been updated and sent for review again.');
			} elseif ($fields['approval'] == 'Pending') {
				setFlash(trim($pet['name']) . ' has been updated and is waiting for review.');
			} else {
				setFlash(trim($pet['name']) . ' has been updated.');
			}
			redirect('pet.php?id=' . $saved['id']);
		}
	}

	$title = 'Edit ' . $saved['name'];
	$output = loadTemplate('petform.html.php', [
		'title' => $title,
		'pet' => $pet,
		'errors' => $errors,
		'species' => findAll($pdo, 'species', 'name'),
		'shelters' => findAll($pdo, 'shelter', 'name'),
		'canChooseShelter' => isAdmin(),
		'fixedShelterId' => $saved['shelterid'],   // shown as plain text for everyone except admins
		'statusOptions' => allowedPetStatuses($saved),
		'buttonText' => 'Save changes',
		'cancelUrl' => 'pet.php?id=' . $saved['id']
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
