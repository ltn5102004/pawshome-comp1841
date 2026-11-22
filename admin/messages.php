<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic delete() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	if (isset($_POST['delete'])) {
		requireCsrfToken('messages.php');
		delete($pdo, 'message', 'id', getValue($_POST, 'delete', 0));
		setFlash('The message has been deleted.');
		redirect('messages.php');
	}

	$messages = query($pdo, 'SELECT * FROM message ORDER BY datesent DESC')->fetchAll();

	$title = 'Contact messages';
	$output = loadTemplate('admin/messages.html.php', ['messages' => $messages]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
