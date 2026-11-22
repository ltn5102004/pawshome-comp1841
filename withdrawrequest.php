<?php
// An adopter withdraws their OWN adoption request (controller) - LIM-04 fix, 22 Nov 2026.
// POST + CSRF only. Nobody can withdraw somebody else's request. If the request had been approved, the pet is
// freed again (Reserved -> Available) unless another request for it is approved. Once the pet has actually been
// adopted the request is history and cannot be withdrawn.
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
include 'includes/Functions.php';
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		redirect('myaccount.php');
	}
	requireCsrfToken('myaccount.php');

	$request = findRequest($pdo, getValue($_POST, 'id', 0));
	if (!$request || $request['userid'] != currentUserId()) {
		setFlash('You can only withdraw your own adoption requests.', 'error');
	} elseif ($request['status'] == 'Approved' && $request['petstatus'] == 'Adopted') {
		setFlash($request['petname'] . ' has already been adopted, so this request can no longer be withdrawn.', 'error');
	} else {
		delete($pdo, 'adoptionrequest', 'id', $request['id']);
		if ($request['status'] == 'Approved') {
			freeReservedPet($pdo, $request['petid']);
		}
		setFlash('Your request for ' . $request['petname'] . ' has been withdrawn.');
	}
	redirect('myaccount.php#my-requests');
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
