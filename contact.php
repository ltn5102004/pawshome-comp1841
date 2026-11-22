<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Sending the email adapted from PHP Group (no date) mail() - Example #2 Sending mail with extra headers, PHP Manual. https://www.php.net/manual/en/function.mail.php
//   - PHP Group (no date) filter_var with FILTER_VALIDATE_EMAIL, PHP Manual. https://www.php.net/manual/en/filter.examples.validation.php
//   - Header injection check: OWASP (no date) Input Validation Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html
include 'includes/Functions.php';

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	$contact = ['name' => '', 'email' => '', 'subject' => '', 'body' => ''];
	$errors = [];

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		requireCsrfToken('contact.php');
		foreach ($contact as $key => $value) {
			$contact[$key] = getValue($_POST, $key);
		}

		if ($contact['name'] == '' || strlen($contact['name']) > 100) {
			$errors[] = 'Please enter your name.';
		}
		if (!filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Please enter a valid email address.';
		}
		if ($contact['subject'] == '' || strlen($contact['subject']) > 150) {
			$errors[] = 'Please enter a subject (up to 150 characters).';
		}
		if (strlen($contact['body']) < 10 || strlen($contact['body']) > 2000) {
			$errors[] = 'Your message must be between 10 and 2000 characters.';
		}
		// New lines in the name or subject could be used to add extra email headers
		if (preg_match('/[\r\n]/', $contact['name'] . $contact['subject'])) {
			$errors[] = 'The name and subject cannot contain line breaks.';
		}

		if (empty($errors)) {
			// Keep a copy in the database so the admin can read it in the admin area
			insert($pdo, 'message', [
				'name' => $contact['name'],
				'email' => $contact['email'],
				'subject' => $contact['subject'],
				'body' => $contact['body'],
				'datesent' => new DateTime()
			]);

			// Send the email to the administrator (PHP mail() as shown in the week 9 lecture)
			$message = "Name: " . $contact['name'] . "\n";
			$message .= "Email: " . $contact['email'] . "\n\n";
			$message .= $contact['body'];
			$headers = 'From: ' . ADMIN_EMAIL . "\r\n" . 'Reply-To: ' . $contact['email'];
			$sent = @mail(ADMIN_EMAIL, 'PawsHome contact: ' . $contact['subject'], $message, $headers);

			if ($sent) {
				setFlash('Thank you, your message has been sent to the administrator.');
			} else {
				setFlash('Thank you, your message has been received. (The email server is not set up on this machine, so it was saved for the administrator instead.)');
			}
			redirect('contact.php');
		}
	} elseif (isLoggedIn()) {
		// Fill in the name for logged in members
		$user = findById($pdo, 'user', 'id', currentUserId());
		$contact['name'] = $user['name'];
		$contact['email'] = $user['email'];
	}

	$title = 'Contact';
	$output = loadTemplate('contact.html.php', ['contact' => $contact, 'errors' => $errors]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
