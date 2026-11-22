<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	$title = 'Manage pets';
	$output = loadTemplate('admin/pets.html.php', ['pets' => allPets($pdo, ['approval' => 'any'])]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
