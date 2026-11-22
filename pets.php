<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - Pagination: paginate() in includes/Functions.php (MySQL LIMIT / OFFSET)
include 'includes/Functions.php';

try {
	include 'includes/DatabaseConnection.php';
	include 'includes/DatabaseFunctions.php';

	// Read the search filters from the URL (pets.php?speciesid=1 etc.)
	$filters = [
		'search' => getValue($_GET, 'search'),
		'speciesid' => getValue($_GET, 'speciesid'),
		'shelterid' => getValue($_GET, 'shelterid'),
		'status' => getValue($_GET, 'status'),
		'sort' => getValue($_GET, 'sort', 'newest'),
		'gender' => getValue($_GET, 'gender'),
		'size' => getValue($_GET, 'size'),
		'age' => getValue($_GET, 'age'),
		'city' => getValue($_GET, 'city')
	];
	// The "good with" tick boxes (goodwithkids, goodwithdogs ...)
	foreach (compatibilityOptions() as $column => $label) {
		$filters[$column] = getValue($_GET, $column);
	}

	// Show 8 pets per page (2 full rows of 4 cards on a desktop screen)
	$pagination = paginate(countPets($pdo, $filters), 8, getValue($_GET, 'page', 1));

	$title = 'Find a pet';
	$output = loadTemplate('pets.html.php', [
		'pets' => allPets($pdo, $filters, $pagination['perPage'], $pagination['offset']),
		'pagination' => $pagination,
		'favouriteIds' => isLoggedIn() ? favouriteIds($pdo, currentUserId()) : [],
		'species' => findAll($pdo, 'species', 'name'),
		'shelters' => findAll($pdo, 'shelter', 'name'),
		'cities' => shelterCities($pdo),
		'filters' => $filters
	]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include 'templates/layout.html.php';
