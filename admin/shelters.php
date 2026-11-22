<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic insert() / update() / delete() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - Foreign key error caught as PDOException: MySQL (no date) FOREIGN KEY Constraints, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	$shelter = ['id' => '', 'name' => '', 'city' => '', 'email' => '', 'phone' => '', 'verified' => 0];
	$errors = [];

	// Open a shelter in the form for editing (shelters.php?edit=2)
	if (isset($_GET['edit'])) {
		$shelter = findById($pdo, 'shelter', 'id', getValue($_GET, 'edit', 0)) ?: $shelter;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('shelters.php');
	}

	if (isset($_POST['delete'])) {
		try {
			delete($pdo, 'shelter', 'id', getValue($_POST, 'delete', 0));
			setFlash('The shelter has been deleted.');
		} catch (PDOException $e) {
			// The foreign key stops us deleting a shelter that still has pets
			setFlash('This shelter still has pets, so it cannot be deleted. Move or delete its pets first.', 'error');
		}
		redirect('shelters.php');
	}

	if (isset($_POST['shelter'])) {
		$shelter = [];
		foreach (['id', 'name', 'city', 'email', 'phone', 'verified'] as $key) {
			$shelter[$key] = getValue($_POST['shelter'], $key);
		}

		if ($shelter['name'] == '' || strlen($shelter['name']) > 100) {
			$errors[] = 'Please enter the shelter name (up to 100 characters).';
		}
		if ($shelter['city'] == '' || strlen($shelter['city']) > 100) {
			$errors[] = 'Please enter the city.';
		}
		if (!filter_var($shelter['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Please enter a valid email address.';
		}
		if (!preg_match('/^[0-9 +()-]{6,30}$/', $shelter['phone'])) {
			$errors[] = 'Please enter a valid phone number.';
		}

		if (empty($errors)) {
			$fields = [
				'name' => $shelter['name'],
				'city' => $shelter['city'],
				'email' => $shelter['email'],
				'phone' => $shelter['phone'],
				// A tick box is only sent when it is ticked
				'verified' => $shelter['verified'] != '' ? 1 : 0
			];
			// The same form is used to add and to edit
			if ($shelter['id'] == '') {
				insert($pdo, 'shelter', $fields);
				setFlash($shelter['name'] . ' has been added.');
			} else {
				$fields['id'] = $shelter['id'];
				update($pdo, 'shelter', 'id', $fields);
				setFlash($shelter['name'] . ' has been updated.');
			}
			redirect('shelters.php');
		}
	}

	$title = 'Manage shelters';
	$output = loadTemplate('admin/shelters.html.php', [
		'shelters' => sheltersWithPetCount($pdo),
		'shelter' => $shelter,
		'errors' => $errors
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
