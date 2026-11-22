<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint
//   - PHP Group (no date) Connections and Connection management (new PDO, setAttribute), PHP Manual. https://www.php.net/manual/en/pdo.connections.php
// Connect to the database with PDO (same as the lab, database name changed to petadopt)
// The isset() check lets currentUser() in Functions.php open the connection first, so a page never opens two.
if (!isset($pdo)) {
	$pdo = new PDO('mysql:host=localhost;dbname=petadopt;charset=utf8mb4', 'root', '');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
