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
		// js/main.js calls this with fetch (JSON answer, the page does not reload); a normal form post also works
		if (wantsJson()) {
			if (!verifyCsrfToken()) {
				jsonResponse(['ok' => false, 'message' => 'Security check failed. Please reload the page and try again.'], 403);
			}
		} else {
			requireCsrfToken('requests.php');
		}
		$request = findRequest($pdo, getValue($_POST, 'id', 0));
		$action = getValue($_POST, 'action');

		if ($request && $action == 'delete') {
			delete($pdo, 'adoptionrequest', 'id', $request['id']);
			// If this request was the reason the pet was Reserved, the pet is free again
			freeReservedPet($pdo, $request['petid']);
			setFlash('The request has been deleted.');
		} elseif (!$request) {
			if (wantsJson()) {
				jsonResponse(['ok' => false, 'message' => 'That request could not be found.'], 404);
			}
			setFlash('That request could not be found.', 'error');
		} else {
			// Same rules and same function the owners and shelter staff use (see validateRequestMove / moveRequest)
			$error = validateRequestMove($pdo, $request, $action);
			if ($error === null) {
				$message = moveRequest($pdo, $request, $action);
				if (wantsJson()) {
					$request = findRequest($pdo, $request['id']);
					jsonResponse([
						'ok' => true,
						'message' => $message,
						'fragments' => [
							'.js-status' => '<span class="badge badge-' . strtolower(html($request['status'])) . '">' . html($request['status']) . '</span>',
							'.js-actions' => loadTemplate('admin/requestactions.html.php', ['request' => $request])
						]
					]);
				}
				setFlash($message);
			} else {
				if (wantsJson()) {
					jsonResponse(['ok' => false, 'message' => $error], 422);
				}
				setFlash($error, 'error');
			}
		}
		redirect('requests.php');
	}

	$title = 'Adoption requests';
	$output = loadTemplate('admin/requests.html.php', ['requests' => allRequests($pdo)]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
