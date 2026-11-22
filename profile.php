<?php
// Edit profile / change password (controller).
// Source: PHP Group (no date) password_verify, password_hash and session_regenerate_id, PHP Manual;
//         PHP Group (no date) filter_var with FILTER_VALIDATE_EMAIL, PHP Manual; controller structure from COMP1841 lab materials.
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$user = findById($pdo, 'user', 'id', currentUserId());
	$detailErrors = [];
	$passwordErrors = [];
	$deleteErrors = [];

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('profile.php');
		// The page has three forms (details, password, delete account). A hidden "form" field tells us which one was sent.
		$form = getValue($_POST, 'form');

		if ($form == 'details') {
			$user['name'] = getValue($_POST, 'name');
			$user['email'] = getValue($_POST, 'email');

			if ($user['name'] == '' || strlen($user['name']) > 100) {
				$detailErrors[] = 'Please enter your name (up to 100 characters).';
			}
			if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
				$detailErrors[] = 'Please enter a valid email address.';
			} else {
				// The email must not belong to a different account
				$other = findUserByEmail($pdo, $user['email']);
				if ($other && $other['id'] != $user['id']) {
					$detailErrors[] = 'Another account already uses that email address.';
				}
			}

			if (empty($detailErrors)) {
				update($pdo, 'user', 'id', ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]);
				setFlash('Your details have been updated.');
				redirect('myaccount.php');
			}
		} elseif ($form == 'password') {
			// Ask for the current password so nobody can change it on a computer left logged in
			if (!password_verify(getValue($_POST, 'current', '', false), $user['password'])) {
				$passwordErrors[] = 'Your current password is incorrect.';
			}
			$newPassword = getValue($_POST, 'password', '', false);
			$passwordErrors = array_merge($passwordErrors, validatePassword($newPassword, getValue($_POST, 'confirm', '', false)));

			if (empty($passwordErrors)) {
				update($pdo, 'user', 'id', ['id' => $user['id'], 'password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
				session_regenerate_id(true);
				setFlash('Your password has been changed.');
				redirect('myaccount.php');
			}
		} elseif ($form == 'delete') {
			// A member may delete their own account. Admins may not: there must always be at least one admin,
			// so another admin removes an admin account (see admin/users.php).
			if (isAdmin()) {
				$deleteErrors[] = 'Admin accounts cannot be deleted here. Ask another admin to remove the account.';
			} else {
				// The password is asked again, so nobody can delete the account on a computer that was left logged in
				if (!password_verify(getValue($_POST, 'current', '', false), $user['password'])) {
					$deleteErrors[] = 'Your password is incorrect.';
				}
				if (getValue($_POST, 'confirmdelete') == '') {
					$deleteErrors[] = 'Please tick the box to confirm that you want to delete your account.';
				}
				if (empty($deleteErrors)) {
					deleteUserAccount($pdo, $user['id'], true);   // true = the member's private pets are deleted with them
					logOut();
					setFlash('Your account has been deleted. We are sorry to see you go.');
					redirect('index.php');
				}
			}
		}
	}

	$title = 'Edit profile';
	$output = loadTemplate('profile.html.php', [
		'user' => $user,
		'detailErrors' => $detailErrors,
		'passwordErrors' => $passwordErrors,
		'deleteErrors' => $deleteErrors,
		'summary' => accountSummary($pdo, $user['id'])   // what would be deleted, shown before the person decides
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
