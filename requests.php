<?php
// Adoption requests for the pets I am responsible for (controller).
// Who sees a request: the owner of a private rehoming pet, or the staff of the pet's shelter.
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Generic update() adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Post / Redirect / Get pattern: PHP Group (no date) header(), PHP Manual. https://www.php.net/manual/en/function.header.php
//   - MDN Web Docs (no date) Using the Fetch API (JSON answer for js/main.js). https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
//   - OWASP (no date) Authorization Cheat Sheet (check permission on the server for every action). https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html
include 'includes/Functions.php';
requireLogin();

// Admins have their own page that shows every request
if (isAdmin()) {
	redirect('admin/requests.php');
}

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Two ways to be called: js/main.js (fetch -> JSON, the page does not reload) or a normal form post
		if (wantsJson()) {
			if (!verifyCsrfToken()) {
				jsonResponse(['ok' => false, 'message' => 'Security check failed. Please reload the page and try again.'], 403);
			}
		} else {
			requireCsrfToken('requests.php');
		}
		$request = findRequest($pdo, getValue($_POST, 'id', 0));
		$stage = getValue($_POST, 'action');

		// The permission and the move are checked again here: a hidden form field can be changed by anyone
		$error = null;
		$status = 200;
		if (!$request || !canHandleRequest($request)) {
			$error = 'You cannot change that request.';
			$status = 403;
		} else {
			$error = validateRequestMove($pdo, $request, $stage);   // null = the move is allowed
			$status = 422;
		}

		if ($error === null) {
			$message = moveRequest($pdo, $request, $stage);
			if (wantsJson()) {
				// Send the new card content so the page can swap it in without reloading
				$request = findRequest($pdo, $request['id']);
				jsonResponse([
					'ok' => true,
					'message' => $message,
					'fragments' => ['.js-request-main' => loadTemplate('requestcard.html.php', ['request' => $request])]
				]);
			}
			setFlash($message);
		} else {
			if (wantsJson()) {
				jsonResponse(['ok' => false, 'message' => $error], $status);
			}
			setFlash($error, 'error');
		}
		redirect('requests.php');
	}

	$title = 'Requests for my pets';
	$output = loadTemplate('requests.html.php', ['requests' => requestsToHandle($pdo, currentUserId(), currentShelterId())]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
