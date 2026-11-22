<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Generic update() / delete() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('users.php');
		$user = findById($pdo, 'user', 'id', getValue($_POST, 'id', 0));

		// An admin cannot remove or demote their own account
		if (!$user || $user['id'] == currentUserId()) {
			setFlash('You cannot change your own account here.', 'error');
		} elseif (isset($_POST['delete'])) {
			// Same function a member uses to delete their own account. When an ADMIN deletes an account, the
			// private pets of that person stay online (no owner) and the admin answers their requests.
			deleteUserAccount($pdo, $user['id'], false);
			setFlash($user['name'] . ' has been deleted.');
		} elseif (isset($_POST['assignshelter'])) {
			// Linking a person to a shelter makes them that shelter's staff. Nobody can do this to themselves,
			// so an admin checking the person really works there is the verification step.
			$shelterId = getValue($_POST, 'shelterid');
			$shelter = $shelterId != '' ? findById($pdo, 'shelter', 'id', $shelterId) : null;
			if ($user['role'] == 'admin') {
				setFlash('Admins do not work for a shelter. Make them a member first.', 'error');
			} elseif ($shelterId != '' && !$shelter) {
				setFlash('That shelter could not be found.', 'error');
			} else {
				update($pdo, 'user', 'id', ['id' => $user['id'], 'shelterid' => $shelter ? $shelter['id'] : null]);
				setFlash($shelter ? $user['name'] . ' is now staff of ' . $shelter['name'] . '.' : $user['name'] . ' no longer works for a shelter.');
			}
		} elseif (isset($_POST['role']) && in_array($_POST['role'], ['member', 'admin'])) {
			// An admin works for PawsHome, not for a shelter, so the shelter link is removed
			update($pdo, 'user', 'id', ['id' => $user['id'], 'role' => $_POST['role'], 'shelterid' => null]);
			setFlash($user['name'] . ' is now ' . ($_POST['role'] == 'admin' ? 'an admin' : 'a member') . '.');
		}
		redirect('users.php');
	}

	$title = 'Manage users';
	// Each user with the name of their shelter (LEFT JOIN: most users have none)
	$users = query($pdo, 'SELECT user.*, shelter.name AS sheltername FROM user
		LEFT JOIN shelter ON shelter.id = user.shelterid ORDER BY user.name')->fetchAll();
	$output = loadTemplate('admin/users.html.php', ['users' => $users, 'shelters' => findAll($pdo, 'shelter', 'name')]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
