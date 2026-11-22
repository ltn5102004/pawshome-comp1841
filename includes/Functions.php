<?php
// General helper functions used by the controllers (not database related)

// Source: PHP Group (no date) session_status and session_start, PHP Manual. https://www.php.net/manual/en/function.session-status.php
// Start the session once so we can remember who is logged in
// The session cookie is HttpOnly (JavaScript cannot read it, so an XSS bug cannot steal the login) and SameSite=Lax
// (the browser does not send it with requests started by other websites, which helps against CSRF).
// "secure" is added automatically when the site is opened with https.
// Source: PHP Group (no date) session_set_cookie_params, PHP Manual. https://www.php.net/manual/en/function.session-set-cookie-params.php
//         OWASP (no date) Session Management Cheat Sheet - cookie attributes. https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html
if (session_status() === PHP_SESSION_NONE) {
	session_set_cookie_params([
		'httponly' => true,
		'samesite' => 'Lax',
		'secure' => !empty($_SERVER['HTTPS'])
	]);
	session_start();
}

// Use the local time zone for every date and time the site saves. PHP's default on this machine was
// Europe/Berlin, 6 hours behind the computer's clock, so saved times looked wrong.
// Source: PHP Group (no date) date_default_timezone_set, PHP Manual. https://www.php.net/manual/en/function.date-default-timezone-set.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Change this to the real system administrator address
define('ADMIN_EMAIL', 'admin@pawshome.test');   // placeholder for the public repo - put the real admin address here when deploying
// Folder where pet images are saved (must be writable by the web server)
define('UPLOAD_DIR', __DIR__ . '/../images/pets/');

// Source: OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (synchronizer token pattern);
//         PHP Group (no date) random_bytes, bin2hex and hash_equals, PHP Manual.
##SECURITY & CSRF PROTECTION##

// Generates or returns the existing CSRF token for the session
function csrfToken() {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

// Generates a hidden input HTML element with the CSRF token
function csrfField() {
	return '<input type="hidden" name="csrf_token" value="' . html(csrfToken()) . '">';
}

// Verifies that the POSTed CSRF token matches the session token
function verifyCsrfToken($token = null) {
	$token = $token ?? $_POST['csrf_token'] ?? '';
	return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Stops the request with HTTP 403 Forbidden if a POSTed form has no valid CSRF token.
// Fix for DEF-01 (found by test SEC-06, 22 Nov 2026): this used to call http_response_code(403) and then redirect(),
// but header('Location: ...') changes the status to 302, so the 403 was never sent. Now a small 403 page is shown
// with a link back to the form; the back link only allows our own pages (safeBackUrl), never the Referer of another site.
// Source: PHP Group (no date) header - "the Location header ... returns a REDIRECT (302) status code". https://www.php.net/manual/en/function.header.php
//         OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
function requireCsrfToken($redirectBack = null) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (!verifyCsrfToken()) {
			http_response_code(403);
			$back = safeBackUrl($redirectBack ?? '', 'index.php');
			$base = basename(dirname($_SERVER['SCRIPT_NAME'])) == 'admin' ? '../' : '';
			include __DIR__ . '/../templates/forbidden.html.php';
			exit();
		}
	}
}

##JSON RESPONSES - for the small requests made by JavaScript (fetch)##
// Source: PHP Group (no date) json_encode and http_response_code, PHP Manual. https://www.php.net/manual/en/function.json-encode.php
//         MDN Web Docs (no date) Accept header. https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept

// True when the browser asked for JSON (js/main.js sends "Accept: application/json").
// A normal form post asks for HTML, so it keeps working when JavaScript is switched off.
function wantsJson() {
	return strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
}

// Sends one JSON answer and stops the script
function jsonResponse($data, $status = 200) {
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data);
	exit();
}

##OUTPUT##

// Source: Butler and Yank (2017) PHP & MySQL: Novice to Ninja; PHP Group (no date) htmlspecialchars, PHP Manual.
// Escape text before printing it in HTML (stops XSS)
function html($text) {
	return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Source: adapted from the COMP1841 lab controller/template structure and Butler and Yank (2017) (output buffering with ob_start).
// Include a template and return its HTML as a string (replaces the ob_start / ob_get_clean lines in each controller)
function loadTemplate($templateFile, $variables = []) {
	extract($variables);
	ob_start();
	include __DIR__ . '/../templates/' . $templateFile;
	return ob_get_clean();
}

// Source: PHP Group (no date) isset, is_array and trim, PHP Manual. Written for this project to stop "?id[]=1" errors.
##INPUT##

// Read one text value from $_GET or $_POST, e.g. getValue($_GET, 'id', 0)
// Returns the default if the key is missing or someone sends an array (?id[]=1) instead of text
// Set $trim to false for passwords so spaces the user typed are kept
function getValue($source, $key, $default = '', $trim = true) {
	if (!isset($source[$key]) || is_array($source[$key])) {
		return $default;
	}
	return $trim ? trim($source[$key]) : $source[$key];
}

// Read several fields at once, e.g. getValues($_POST['pet'], ['name', 'age'])
function getValues($source, $keys) {
	$values = [];
	foreach ($keys as $key) {
		$values[$key] = getValue(is_array($source) ? $source : [], $key);
	}
	return $values;
}

// Source: MySQL (no date) SELECT Statement - LIMIT and OFFSET, MySQL 8.0 Reference Manual;
//         PHP Group (no date) ceil, http_build_query and $_SERVER, PHP Manual.
##PAGINATION - generic, works for any list##

// Works out which page to show. Returns the numbers needed for LIMIT / OFFSET and for the page links.
function paginate($totalItems, $perPage, $currentPage) {
	$totalPages = max(1, (int)ceil($totalItems / $perPage));
	$currentPage = (int)$currentPage;
	// Keep the page number between 1 and the last page (so ?page=999 or ?page=-1 still work)
	$currentPage = min(max(1, $currentPage), $totalPages);

	return [
		'page' => $currentPage,
		'pages' => $totalPages,
		'perPage' => $perPage,
		'offset' => ($currentPage - 1) * $perPage,
		'total' => $totalItems
	];
}

// Link to another page that keeps the current search filters, e.g. pets.php?speciesid=2&page=3
function pageUrl($page) {
	$parameters = $_GET;
	$parameters['page'] = $page;
	return basename($_SERVER['SCRIPT_NAME']) . '?' . http_build_query($parameters);
}

// The page being shown now, with its query string (e.g. "pets.php?page=2"), used for "go back" links
function currentPage() {
	$page = basename($_SERVER['SCRIPT_NAME']);
	if (!empty($_SERVER['QUERY_STRING'])) {
		$page .= '?' . $_SERVER['QUERY_STRING'];
	}
	return $page;
}

// Source: OWASP (no date) Unvalidated Redirects and Forwards Cheat Sheet (allow-list of local pages);
//         PHP Group (no date) preg_match, PHP Manual.
// Only allow redirects to pages on this site (e.g. "pets.php?page=2"), never to another website
function safeBackUrl($url, $default = 'index.php') {
	if (preg_match('/^[a-z]+\.php(\?[A-Za-z0-9_=&%+.\-\[\]]*)?$/', $url)) {
		return $url;
	}
	return $default;
}

// Source: PHP Group (no date) filemtime, PHP Manual (cache busting with a version query string).
// Adds the file's last-modified time to a CSS / JS link (style.css?v=1762160000)
// so the browser downloads the new version after every change instead of using an old cached copy
function asset($path, $base = '') {
	$file = __DIR__ . '/../' . $path;
	$version = file_exists($file) ? filemtime($file) : 1;
	return $base . $path . '?v=' . $version;
}

// Source: PHP Group (no date) date and strtotime, PHP Manual. https://www.php.net/manual/en/function.date.php
function formatDate($date) {
	return date('j M Y', strtotime($date));
}

// Source: PHP Group (no date) file_exists, PHP Manual. https://www.php.net/manual/en/function.file-exists.php
// Path to a pet image, or the placeholder if the pet has none
function imagePath($image, $base = '') {
	if ($image != '' && file_exists(UPLOAD_DIR . $image)) {
		return $base . 'images/pets/' . $image;
	}
	return $base . 'images/no-image.svg';
}

// Source: PHP Group (no date) header() - "Location" header followed by exit, PHP Manual. https://www.php.net/manual/en/function.header.php
function redirect($url) {
	header('Location: ' . $url);
	exit();
}

// Source: PHP Group (no date) Sessions ($_SESSION), PHP Manual.
// Ends the login completely and starts a fresh empty session (only used to show one message on the next page).
// Used by logout.php and when a member deletes their own account.
// Source: adapted from PHP Group (no date) session_destroy - Example #1 "Destroying a session with $_SESSION", PHP Manual.
//         https://www.php.net/manual/en/function.session-destroy.php
function logOut() {
	// 1. Unset all of the session variables
	$_SESSION = [];
	// 2. Delete the session cookie as well (this destroys the session, not just the session data)
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	// 3. Finally, destroy the session, then start a new empty one with a new id
	session_destroy();
	session_start();
	session_regenerate_id(true);
}

##FLASH MESSAGES - a message shown once on the next page##

function setFlash($message, $type = 'success') {
	$_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
	if (isset($_SESSION['flash'])) {
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);
		return $flash;
	}
	return null;
}

// Source: PHP Group (no date) session_start and session_regenerate_id, PHP Manual; COMP1841 lab materials (login week).
##LOGIN AND PERMISSIONS##

// The logged in user, read from the DATABASE on every page (once per request).
// Only the user id is kept in the session. If the role were saved in the session as well, an admin who is
// demoted (or a deleted user) would keep their old rights until they log out.
// Source: OWASP (no date) Session Management Cheat Sheet - keep only an identifier in the session and
//         check authorisation on the server for every request. https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html
//         PHP Group (no date) static variables and global keyword, PHP Manual. https://www.php.net/manual/en/language.variables.scope.php
function currentUser() {
	static $user = false;   // false = not looked up yet, null = nobody logged in
	if ($user !== false) {
		return $user;
	}
	$user = null;
	if (isset($_SESSION['userid'])) {
		global $pdo;
		// Open the database connection here if the page has not done it yet
		include_once __DIR__ . '/DatabaseConnection.php';
		// LEFT JOIN: most people are not shelter staff, so they have no shelter row
		$query = $pdo->prepare('SELECT user.id, user.name, user.email, user.role, user.shelterid, user.datejoined,
				shelter.name AS sheltername, shelter.verified AS shelterverified
			FROM user LEFT JOIN shelter ON shelter.id = user.shelterid
			WHERE user.id = :id');
		$query->execute([':id' => $_SESSION['userid']]);
		$user = $query->fetch() ?: null;
		if ($user === null) {
			// The account was deleted while the person was logged in
			unset($_SESSION['userid']);
		}
	}
	return $user;
}

function isLoggedIn() {
	return currentUser() !== null;
}

function isAdmin() {
	return isLoggedIn() && currentUser()['role'] == 'admin';
}

// Shelter staff = a member whose account an admin has linked to a shelter (user.shelterid).
// There is no extra role: one column is the only source of truth, so role and shelter can never disagree.
function currentShelterId() {
	return isLoggedIn() ? currentUser()['shelterid'] : null;
}

function isShelterStaff() {
	return currentShelterId() !== null;
}

// (isAdmin and currentUserId read the user from the database, see currentUser above)
function currentUserId() {
	return isLoggedIn() ? currentUser()['id'] : null;
}

// Send visitors who are not logged in to the login page
function requireLogin($loginPage = 'login.php') {
	if (!isLoggedIn()) {
		setFlash('Please log in to continue.', 'error');
		redirect($loginPage);
	}
}

// Only admins may see the admin area
function requireAdmin($loginPage = '../login.php') {
	requireLogin($loginPage);
	if (!isAdmin()) {
		setFlash('Sorry, only administrators can open that page.', 'error');
		redirect('../index.php');
	}
}

// Source: own permission check using the session (COMP1841 lab materials, login week).
// Which approval a listing gets when it is saved.
//  - A NEW listing by an admin is approved straight away.
//  - An admin who EDITS a listing keeps the decision it already has (fixing a typo must not approve it).
//  - Everything else, including a member EDITING an approved listing, goes (back) to Pending,
//    so nobody can get harmless text approved and then change it afterwards.
// $current = the approval the listing has now, or null for a new listing.
//  - Staff of a VERIFIED shelter, working on their own shelter's pet, are trusted: new listings are approved at
//    once and edits keep an approved listing approved. They cannot overrule an admin's rejection.
// $shelterId = the shelter the listing belongs to (null for a private rehoming).
function approvalForSave($current = null, $shelterId = null) {
	if (isAdmin()) {
		return $current ?? 'Approved';
	}
	if (isShelterStaff() && $shelterId == currentShelterId() && currentUser()['shelterverified']) {
		return ($current === null || $current == 'Approved') ? 'Approved' : 'Pending';
	}
	return 'Pending';
}

// Decides which shelter and city a listing gets. The server decides: a shelter sent from the form is only
// accepted from an admin, so a member can never post a pet under a shelter's name.
//   - admin:         the shelter chosen in the form (empty = private rehoming)
//   - shelter staff: always their own shelter
//   - member:        no shelter (private rehoming) and a city typed in the form
// When an existing pet ($saved) is edited by a non-admin, its shelter cannot be changed.
function listingPlace($submitted, $saved = null) {
	if (isAdmin()) {
		$shelterId = $submitted['shelterid'] != '' ? $submitted['shelterid'] : null;
	} elseif ($saved !== null) {
		$shelterId = $saved['shelterid'];
	} elseif (isShelterStaff()) {
		$shelterId = currentShelterId();
	} else {
		$shelterId = null;
	}
	return [
		'shelterid' => $shelterId,
		// a pet with a shelter uses the shelter's city, so the city is only stored for private rehoming
		'city' => $shelterId === null ? trim($submitted['city']) : null
	];
}

// Text for "where is this pet": "Greenwich Animal Rescue, London" or "Private owner, Oxford"
function placeLabel($pet) {
	$name = !empty($pet['sheltername']) ? $pet['sheltername'] : 'Private owner';
	return $name . ($pet['sheltercity'] ? ', ' . $pet['sheltercity'] : '');
}

// Can this visitor see this pet? Approved pets are public; a pet that is still waiting
// (or was rejected) can only be seen by the person who listed it and by admins.
function canViewPet($pet) {
	return $pet['approval'] == 'Approved' || canManagePet($pet);
}

// A pet can be edited or deleted by the member who posted it, or by an admin
// Who may edit / delete a pet: an admin, the member who posted it, or staff of the pet's own shelter
// (so a colleague can carry on when the person who posted it has left).
function canManagePet($pet) {
	return isAdmin()
		|| (isLoggedIn() && $pet['userid'] == currentUserId())
		|| (isShelterStaff() && !empty($pet['shelterid']) && $pet['shelterid'] == currentShelterId());
}

// Source: PHP Group (no date) filter_var with FILTER_VALIDATE_INT / FILTER_VALIDATE_EMAIL, PHP Manual.
##VALIDATION##

// Server side validation for the add / edit pet form - returns a list of error messages
// $place comes from listingPlace(): the shelter and city the server decided on
// $saved = the pet as it is now in the database when editing, or null for a new listing
function validatePet($pdo, $pet, $place, $saved = null) {
	$errors = [];

	$name = trim($pet['name'] ?? '');
	if ($name == '') {
		$errors[] = 'Please enter the pet\'s name.';
	} elseif (strlen($name) > 50) {
		$errors[] = 'The name must be 50 characters or fewer.';
	}

	$age = filter_var($pet['age'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 30]]);
	if ($age === false) {
		$errors[] = 'Age must be a whole number between 0 and 30.';
	}

	if (!in_array($pet['gender'] ?? '', ['Male', 'Female'])) {
		$errors[] = 'Please choose a gender.';
	}

	if (empty($pet['speciesid']) || !findById($pdo, 'species', 'id', $pet['speciesid'])) {
		$errors[] = 'Please choose a species.';
	}

	if ($place['shelterid'] !== null && !findById($pdo, 'shelter', 'id', $place['shelterid'])) {
		$errors[] = 'Please choose a valid shelter.';
	}
	// A private rehoming has no shelter, so we need to know where the pet is
	if ($place['shelterid'] === null && (strlen($place['city']) < 2 || strlen($place['city']) > 100)) {
		$errors[] = 'Please enter the city where the pet is (2 to 100 characters).';
	}

	$description = trim($pet['description'] ?? '');
	if (strlen($description) < 10) {
		$errors[] = 'The description must be at least 10 characters.';
	} elseif (strlen($description) > 2000) {
		$errors[] = 'The description must be 2000 characters or fewer.';
	}

	if (!in_array($pet['status'] ?? '', allowedPetStatuses($saved))) {
		$errors[] = 'Please choose a valid status.';
	}

	// Health and personality details
	if (strlen($pet['breed'] ?? '') > 80) {
		$errors[] = 'The breed must be 80 characters or fewer.';
	}
	if (!in_array($pet['size'] ?? '', petSizes())) {
		$errors[] = 'Please choose a size.';
	}
	if (!in_array($pet['energy'] ?? '', energyLevels())) {
		$errors[] = 'Please choose an energy level.';
	}
	if (strlen($pet['healthnotes'] ?? '') > 500) {
		$errors[] = 'The health notes must be 500 characters or fewer.';
	}

	return $errors;
}

// The words shown for a listing's review ("approval" column). They are NOT "Approved" / "Rejected" on purpose:
// those words are already used for adoption requests, and two different "Approved" next to each other is confusing.
//   Approved -> Published (the listing is on the public site)   Pending -> Waiting for review   Rejected -> Not approved
function reviewLabel($approval) {
	return ['Approved' => 'Published', 'Pending' => 'Waiting for review', 'Rejected' => 'Not approved'][$approval] ?? $approval;
}

##ICONS for the admin area##
// Small line icons (24 x 24) drawn for this project with basic SVG shapes. aria-hidden: the text next to the
// icon already says what it means, so screen readers should skip the picture.
// Source: MDN Web Docs (no date) SVG: Paths and basic shapes. https://developer.mozilla.org/en-US/docs/Web/SVG/Tutorial/Paths
function icon($name) {
	$shapes = [
		'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
		'review' => '<path d="M9 11l3 3 8-8"/><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9"/>',
		'pets' => '<circle cx="6.5" cy="10" r="2"/><circle cx="10" cy="5.5" r="2"/><circle cx="14" cy="5.5" r="2"/><circle cx="17.5" cy="10" r="2"/><path d="M7.5 18c0-3.2 2.1-5.5 4.5-5.5s4.5 2.3 4.5 5.5c0 1.6-1.5 2.5-4.5 2.5s-4.5-.9-4.5-2.5z"/>',
		'requests' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9z"/><path d="M9 11h6M9 15h4"/>',
		'shelters' => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-5h4v5"/>',
		'species' => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8" r="1.5"/>',
		'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.4-5.5 6.5-5.5s5.7 2 6.5 5.5"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.8c1.8.7 3 2.5 3.5 5.2"/>',
		'messages' => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 9h8M8 12h5"/>',
		'site' => '<path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		'logout' => '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4"/><path d="M10 16l-4-4 4-4"/><path d="M6 12h10"/>',
		'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/>',
		'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/>',
		'arrow' => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
		'plus' => '<path d="M12 5v14M5 12h14"/>',
		'heart' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
		'search' => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
		'user' => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c1-4 4-6 7.5-6s6.5 2 7.5 6"/>',
		'send' => '<path d="M21 3L10 14"/><path d="M21 3l-7 18-4-7-7-4z"/>'
	];
	return '<svg class="icon" aria-hidden="true" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
		. ($shapes[$name] ?? '') . '</svg>';
}

// Which kind of user is looking at "My account". Each kind gets its own colour and its own order of sections.
//   admin   - runs the site
//   staff   - linked to a shelter (user.shelterid)
//   owner   - a member who listed a pet or receives adoption requests
//   adopter - a member who is looking for a pet
function accountRole($ownsPets = false) {
	if (isAdmin()) {
		return 'admin';
	}
	if (isShelterStaff()) {
		return 'staff';
	}
	return $ownsPets ? 'owner' : 'adopter';
}

// Label and short explanation shown in the coloured banner for each kind of user
function roleInfo($role) {
	$roles = [
		'admin' => ['Administrator', 'You look after the whole PawsHome site.'],
		'staff' => ['Shelter staff', 'You list pets and answer adoption requests for your shelter.'],
		'owner' => ['Pet owner', 'You are rehoming a pet. Answer the people who want to adopt it.'],
		'adopter' => ['Adopter', 'You are looking for a new friend. Follow your requests here.']
	];
	return $roles[$role] ?? $roles['adopter'];
}

// First letters of the name for the round "avatar" in the banner (mb_ functions keep Vietnamese letters whole)
// Source: PHP Group (no date) mb_substr and mb_strtoupper, PHP Manual. https://www.php.net/manual/en/function.mb-substr.php
function initials($name) {
	$parts = preg_split('/\s+/', trim($name));
	$letters = mb_substr($parts[0] ?? '', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '');
	return mb_strtoupper($letters);
}

// Who listed a pet, for the admin pet list. A sample shelter pet has nobody linked to it, which is normal
// and different from a private pet whose owner deleted their account.
function listedByLabel($pet) {
	if (!empty($pet['postedby'])) {
		return $pet['postedby'];
	}
	return empty($pet['shelterid']) ? 'Owner account deleted' : 'The shelter';
}

##PET DETAILS - lists used by the forms, the filters and the validation##
// Source: PHP Group (no date) in_array, array_merge and array_keys, PHP Manual. https://www.php.net/manual/en/function.in-array.php
//         Tick boxes are only sent when ticked: MDN Web Docs (no date) <input type="checkbox">. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/checkbox
// Keeping each list in one function means the form, the filter and the check can never disagree.

// The status a person may choose in the add / edit form.
// The status follows the adoption process, so it is NOT free to choose:
//   - a new pet is always Available (it cannot be reserved or adopted before anyone has asked for it)
//   - Reserved is only set by the system when an adoption request is approved (see moveRequest), so it is
//     only offered while the pet already is Reserved
//   - when editing, the owner can mark the pet Adopted (it has gone to its new home) or Available again
// Source: own design based on the adoption stages in requestStages(); in_array() - PHP Group (no date) PHP Manual.
//         https://www.php.net/manual/en/function.in-array.php
function allowedPetStatuses($saved = null) {
	if ($saved === null) {
		return ['Available'];
	}
	return $saved['status'] == 'Reserved' ? ['Available', 'Reserved', 'Adopted'] : ['Available', 'Adopted'];
}

function petSizes() {
	return ['Small', 'Medium', 'Large'];
}

function energyLevels() {
	return ['Low', 'Medium', 'High'];
}

// Database column => label shown to the user
function compatibilityOptions() {
	return [
		'goodwithkids' => 'Good with children',
		'goodwithdogs' => 'Good with dogs',
		'goodwithcats' => 'Good with cats',
		'apartmentok' => 'Suits a flat'
	];
}

// Every field of the add / edit pet form (used with getValues)
function petFormKeys() {
	return array_merge(
		['id', 'name', 'age', 'gender', 'speciesid', 'shelterid', 'city', 'description', 'status',
		 'breed', 'size', 'energy', 'vaccinated', 'neutered', 'healthnotes'],
		array_keys(compatibilityOptions())
	);
}

// Turns the checked form values into the columns saved by insert() / update().
// A tick box is only sent when it is ticked, so "not sent" is saved as 0.
// $place comes from listingPlace() - the shelter in the form is never used directly
function petFields($pet, $place) {
	$fields = [
		'name' => trim($pet['name']),
		'age' => $pet['age'],
		'gender' => $pet['gender'],
		'description' => trim($pet['description']),
		'status' => $pet['status'],
		'speciesid' => $pet['speciesid'],
		'shelterid' => $place['shelterid'],
		'city' => $place['city'],
		'breed' => $pet['breed'] != '' ? $pet['breed'] : null,
		'size' => $pet['size'],
		'energy' => $pet['energy'],
		'vaccinated' => $pet['vaccinated'] != '' ? 1 : 0,
		'neutered' => $pet['neutered'] != '' ? 1 : 0,
		'healthnotes' => $pet['healthnotes'] != '' ? $pet['healthnotes'] : null
	];
	foreach (compatibilityOptions() as $column => $label) {
		$fields[$column] = $pet[$column] != '' ? 1 : 0;
	}
	return $fields;
}

// A pet with no shelter is a private rehoming (a member giving up their own pet).
// Showing this clearly helps adopters avoid people selling pets while pretending to rehome them.
function isPrivateRehoming($pet) {
	return empty($pet['shelterid']);
}

##ADOPTION REQUEST STAGES##
// Source: stages designed for this project, based on the rehoming process described by RSPCA (no date) Find a pet. https://www.rspca.org.uk/findapet

// The order matters: it is used to draw the progress tracker
function requestStages() {
	return ['New', 'Interviewing', 'Approved', 'Rejected'];
}

// Which stage a request may move to from where it is now, with the text of each button.
// A request goes New -> Interviewing -> Approved. It can be rejected at any point, and a decision can be undone.
// Only these moves are offered AND accepted by the server, so nobody can skip the interview or
// jump to a stage by editing the form.
// Source: finite state machine / allowed transitions table - Wikipedia contributors (no date) Finite-state machine.
//         https://en.wikipedia.org/wiki/Finite-state_machine
function requestActions($status) {
	$moves = [
		'New'          => ['Interviewing' => 'Start interview', 'Rejected' => 'Reject'],
		'Interviewing' => ['Approved' => 'Approve', 'Rejected' => 'Reject', 'New' => 'Back to new'],
		'Approved'     => ['Interviewing' => 'Withdraw approval', 'Rejected' => 'Reject'],
		'Rejected'     => ['New' => 'Reopen']
	];
	return $moves[$status] ?? [];
}

##WHO HANDLES ADOPTION REQUESTS##
// Source: own design - the person who can vouch for the pet decides (see handlerWhere in DatabaseFunctions.php).
//         Role-based access control: OWASP (no date) Authorization Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html

// Needs a row from requestSelect(). Admins can handle everything; otherwise only the owner of a private
// rehoming pet, or the staff of the pet's shelter.
function canHandleRequest($request) {
	if (isAdmin()) {
		return true;
	}
	if (!isLoggedIn()) {
		return false;
	}
	if ($request['petshelterid'] === null) {
		return $request['petuserid'] == currentUserId();
	}
	return isShelterStaff() && $request['petshelterid'] == currentShelterId();
}

// Who will answer this request - shown to the admin, who supervises all of them
function handlerLabel($request) {
	if ($request['petshelterid'] === null) {
		return $request['ownername'] ? 'Owner: ' . $request['ownername'] : 'Admin (the owner\'s account was deleted)';
	}
	return $request['shelterstaff'] > 0
		? 'Staff of ' . $request['petsheltername']
		: 'Admin (' . $request['petsheltername'] . ' has no staff yet)';
}

##ADOPTER PROFILE##
// Source: PHP Group (no date) in_array and strlen, PHP Manual (server side validation of the select / radio values).

function homeTypes() {
	return ['House with garden', 'House without garden', 'Flat / apartment'];
}

function experienceLevels() {
	return ['First-time owner', 'Some experience', 'Experienced owner'];
}

function budgets() {
	return ['Under £50', '£50-£100', 'Over £100'];
}

// Server side validation for the adopter profile form
function validateAdopterProfile($profile) {
	$errors = [];
	if (!in_array($profile['hometype'], homeTypes())) {
		$errors[] = 'Please choose your type of home.';
	}
	if (!in_array($profile['experience'], experienceLevels())) {
		$errors[] = 'Please choose your experience with pets.';
	}
	if (!in_array($profile['budget'], budgets())) {
		$errors[] = 'Please choose a monthly budget.';
	}
	if (strlen($profile['otherpets']) > 255) {
		$errors[] = 'Other pets must be 255 characters or fewer.';
	}
	return $errors;
}

// Source: PHP Group (no date) preg_match and strlen, PHP Manual. Passwords are stored with password_hash (PHP Group, no date).
// Password rules shared by the register and change password forms
function validatePassword($password, $confirm) {
	$errors = [];
	if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
		$errors[] = 'The password must be at least 8 characters and contain a letter and a number.';
	}
	if ($password != $confirm) {
		$errors[] = 'The two passwords do not match.';
	}
	return $errors;
}

##IMAGE UPLOAD - adapted from W3Schools (2024) PHP File Upload##

// Returns ['filename' => new file name or null, 'error' => message or null]
function uploadImage($file) {
	// No file chosen - that is fine, the image is optional
	if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
		return ['filename' => null, 'error' => null];
	}
	if ($file['error'] != UPLOAD_ERR_OK) {
		return ['filename' => null, 'error' => 'The image could not be uploaded (it may be larger than 2MB).'];
	}

	$imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

	// Check the file is a real image
	// (@ hides the PHP notice for broken files - we show our own message instead)
	if (@getimagesize($file['tmp_name']) === false) {
		return ['filename' => null, 'error' => 'The file is not an image.'];
	}
	// Check file size (2MB)
	if ($file['size'] > 2000000) {
		return ['filename' => null, 'error' => 'Sorry, the image must be smaller than 2MB.'];
	}
	// Allow certain file formats only
	if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
		return ['filename' => null, 'error' => 'Only JPG, JPEG, PNG, GIF and WEBP files are allowed.'];
	}

	// Give the file a unique name so uploads never overwrite each other
	$newName = uniqid('pet_') . '.' . $imageFileType;
	if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
		return ['filename' => null, 'error' => 'Sorry, there was an error saving the image. Check the images/pets folder is writable.'];
	}
	return ['filename' => $newName, 'error' => null];
}

// Source: PHP Group (no date) unlink and basename, PHP Manual. https://www.php.net/manual/en/function.unlink.php
// Remove an image file from the server when its pet is deleted or the image is replaced
function deleteImage($filename) {
	if ($filename != '') {
		$path = UPLOAD_DIR . basename($filename);
		if (file_exists($path)) {
			unlink($path);
		}
	}
}
