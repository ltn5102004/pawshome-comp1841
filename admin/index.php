<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) PDO - Exceptions and errors (try / catch PDOException), PHP Manual. https://www.php.net/manual/en/pdo.error-handling.php
//   - PHP Group (no date) array_filter, PHP Manual. https://www.php.net/manual/en/function.array-filter.php
include '../includes/Functions.php';
requireAdmin();

try {
	include '../includes/DatabaseConnection.php';
	include '../includes/DatabaseFunctions.php';

	$title = 'Dashboard';
	// All the numbers come from dashboardData() in includes/DatabaseFunctions.php
	$output = loadTemplate('admin/dashboard.html.php', ['data' => dashboardData($pdo)]);
} catch (PDOException $e) {
	$title = 'An error has occurred';
	$output = loadTemplate('error.html.php', ['message' => 'Database error: ' . $e->getMessage()]);
}

include '../templates/admin_layout.html.php';
