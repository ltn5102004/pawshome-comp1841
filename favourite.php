<?php
// Save / unsave a favourite pet (controller).
// Works in two ways: a normal form post (redirect + message at the top of the page) and a fetch request from
// js/main.js (JSON answer, no page reload, the message appears under the heart button).
// Source:
//   - Controller structure from COMP1841 lab materials; generic insert() adapted from Butler and Yank (2017);
//     many-to-many join table idea adapted from Butler and Yank (2017) (joke_category table).
//   - MDN Web Docs (no date) Using the Fetch API. https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
//   - PHP Group (no date) json_encode, PHP Manual (see jsonResponse in includes/Functions.php). https://www.php.net/manual/en/function.json-encode.php
include 'includes/Functions.php';

// A fetch request cannot follow a redirect to the login page, so it gets a JSON answer instead
if (wantsJson() && !isLoggedIn()) {
	jsonResponse(['ok' => false, 'message' => 'Please log in to save favourites.'], 401);
}
requireLogin();

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	// Saving or removing a favourite changes data, so it only works from a form (POST)
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		redirect('myaccount.php');
	}

	$back = safeBackUrl(getValue($_POST, 'back'), 'myaccount.php');
	if (wantsJson()) {
		if (!verifyCsrfToken()) {
			jsonResponse(['ok' => false, 'message' => 'Security check failed. Please reload the page and try again.'], 403);
		}
	} else {
		requireCsrfToken($back);
	}

	$pet = findById($pdo, 'pet', 'id', getValue($_POST, 'petid', 0));
	$ok = true;
	$saved = false;

	if (!$pet || $pet['approval'] != 'Approved') {
		$ok = false;
		$message = 'That pet could not be found.';
	} elseif (isFavourite($pdo, currentUserId(), $pet['id'])) {
		// Already saved, so the button works as "remove"
		removeFavourite($pdo, currentUserId(), $pet['id']);
		$message = $pet['name'] . ' has been removed from your favourites.';
	} else {
		insert($pdo, 'favourite', [
			'userid' => currentUserId(),
			'petid' => $pet['id'],
			'datesaved' => new DateTime()
		]);
		$saved = true;
		$message = $pet['name'] . ' has been saved to your favourites.';
	}

	if (wantsJson()) {
		jsonResponse([
			'ok' => $ok,
			'saved' => $saved,
			'message' => $message,
			'petname' => $pet ? $pet['name'] : '',
			'count' => $pet ? (int)favouriteCount($pdo, $pet['id']) : 0   // the new number of hearts
		], $ok ? 200 : 404);
	}

	setFlash($message, $ok ? 'success' : 'error');
	redirect($back);
} catch (PDOException $e) {
	if (wantsJson()) {
		jsonResponse(['ok' => false, 'message' => 'Sorry, something went wrong. Please try again.'], 500);
	}
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
