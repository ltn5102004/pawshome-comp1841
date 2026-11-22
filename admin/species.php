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

	$item = ['id' => '', 'name' => ''];
	$errors = [];

	if (isset($_GET['edit'])) {
		$item = findById($pdo, 'species', 'id', getValue($_GET, 'edit', 0)) ?: $item;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('species.php');
	}

	if (isset($_POST['delete'])) {
		try {
			delete($pdo, 'species', 'id', getValue($_POST, 'delete', 0));
			setFlash('The species has been deleted.');
		} catch (PDOException $e) {
			setFlash('Some pets still use this species, so it cannot be deleted.', 'error');
		}
		redirect('species.php');
	}

	if (isset($_POST['species'])) {
		$item = ['id' => getValue($_POST['species'], 'id'), 'name' => getValue($_POST['species'], 'name')];

		if ($item['name'] == '' || strlen($item['name']) > 50) {
			$errors[] = 'Please enter a species name (up to 50 characters).';
		}

		if (empty($errors)) {
			try {
				if ($item['id'] == '') {
					insert($pdo, 'species', ['name' => $item['name']]);
					setFlash($item['name'] . ' has been added.');
				} else {
					update($pdo, 'species', 'id', ['id' => $item['id'], 'name' => $item['name']]);
					setFlash($item['name'] . ' has been updated.');
				}
				redirect('species.php');
			} catch (PDOException $e) {
				// The name column is UNIQUE
				$errors[] = 'That species already exists.';
			}
		}
	}

	$title = 'Manage species';
	$output = loadTemplate('admin/species.html.php', [
		'speciesList' => speciesWithPetCount($pdo),
		'item' => $item,
		'errors' => $errors
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
