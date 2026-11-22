<?php
// Admin review queue: approve or reject listings (controller).
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Generic update() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Moderation queue idea: listings are hidden until a person with authority has approved them
//     (pre-moderation) - see Wikipedia contributors (no date) Moderation system. https://en.wikipedia.org/wiki/Moderation_system
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('approvals.php');
		$pet = findById($pdo, 'pet', 'id', getValue($_POST, 'id', 0));
		$decision = getValue($_POST, 'decision');
		$note = getValue($_POST, 'reviewnote');

		if (!$pet) {
			setFlash('That pet could not be found.', 'error');
		} elseif ($decision == 'Approved') {
			update($pdo, 'pet', 'id', ['id' => $pet['id'], 'approval' => 'Approved', 'reviewnote' => null]);
			setFlash($pet['name'] . ' has been approved and is now on the public site.');
		} elseif ($decision == 'Rejected') {
			// The owner sees this text, so a reason is required
			if (strlen($note) < 5 || strlen($note) > 255) {
				setFlash('Please give a reason for rejecting ' . $pet['name'] . ' (5 to 255 characters).', 'error');
			} else {
				update($pdo, 'pet', 'id', ['id' => $pet['id'], 'approval' => 'Rejected', 'reviewnote' => $note]);
				setFlash($pet['name'] . ' has been rejected. The owner can see your reason and edit the listing.');
			}
		}
		redirect('approvals.php');
	}

	$title = 'Listings to review';
	$output = loadTemplate('admin/approvals.html.php', ['pets' => pendingPets($pdo)]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
