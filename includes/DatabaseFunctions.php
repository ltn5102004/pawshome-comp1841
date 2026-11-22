<?php

// Source: COMP1841 lab materials (DatabaseFunctions.php) and Butler and Yank (2017) PHP & MySQL: Novice to Ninja;
//         PHP Group (no date) PDO::prepare and PDOStatement::execute, PHP Manual.
##REUSABLE QUERY FUNCTION##

function query($pdo, $sql, $parameters = []) {
	$query = $pdo->prepare($sql);
	$query->execute($parameters);
	return $query;
}

// Source: adapted from Butler and Yank (2017) PHP & MySQL: Novice to Ninja, generic insert / update / delete / findById / findAll.
##GENERIC FUNCTIONS - work with any table##

// Count the rows in a table
function total($pdo, $table) {
	$query = query($pdo, 'SELECT COUNT(*) FROM `' . $table . '`');
	$row = $query->fetch(PDO::FETCH_NUM);
	return $row[0];
}

// Get one row by its primary key
function findById($pdo, $table, $primaryKey, $value) {
	$query = 'SELECT * FROM `' . $table . '` WHERE `' . $primaryKey . '` = :value';
	$parameters = [':value' => $value];
	$query = query($pdo, $query, $parameters);
	return $query->fetch();
}

// Get every row from a table, optionally sorted by one column
function findAll($pdo, $table, $orderBy = null) {
	$sql = 'SELECT * FROM `' . $table . '`';
	if ($orderBy != null) {
		$sql .= ' ORDER BY `' . $orderBy . '`';
	}
	$result = query($pdo, $sql);
	return $result->fetchAll();
}

// INSERT INTO table (a, b) VALUES (:a, :b) built from the $fields array
function insert($pdo, $table, $fields) {
	$query = 'INSERT INTO `' . $table . '` (';
	foreach ($fields as $key => $value) {
		$query .= '`' . $key . '`,';
	}
	$query = rtrim($query, ',');
	$query .= ') VALUES (';
	foreach ($fields as $key => $value) {
		$query .= ':' . $key . ',';
	}
	$query = rtrim($query, ',');
	$query .= ')';

	$fields = processDates($fields);
	query($pdo, $query, $fields);
	return $pdo->lastInsertId();
}

// UPDATE table SET a = :a, b = :b WHERE id = :primaryKey
function update($pdo, $table, $primaryKey, $fields) {
	$query = 'UPDATE `' . $table . '` SET ';
	foreach ($fields as $key => $value) {
		$query .= '`' . $key . '` = :' . $key . ',';
	}
	$query = rtrim($query, ',');
	$query .= ' WHERE `' . $primaryKey . '` = :primaryKey';

	// Set the :primaryKey variable
	$fields['primaryKey'] = $fields[$primaryKey];
	$fields = processDates($fields);
	query($pdo, $query, $fields);
}

// DELETE FROM table WHERE primaryKey = :id
function delete($pdo, $table, $primaryKey, $id) {
	$parameters = [':id' => $id];
	query($pdo, 'DELETE FROM `' . $table . '` WHERE `' . $primaryKey . '` = :id', $parameters);
}

// Turn any DateTime objects into the Y-m-d H:i:s format MySQL expects
function processDates($fields) {
	foreach ($fields as $key => $value) {
		if ($value instanceof DateTime) {
			$fields[$key] = $value->format('Y-m-d H:i:s');
		}
	}
	return $fields;
}

// Source: MySQL (no date) JOIN Clause and Subqueries, MySQL 8.0 Reference Manual (INNER JOIN, LEFT JOIN, COUNT subquery).
##PET FUNCTIONS##

// The SELECT used by every pet list - joins pet with species, shelter and user
function petSelect() {
	return 'SELECT pet.id, pet.name, pet.age, pet.gender, pet.description, pet.image,
			pet.imagecredit, pet.imagesource,
			pet.breed, pet.size, pet.energy, pet.vaccinated, pet.neutered, pet.healthnotes,
			pet.goodwithkids, pet.goodwithdogs, pet.goodwithcats, pet.apartmentok,
			pet.status, pet.approval, pet.reviewnote, pet.dateadded, pet.speciesid, pet.shelterid, pet.userid,
			species.name AS speciesname,
			shelter.name AS sheltername,
			COALESCE(shelter.city, pet.city) AS sheltercity,   -- a private rehoming has no shelter, so use its own city
			shelter.email AS shelteremail, shelter.phone AS shelterphone,
			shelter.verified AS shelterverified,
			user.name AS postedby, user.role AS postedbyrole,
			(SELECT COUNT(*) FROM favourite WHERE favourite.petid = pet.id) AS favouritecount
		FROM pet
		INNER JOIN species ON pet.speciesid = species.id
		LEFT JOIN shelter ON pet.shelterid = shelter.id
		LEFT JOIN user ON pet.userid = user.id';
}

// Builds the WHERE part for the search filters on the pets page.
// Returns the SQL and its parameters so both allPets() and countPets() can use it.
function petFilters($filters) {
	$sql = ' WHERE 1 = 1';
	$parameters = [];

	// SECURE BY DEFAULT: unless the caller asks for something else, only listings that an admin has
	// approved are returned. The admin pages pass 'approval' => 'any' (all) or 'Pending' / 'Rejected'.
	$approval = $filters['approval'] ?? 'Approved';
	if (in_array($approval, ['Pending', 'Approved', 'Rejected'])) {
		$sql .= ' AND pet.approval = :approval';
		$parameters[':approval'] = $approval;
	}

	if (!empty($filters['search'])) {
		$sql .= ' AND (pet.name LIKE :search OR pet.description LIKE :search2 OR pet.breed LIKE :search3)';
		$parameters[':search'] = '%' . $filters['search'] . '%';
		$parameters[':search2'] = '%' . $filters['search'] . '%';
		$parameters[':search3'] = '%' . $filters['search'] . '%';
	}
	if (!empty($filters['speciesid'])) {
		$sql .= ' AND pet.speciesid = :speciesid';
		$parameters[':speciesid'] = $filters['speciesid'];
	}
	if (!empty($filters['shelterid'])) {
		$sql .= ' AND pet.shelterid = :shelterid';
		$parameters[':shelterid'] = $filters['shelterid'];
	}
	if (!empty($filters['status'])) {
		$sql .= ' AND pet.status = :status';
		$parameters[':status'] = $filters['status'];
	}
	if (!empty($filters['gender'])) {
		$sql .= ' AND pet.gender = :gender';
		$parameters[':gender'] = $filters['gender'];
	}
	if (!empty($filters['size'])) {
		$sql .= ' AND pet.size = :size';
		$parameters[':size'] = $filters['size'];
	}
	// Shelters are joined in countPets() too, so the city can be filtered here.
	// COALESCE = the shelter's city, or the pet's own city for a private rehoming.
	if (!empty($filters['city'])) {
		$sql .= ' AND COALESCE(shelter.city, pet.city) = :city';
		$parameters[':city'] = $filters['city'];
	}
	// Age groups: young 0-2, adult 3-7, senior 8+ (BETWEEN is inclusive)
	// Source: MySQL (no date) Comparison Functions and Operators - BETWEEN, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html
	$ageGroups = ['young' => [0, 2], 'adult' => [3, 7], 'senior' => [8, 99]];
	if (!empty($filters['age']) && isset($ageGroups[$filters['age']])) {
		$sql .= ' AND pet.age BETWEEN :agemin AND :agemax';
		$parameters[':agemin'] = $ageGroups[$filters['age']][0];
		$parameters[':agemax'] = $ageGroups[$filters['age']][1];
	}
	// Tick boxes: only add the condition when the box is ticked.
	// The column names come from this fixed list, never from the user, so they are safe to put in the SQL.
	foreach (compatibilityOptions() as $column => $label) {
		if (!empty($filters[$column])) {
			$sql .= ' AND pet.' . $column . ' = 1';
		}
	}

	return ['sql' => $sql, 'parameters' => $parameters];
}

// How many pets match the filters (needed to work out the number of pages)
function countPets($pdo, $filters = []) {
	$where = petFilters($filters);
	$query = query($pdo, 'SELECT COUNT(*) FROM pet LEFT JOIN shelter ON pet.shelterid = shelter.id' . $where['sql'], $where['parameters']);
	return $query->fetch(PDO::FETCH_NUM)[0];
}

// All pets, with optional search filters from the pets page.
// $limit and $offset return one page of results (see paginate() in Functions.php)
function allPets($pdo, $filters = [], $limit = null, $offset = 0) {
	$where = petFilters($filters);
	$sql = petSelect() . $where['sql'];
	$parameters = $where['parameters'];

	// Dynamic sorting options
	$sort = $filters['sort'] ?? 'newest';
	switch ($sort) {
		case 'oldest':
			$sql .= ' ORDER BY pet.dateadded ASC, pet.id ASC';
			break;
		case 'name_asc':
			$sql .= ' ORDER BY pet.name ASC';
			break;
		case 'name_desc':
			$sql .= ' ORDER BY pet.name DESC';
			break;
		case 'age_asc':
			$sql .= ' ORDER BY pet.age ASC, pet.name ASC';
			break;
		case 'age_desc':
			$sql .= ' ORDER BY pet.age DESC, pet.name ASC';
			break;
		case 'newest':
		default:
			$sql .= ' ORDER BY pet.dateadded DESC, pet.id DESC';
			break;
	}

	// (int) makes sure only whole numbers are added to the SQL
	if ($limit !== null) {
		$sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
	}

	return query($pdo, $sql, $parameters)->fetchAll();
}

// Number of listings waiting for (or already given) a decision, e.g. totalPetsByApproval($pdo, 'Pending')
function totalPetsByApproval($pdo, $approval) {
	$query = query($pdo, 'SELECT COUNT(*) FROM pet WHERE approval = :approval', [':approval' => $approval]);
	return $query->fetch(PDO::FETCH_NUM)[0];
}

// Oldest first, so the admin works through the queue in the order it arrived
function pendingPets($pdo) {
	return query($pdo, petSelect() . " WHERE pet.approval = 'Pending' ORDER BY pet.dateadded ASC, pet.id ASC")->fetchAll();
}

// Returns the pet whatever its approval is - the controller decides who may see a pet that is not approved
function getPet($pdo, $id) {
	$parameters = [':id' => $id];
	$query = query($pdo, petSelect() . ' WHERE pet.id = :id', $parameters);
	return $query->fetch();
}

function latestPets($pdo, $limit = 3) {
	$sql = petSelect() . " WHERE pet.status = 'Available' AND pet.approval = 'Approved' ORDER BY pet.dateadded DESC, pet.id DESC LIMIT " . (int)$limit;
	return query($pdo, $sql)->fetchAll();
}

function petsByUser($pdo, $userId) {
	$parameters = [':userid' => $userId];
	return query($pdo, petSelect() . ' WHERE pet.userid = :userid ORDER BY pet.dateadded DESC', $parameters)->fetchAll();
}

function totalPetsByStatus($pdo, $status) {
	// Only approved listings are counted (these numbers are shown on the public home page)
	$query = query($pdo, "SELECT COUNT(*) FROM pet WHERE status = :status AND approval = 'Approved'", [':status' => $status]);
	return $query->fetch(PDO::FETCH_NUM)[0];
}

// Source: MySQL (no date) Aggregate Functions and GROUP BY, MySQL 8.0 Reference Manual.
##SHELTER AND SPECIES FUNCTIONS##

// Shelters with the number of pets in each one (JOIN + GROUP BY)
function sheltersWithPetCount($pdo) {
	// Only approved pets are counted. The condition is in the ON part so shelters with no pets still appear.
	$sql = "SELECT shelter.*, COUNT(pet.id) AS petcount,
			(SELECT COUNT(*) FROM user WHERE user.shelterid = shelter.id) AS staffcount
		FROM shelter
		LEFT JOIN pet ON pet.shelterid = shelter.id AND pet.approval = 'Approved'
		GROUP BY shelter.id
		ORDER BY shelter.name";
	return query($pdo, $sql)->fetchAll();
}

##ADMIN DASHBOARD##
// Source: MySQL (no date) Aggregate Functions - COUNT() and SUM() with a condition, MySQL 8.0 Reference Manual.
//         https://dev.mysql.com/doc/refman/8.0/en/aggregate-functions.html

// Small numbers shown next to the items of the admin menu
function adminBadgeCounts($pdo) {
	$row = query($pdo, "SELECT
			(SELECT COUNT(*) FROM pet WHERE approval = 'Pending') AS review,
			(SELECT COUNT(*) FROM adoptionrequest WHERE status = 'New') AS newrequests,
			(SELECT COUNT(*) FROM message) AS messages")->fetch();
	return array_map('intval', $row);
}

// Every number on the dashboard, in a handful of queries
function dashboardData($pdo) {
	$pets = query($pdo, "SELECT COUNT(*) AS total, SUM(status = 'Available') AS available, SUM(status = 'Reserved') AS reserved,
			SUM(status = 'Adopted') AS adopted, SUM(approval = 'Pending') AS pending FROM pet")->fetch();
	$requests = query($pdo, "SELECT COUNT(*) AS total, SUM(status = 'New') AS new, SUM(status = 'Interviewing') AS interviewing,
			SUM(status = 'Approved') AS approved FROM adoptionrequest")->fetch();
	$users = query($pdo, "SELECT COUNT(*) AS total, SUM(role = 'admin') AS admins, SUM(shelterid IS NOT NULL) AS staff FROM user")->fetch();
	$shelters = query($pdo, 'SELECT COUNT(*) AS total, SUM(verified = 1) AS verified FROM shelter')->fetch();
	// Open requests nobody else can answer: a private pet whose owner is gone, or a shelter without staff
	$adminOnly = query($pdo, "SELECT COUNT(*) FROM adoptionrequest
		INNER JOIN pet ON pet.id = adoptionrequest.petid
		WHERE adoptionrequest.status IN ('New', 'Interviewing')
		AND ((pet.shelterid IS NULL AND pet.userid IS NULL)
			OR (pet.shelterid IS NOT NULL AND NOT EXISTS (SELECT 1 FROM user WHERE user.shelterid = pet.shelterid)))")->fetch(PDO::FETCH_NUM)[0];
	return [
		'pets' => array_map('intval', $pets),
		'requests' => array_map('intval', $requests),
		'users' => array_map('intval', $users),
		'shelters' => array_map('intval', $shelters),
		'messages' => (int)total($pdo, 'message'),
		'adminOnly' => (int)$adminOnly,
		'latestRequests' => query($pdo, requestSelect() . ' ORDER BY adoptionrequest.requestdate DESC, adoptionrequest.id DESC LIMIT 5')->fetchAll(),
		'latestPets' => query($pdo, petSelect() . ' ORDER BY pet.dateadded DESC, pet.id DESC LIMIT 5')->fetchAll()
	];
}

// Every city that has a shelter, for the "Area" filter
// Source: MySQL (no date) SELECT DISTINCT, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/select.html
function shelterCities($pdo) {
	// Cities of the shelters plus the cities of approved private rehoming pets
	// Source: MySQL (no date) UNION Clause, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/union.html
	$sql = "SELECT city FROM shelter
		UNION
		SELECT city FROM pet WHERE city IS NOT NULL AND approval = 'Approved'
		ORDER BY city";
	return query($pdo, $sql)->fetchAll(PDO::FETCH_COLUMN);
}

function speciesWithPetCount($pdo) {
	$sql = 'SELECT species.*, COUNT(pet.id) AS petcount
		FROM species
		LEFT JOIN pet ON pet.speciesid = species.id
		GROUP BY species.id
		ORDER BY species.name';
	return query($pdo, $sql)->fetchAll();
}

// Source: prepared statement with a named parameter - PHP Group (no date) PDO::prepare, PHP Manual. https://www.php.net/manual/en/pdo.prepare.php
##LOGIN ATTEMPTS - slows down password guessing##
// Source: OWASP (no date) Authentication Cheat Sheet - protect against automated attacks (throttling failed logins).
//         https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html
// A failure is counted for the pair (e-mail, IP address), so a stranger guessing one account's password from
// his own computer cannot lock the real owner out when the owner logs in from somewhere else.
define('MAX_LOGIN_FAILURES', 5);      // failures allowed ...
define('LOGIN_WINDOW_MINUTES', 10);   // ... within this many minutes

function recentFailedLogins($pdo, $email, $ip) {
	$sql = 'SELECT COUNT(*) FROM loginattempt WHERE email = :email AND ip = :ip AND attempted > (NOW() - INTERVAL ' . (int)LOGIN_WINDOW_MINUTES . ' MINUTE)';
	return query($pdo, $sql, [':email' => strtolower($email), ':ip' => $ip])->fetch(PDO::FETCH_NUM)[0];
}

function recordFailedLogin($pdo, $email, $ip) {
	insert($pdo, 'loginattempt', ['email' => strtolower($email), 'ip' => $ip, 'attempted' => new DateTime()]);
	// keep the table small: anything older than a day is no longer needed
	query($pdo, 'DELETE FROM loginattempt WHERE attempted < (NOW() - INTERVAL 1 DAY)');
}

function clearFailedLogins($pdo, $email, $ip) {
	query($pdo, 'DELETE FROM loginattempt WHERE email = :email AND ip = :ip', [':email' => strtolower($email), ':ip' => $ip]);
}

##USER FUNCTIONS##

function findUserByEmail($pdo, $email) {
	$query = query($pdo, 'SELECT * FROM user WHERE email = :email', [':email' => $email]);
	return $query->fetch();
}

// Source: many-to-many join table idea adapted from Butler and Yank (2017) (joke_category table);
//         PHP Group (no date) PDOStatement::fetchAll with PDO::FETCH_COLUMN, PHP Manual.
##FAVOURITE FUNCTIONS (many-to-many between user and pet)##

// The full details of every pet a member has saved (JOIN through the favourite table)
function favouritePets($pdo, $userId) {
	$sql = petSelect() . " INNER JOIN favourite ON favourite.petid = pet.id
		WHERE favourite.userid = :userid AND pet.approval = 'Approved'
		ORDER BY favourite.datesaved DESC";
	return query($pdo, $sql, [':userid' => $userId])->fetchAll();
}

// Just the ids of the saved pets, so the heart buttons know which pets are already saved
function favouriteIds($pdo, $userId) {
	$query = query($pdo, 'SELECT petid FROM favourite WHERE userid = :userid', [':userid' => $userId]);
	return $query->fetchAll(PDO::FETCH_COLUMN);
}

// How many members have saved this pet (the number shown next to the heart)
function favouriteCount($pdo, $petId) {
	return query($pdo, 'SELECT COUNT(*) FROM favourite WHERE petid = :petid', [':petid' => $petId])->fetch(PDO::FETCH_NUM)[0];
}

function isFavourite($pdo, $userId, $petId) {
	$parameters = [':userid' => $userId, ':petid' => $petId];
	$query = query($pdo, 'SELECT COUNT(*) FROM favourite WHERE userid = :userid AND petid = :petid', $parameters);
	return $query->fetch(PDO::FETCH_NUM)[0] > 0;
}

// The favourite table has a two column primary key, so the generic delete() cannot be used here
function removeFavourite($pdo, $userId, $petId) {
	$parameters = [':userid' => $userId, ':petid' => $petId];
	query($pdo, 'DELETE FROM favourite WHERE userid = :userid AND petid = :petid', $parameters);
}

// Source: MySQL (no date) JOIN Clause, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/join.html
##ADOPTER PROFILE FUNCTIONS (one-to-one with user)##
// Source: one-to-one relationship (shared primary key) - MySQL (no date) FOREIGN KEY Constraints, MySQL 8.0 Reference Manual.
//         https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html

function findAdopterProfile($pdo, $userId) {
	return findById($pdo, 'adopterprofile', 'userid', $userId);
}

// Insert the profile the first time, update it after that (re-uses the generic insert / update functions)
function saveAdopterProfile($pdo, $userId, $fields) {
	$fields['userid'] = $userId;
	$fields['updated'] = new DateTime();
	if (findAdopterProfile($pdo, $userId)) {
		update($pdo, 'adopterprofile', 'userid', $fields);
	} else {
		insert($pdo, 'adopterprofile', $fields);
	}
}

##ADOPTION REQUEST FUNCTIONS##

function requestSelect() {
	// `user` is the ADOPTER who sent the request; `owner` is the member who listed the pet (needs the second join).
	// LEFT JOIN because an adopter may not have filled in a profile, and a pet may have no shelter or no owner.
	// Source: MySQL (no date) JOIN Clause (table aliases), MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/join.html
	return 'SELECT adoptionrequest.*, pet.name AS petname, pet.image AS petimage,
			pet.status AS petstatus, pet.shelterid AS petshelterid, pet.userid AS petuserid,
			shelter.name AS petsheltername, owner.name AS ownername,
			(SELECT COUNT(*) FROM user AS staff WHERE staff.shelterid = pet.shelterid) AS shelterstaff,
			user.name AS username, user.email AS useremail,
			adopterprofile.hometype, adopterprofile.householdagrees, adopterprofile.experience,
			adopterprofile.otherpets, adopterprofile.budget
		FROM adoptionrequest
		INNER JOIN pet ON adoptionrequest.petid = pet.id
		INNER JOIN user ON adoptionrequest.userid = user.id
		LEFT JOIN shelter ON shelter.id = pet.shelterid
		LEFT JOIN user AS owner ON owner.id = pet.userid
		LEFT JOIN adopterprofile ON adopterprofile.userid = user.id';
}

// WHO HANDLES A REQUEST: the person who can vouch for the pet.
//   - private rehoming (no shelter): the member who listed the pet
//   - shelter pet: the staff of that shelter
//   - admins can see and handle everything (they step in when a shelter has no staff or an owner has left)
// This builds the WHERE part for "requests that this person handles" (used by the list and the counter).
function handlerWhere($userId, $shelterId) {
	$sql = ' WHERE ((pet.shelterid IS NULL AND pet.userid = :userid)';
	$parameters = [':userid' => $userId];
	if ($shelterId !== null) {
		$sql .= ' OR pet.shelterid = :shelterid';
		$parameters[':shelterid'] = $shelterId;
	}
	return ['sql' => $sql . ')', 'parameters' => $parameters];
}

// Requests for the pets this person is responsible for, newest first
function requestsToHandle($pdo, $userId, $shelterId = null) {
	$where = handlerWhere($userId, $shelterId);
	return query($pdo, requestSelect() . $where['sql'] . ' ORDER BY adoptionrequest.requestdate DESC, adoptionrequest.id DESC', $where['parameters'])->fetchAll();
}

// How many requests this person handles, and how many of them are still New (shown as a badge in the menu)
function countRequestsToHandle($pdo, $userId, $shelterId = null) {
	$where = handlerWhere($userId, $shelterId);
	$sql = "SELECT COUNT(*) AS total, SUM(adoptionrequest.status = 'New') AS new
		FROM adoptionrequest INNER JOIN pet ON adoptionrequest.petid = pet.id" . $where['sql'];
	$row = query($pdo, $sql, $where['parameters'])->fetch();
	return ['total' => (int)$row['total'], 'new' => (int)$row['new']];
}

function findRequest($pdo, $id) {
	return query($pdo, requestSelect() . ' WHERE adoptionrequest.id = :id', [':id' => $id])->fetch();
}

// Checks a move BEFORE it is made. Returns an error message, or null when the move is allowed.
// $request comes from requestSelect().
function validateRequestMove($pdo, $request, $stage) {
	if (!array_key_exists($stage, requestActions($request['status']))) {
		return 'A request that is "' . $request['status'] . '" cannot be moved to "' . $stage . '".';
	}
	// Once the pet has gone to its new home, the approved request is history
	if ($request['status'] == 'Approved' && $request['petstatus'] == 'Adopted') {
		return $request['petname'] . ' has already been adopted, so this request can no longer be changed.';
	}
	if ($stage == 'Approved') {
		if ($request['petstatus'] == 'Adopted') {
			return $request['petname'] . ' has already been adopted.';
		}
		// One pet goes to one home
		$other = query($pdo, "SELECT COUNT(*) FROM adoptionrequest WHERE petid = :petid AND status = 'Approved' AND id <> :id",
			[':petid' => $request['petid'], ':id' => $request['id']])->fetch(PDO::FETCH_NUM)[0];
		if ($other > 0) {
			return 'Another request for ' . $request['petname'] . ' is already approved. Withdraw that one first.';
		}
	}
	return null;
}

// How much a member has in the system (shown before they delete their account)
function accountSummary($pdo, $userId) {
	$one = function ($sql) use ($pdo, $userId) {
		return (int)query($pdo, $sql, [':id' => $userId])->fetch(PDO::FETCH_NUM)[0];
	};
	return [
		'privatePets' => $one('SELECT COUNT(*) FROM pet WHERE userid = :id AND shelterid IS NULL'),
		'shelterPets' => $one('SELECT COUNT(*) FROM pet WHERE userid = :id AND shelterid IS NOT NULL'),
		'requestsSent' => $one('SELECT COUNT(*) FROM adoptionrequest WHERE userid = :id'),
		'requestsReceived' => $one('SELECT COUNT(*) FROM adoptionrequest INNER JOIN pet ON pet.id = adoptionrequest.petid WHERE pet.userid = :id AND pet.shelterid IS NULL'),
		'favourites' => $one('SELECT COUNT(*) FROM favourite WHERE userid = :id')
	];
}

// Deletes an account and everything that belongs to it. Used by the admin (users page) and by members deleting
// their own account (profile page), so both always do exactly the same thing.
//   - the foreign keys delete the member's adopter profile, favourites and the requests they sent (ON DELETE CASCADE)
//   - pets the member listed: a PRIVATE pet belongs to the person, so with $deleteOwnPets = true it is deleted with its
//     photo; pets listed for a shelter belong to the shelter and stay (their owner column becomes NULL)
//   - pets that were Reserved through this member's approved requests are freed again
// Source: database transaction - PHP Group (no date) PDO::beginTransaction, commit and rollBack, PHP Manual.
//         https://www.php.net/manual/en/pdo.begintransaction.php
function deleteUserAccount($pdo, $userId, $deleteOwnPets = false) {
	$images = [];
	$pdo->beginTransaction();
	try {
		$user = findById($pdo, 'user', 'id', $userId);
		$reserved = query($pdo, "SELECT DISTINCT petid FROM adoptionrequest WHERE userid = :id AND status = 'Approved'", [':id' => $userId])->fetchAll(PDO::FETCH_COLUMN);
		if ($deleteOwnPets) {
			foreach (query($pdo, 'SELECT id, image FROM pet WHERE userid = :id AND shelterid IS NULL', [':id' => $userId])->fetchAll() as $pet) {
				delete($pdo, 'pet', 'id', $pet['id']);
				if ($pet['image']) {
					$images[] = $pet['image'];
				}
			}
		}
		delete($pdo, 'user', 'id', $userId);
		foreach ($reserved as $petId) {
			freeReservedPet($pdo, $petId);
		}
		// failed log in attempts of this e-mail are no longer needed
		if ($user) {
			query($pdo, 'DELETE FROM loginattempt WHERE email = :email', [':email' => strtolower($user['email'])]);
		}
		$pdo->commit();
	} catch (PDOException $e) {
		$pdo->rollBack();
		throw $e;
	}
	// photo files are removed only after the database part succeeded
	foreach ($images as $image) {
		deleteImage($image);
	}
}

// A pet is only "Reserved" while one of its requests is Approved. Call this after anything that could remove the
// reason for the reservation (approval withdrawn, request deleted, adopter's account deleted): if no approved
// request is left, a Reserved pet becomes Available again. A pet that is Adopted is never changed here.
function freeReservedPet($pdo, $petId) {
	$left = query($pdo, "SELECT COUNT(*) FROM adoptionrequest WHERE petid = :petid AND status = 'Approved'", [':petid' => $petId])->fetch(PDO::FETCH_NUM)[0];
	if ($left == 0) {
		query($pdo, "UPDATE pet SET status = 'Available' WHERE id = :petid AND status = 'Reserved'", [':petid' => $petId]);
	}
}

// Moves a request to another stage and keeps the pet's status in step. Returns the message to show.
// The caller must already have checked canHandleRequest().
function moveRequest($pdo, $request, $stage) {
	// The days are saved so the progress bar can show what REALLY happened:
	//   Interviewing  the interview day is saved the first time (it is kept if the interview already took place)
	//   Approved / Rejected  the day of the decision is saved (an interview day, if any, stays)
	//   New (back to new or reopen)  everything after "request sent" is cleared
	// Withdrawing an approval (Approved -> Interviewing) clears the decision day.
	$fields = ['id' => $request['id'], 'status' => $stage];
	if ($stage == 'Interviewing') {
		$fields['interviewdate'] = $request['interviewdate'] ?: new DateTime();
		$fields['decisiondate'] = null;
	} elseif ($stage == 'Approved' || $stage == 'Rejected') {
		$fields['decisiondate'] = new DateTime();
	} else {
		$fields['interviewdate'] = null;
		$fields['decisiondate'] = null;
	}
	update($pdo, 'adoptionrequest', 'id', $fields);

	if ($stage == 'Approved') {
		// Approving a request reserves the pet so nobody else can take it
		update($pdo, 'pet', 'id', ['id' => $request['petid'], 'status' => 'Reserved']);
	} elseif ($request['status'] == 'Approved') {
		// The approval was withdrawn: free the pet again unless another request for it is still approved
		freeReservedPet($pdo, $request['petid']);
	}
	return 'The request for ' . $request['petname'] . ' has been moved to "' . $stage . '".';
}

function allRequests($pdo) {
	return query($pdo, requestSelect() . ' ORDER BY adoptionrequest.requestdate DESC, adoptionrequest.id DESC')->fetchAll();
}

function requestsByUser($pdo, $userId) {
	$parameters = [':userid' => $userId];
	return query($pdo, requestSelect() . ' WHERE adoptionrequest.userid = :userid ORDER BY adoptionrequest.requestdate DESC', $parameters)->fetchAll();
}

// Has this user already asked to adopt this pet?
// Does this pet have an adoption request that was approved? (then it must stay Reserved or Adopted)
function hasApprovedRequest($pdo, $petId) {
	$query = query($pdo, "SELECT COUNT(*) FROM adoptionrequest WHERE petid = :petid AND status = 'Approved'", [':petid' => $petId]);
	return $query->fetch(PDO::FETCH_NUM)[0] > 0;
}

function hasRequested($pdo, $petId, $userId) {
	$parameters = [':petid' => $petId, ':userid' => $userId];
	$query = query($pdo, 'SELECT COUNT(*) FROM adoptionrequest WHERE petid = :petid AND userid = :userid', $parameters);
	return $query->fetch(PDO::FETCH_NUM)[0] > 0;
}
