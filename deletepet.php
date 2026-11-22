<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic delete() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) unlink, PHP Manual (delete the image file). https://www.php.net/manual/en/function.unlink.php
//   - PHP Group (no date) header() - redirect with Location then exit, PHP Manual. https://www.php.net/manual/en/function.header.php
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	// Delete only works from a form (POST), never from a link
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		redirect('pets.php');
	}
	requireCsrfToken('pets.php');

	$pet = findById($pdo, 'pet', 'id', getValue($_POST, 'id', 0));

	if ($pet && canManagePet($pet)) {
		delete($pdo, 'pet', 'id', $pet['id']);
		// Remove the photo from the server as well
		deleteImage($pet['image']);
		setFlash($pet['name'] . ' has been deleted.');
	} else {
		setFlash('You cannot delete that pet.', 'error');
	}

	// Go back to the page the user came from (admin list or my account)
	$back = $_POST['back'] ?? 'myaccount.php';
	redirect($back == 'admin' ? 'admin/pets.php' : 'myaccount.php');
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
