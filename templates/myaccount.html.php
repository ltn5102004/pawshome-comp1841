<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - PHP Group (no date) Output Control - ob_start / ob_get_clean (each section is built first, then shown in the order that suits the user). https://www.php.net/manual/en/function.ob-start.php
//   - MDN Web Docs (no date) CSS grid layout (tiles). https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
// Needs: $role, $incoming, $user, $profile, $favourites, $favouriteIds, $myPets, $shelterPets, $myRequests
[$roleLabel, $roleText] = roleInfo($role);

// --- "Next steps": the most useful things to do now, worked out from the user's data ---
$nextSteps = [];
if ($incoming['new'] > 0) {
	$nextSteps[] = ['send', 'requests.php', $incoming['new'] . ' new adoption ' . ($incoming['new'] == 1 ? 'request is' : 'requests are') . ' waiting for your answer.', 'Answer now'];
}
// All approved requests in ONE line, so a long list does not push everything else down
$approved = array_column(array_filter($myRequests, function ($r) { return $r['status'] == 'Approved'; }), 'petname');
if ($approved) {
	// "Pip", "Pip and Max", "Pip, Max and Kiwi"
	$names = count($approved) > 1 ? implode(', ', array_slice($approved, 0, -1)) . ' and ' . end($approved) : $approved[0];
	$nextSteps[] = ['check', '#my-requests', 'Good news: your ' . (count($approved) == 1 ? 'request for ' . $names . ' was' : 'requests for ' . $names . ' were') . ' approved. You will be contacted to arrange the adoption.', 'See my requests'];
}
foreach (array_merge($shelterPets ?? [], $myPets) as $p) {
	if ($p['approval'] == 'Rejected') {
		$nextSteps[] = ['alert', 'editpet.php?id=' . $p['id'], 'The listing for ' . $p['name'] . ' was not approved' . ($p['reviewnote'] ? ': "' . $p['reviewnote'] . '"' : '.'), 'Fix the listing'];
	}
}
if (!$profile && $role != 'staff' && $role != 'admin') {
	$nextSteps[] = ['user', 'adopterprofile.php', 'Create your adopter profile so owners and shelters can answer you faster.', 'Create profile'];
}
if ($role == 'staff' && !$user['shelterverified']) {
	$nextSteps[] = ['alert', 'contact.php', 'Your shelter is not verified yet, so an admin checks each listing first.', 'Ask the team'];
}

// --- quick tiles at the top, different for each kind of user ---
$requestTile = ['send', 'requests.php', $incoming['total'], 'Requests for my pets', $incoming['new'] ? $incoming['new'] . ' new, waiting for you' : 'Nothing new to answer'];
$tiles = [
	'admin' => [
		['dashboard', 'admin/index.php', '', 'Admin dashboard', 'Run the site'],
		['search', 'pets.php', '', 'Find a pet', 'Browse the public site'],
		['heart', '#favourites', count($favourites), 'Favourites', 'Pets you saved'],
		['user', 'profile.php', '', 'Edit profile', 'Name, e-mail, password']
	],
	'staff' => [
		$requestTile,
		['pets', '#shelter-pets', count($shelterPets ?? []), 'Shelter pets', $user['sheltername']],
		['plus', 'addpet.php', '', 'Add a pet', $user['shelterverified'] ? 'Published straight away' : 'Checked by an admin first'],
		['user', 'profile.php', '', 'Edit profile', 'Name, e-mail, password']
	],
	'owner' => [
		$requestTile,
		['pets', '#my-pets', count($myPets), 'My listed pets', 'Edit or remove them'],
		['send', '#my-requests', count($myRequests), 'Requests I sent', 'Follow their progress'],
		['heart', '#favourites', count($favourites), 'Favourites', 'Pets you saved']
	],
	'adopter' => [
		['search', 'pets.php', '', 'Find a pet', 'Search and filter'],
		['send', '#my-requests', count($myRequests), 'My requests', 'Follow their progress'],
		['heart', '#favourites', count($favourites), 'Favourites', 'Pets you saved'],
		['user', 'adopterprofile.php', '', 'Adopter profile', $profile ? 'Complete' : 'Not filled in yet']
	]
][$role];

// --- each section is built once and stored; the order is chosen below ---
$sections = [];

ob_start(); ?>
<section class="account-section" id="adopter-profile" aria-labelledby="adopter-heading">
	<div class="section-head">
		<h2 id="adopter-heading">My adopter profile</h2>
		<a class="btn btn-small btn-outline" href="adopterprofile.php"><?=$profile ? 'Edit' : 'Create'?> adopter profile</a>
	</div>
	<?php if ($profile): ?>
		<dl class="facts">
			<div><dt>Home</dt><dd><?=html($profile['hometype'])?></dd></div>
			<div><dt>Experience</dt><dd><?=html($profile['experience'])?></dd></div>
			<div><dt>Monthly budget</dt><dd><?=html($profile['budget'])?></dd></div>
			<div><dt>Household agrees</dt><dd><?=$profile['householdagrees'] ? 'Yes' : 'Not yet'?></dd></div>
			<div><dt>Other pets</dt><dd><?=html($profile['otherpets'] ?: 'None')?></dd></div>
		</dl>
		<p class="meta">Owners and shelters see this profile with every adoption request you send. Last updated <?=formatDate($profile['updated'])?>.</p>
	<?php else: ?>
		<p>Fill in your living situation once and it will be sent with every adoption request, so owners and shelters can reply faster.</p>
	<?php endif; ?>
</section>
<?php $sections['profile'] = ob_get_clean();

ob_start(); ?>
<section class="account-section" id="favourites" aria-labelledby="favourites-heading">
	<div class="section-head">
		<h2 id="favourites-heading">My favourites</h2>
		<a class="btn btn-small btn-outline" href="pets.php">Find more pets</a>
	</div>
	<?php if (empty($favourites)): ?>
		<p class="empty">You have not saved any pets yet. Press the &hearts; button on a pet to save it here. <a href="pets.php">Find a pet</a>.</p>
	<?php else: ?>
		<div class="grid">
			<?php foreach ($favourites as $pet): ?>
				<?php include __DIR__ . '/petcard.html.php'; ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<?php $sections['favourites'] = ob_get_clean();

ob_start();
if ($shelterPets !== null): ?>
	<?php // Shown only to shelter staff: every pet of their shelter, including listings that are waiting for review ?>
	<section class="account-section" id="shelter-pets" aria-labelledby="shelter-heading">
		<div class="section-head">
			<h2 id="shelter-heading">Pets at <?=html($user['sheltername'])?></h2>
			<a class="btn" href="addpet.php"><?=icon('plus')?> Add a pet</a>
		</div>
		<p class="meta">
			<?php if ($user['shelterverified']): ?>
				<span class="badge badge-verified"><span aria-hidden="true">&#10003;</span> Verified shelter</span> Your listings are published straight away.
			<?php else: ?>
				<span class="badge badge-unverified">Not yet verified</span> Your listings are checked by an admin before they appear. Ask the PawsHome team to verify your shelter.
			<?php endif; ?>
		</p>
		<?php if (empty($shelterPets)): ?>
			<p class="empty">Your shelter has no pets listed yet.</p>
		<?php else: ?>
			<?php $petList = $shelterPets; $tableCaption = 'Pets at ' . $user['sheltername']; include __DIR__ . '/petstable.html.php'; ?>
		<?php endif; ?>
	</section>
<?php endif;
$sections['shelter'] = ob_get_clean();

ob_start();
// Staff list pets for their shelter above, so an empty "Other pets" box would only be noise
if ($role != 'staff' || !empty($myPets)): ?>
<section class="account-section" id="my-pets" aria-labelledby="mypets-heading">
	<div class="section-head">
		<h2 id="mypets-heading"><?=$shelterPets !== null ? 'Other pets I have listed' : 'Pets I have listed'?></h2>
		<a class="btn<?=$role == 'adopter' ? ' btn-outline' : ''?>" href="addpet.php"><?=icon('plus')?> Rehome a pet</a>
	</div>
	<?php if (empty($myPets)): ?>
		<p class="empty">You have not listed any pets yet. Need to find a new home for your pet? Press <strong>Rehome a pet</strong>.</p>
	<?php else: ?>
		<?php $petList = $myPets; $tableCaption = 'Pets I have listed'; include __DIR__ . '/petstable.html.php'; ?>
	<?php endif; ?>
</section>
<?php endif;
$sections['mypets'] = ob_get_clean();

ob_start(); ?>
<section class="account-section" id="my-requests" aria-labelledby="myrequests-heading">
	<div class="section-head">
		<h2 id="myrequests-heading">My adoption requests</h2>
	</div>
	<?php if (empty($myRequests)): ?>
		<p class="empty">You have not sent any adoption requests. <a href="pets.php">Find a pet</a>.</p>
	<?php else: ?>
		<div class="request-list">
			<?php foreach ($myRequests as $request): ?>
				<article class="request-card stage-<?=strtolower(html($request['status']))?>">
					<img class="thumb" src="<?=html(imagePath($request['petimage']))?>" alt="" width="64" height="48">
					<div class="request-body">
						<h3><a href="pet.php?id=<?=html($request['petid'])?>"><?=html($request['petname'])?></a>
							<span class="badge badge-<?=strtolower(html($request['status']))?>"><?=html($request['status'])?></span></h3>
						<p class="meta">Sent <?=formatDate($request['requestdate'])?> &middot; &ldquo;<?=html($request['message'])?>&rdquo;</p>
						<?php include __DIR__ . '/requesttracker.html.php'; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<?php $sections['myrequests'] = ob_get_clean();

// The order of the sections: what this kind of user needs most comes first
$order = [
	'admin' => ['favourites', 'mypets', 'myrequests', 'profile'],
	'staff' => ['shelter', 'mypets', 'myrequests', 'favourites', 'profile'],
	'owner' => ['mypets', 'myrequests', 'favourites', 'profile'],
	'adopter' => ['myrequests', 'favourites', 'profile', 'mypets']
][$role];
// Short names for the "Jump to" links
$jump = ['shelter' => ['shelter-pets', 'Shelter pets'], 'mypets' => ['my-pets', 'My pets'], 'myrequests' => ['my-requests', 'Requests I sent'],
	'favourites' => ['favourites', 'Favourites'], 'profile' => ['adopter-profile', 'Adopter profile']];
?>

<header class="role-hero role-<?=html($role)?>">
	<div class="role-hero-main">
		<span class="avatar" aria-hidden="true"><?=html(initials($user['name']))?></span>
		<div>
			<p class="role-chip"><?=html($roleLabel)?><?=$role == 'staff' ? ' &middot; ' . html($user['sheltername']) : ''?></p>
			<h1>Hello, <?=html($user['name'])?></h1>
			<p class="role-hero-text"><?=html($roleText)?></p>
			<p class="role-hero-meta"><?=html($user['email'])?> &middot; member since <?=formatDate($user['datejoined'])?></p>
		</div>
	</div>
	<div class="role-hero-actions">
		<a class="btn btn-light" href="profile.php">Edit profile</a>
	</div>
</header>

<nav class="tiles role-<?=html($role)?>" aria-label="Quick links">
	<?php foreach ($tiles as $t): ?>
		<a class="tile" href="<?=html($t[1])?>">
			<span class="tile-icon"><?=icon($t[0])?></span>
			<span class="tile-text">
				<span class="tile-name"><?php if ($t[2] !== ''): ?><strong class="tile-value"><?=html($t[2])?></strong> <?php endif; ?><?=html($t[3])?></span>
				<span class="tile-detail"><?=html($t[4])?></span>
			</span>
		</a>
	<?php endforeach; ?>
</nav>

<?php if ($nextSteps): ?>
	<section class="next-steps" aria-labelledby="steps-heading">
		<h2 id="steps-heading">Next steps</h2>
		<ul>
			<?php foreach ($nextSteps as $step): ?>
				<li>
					<span class="step-icon"><?=icon($step[0])?></span>
					<span class="step-text"><?=html($step[2])?></span>
					<a class="btn btn-small" href="<?=html($step[1])?>"><?=html($step[3])?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>

<nav class="jump-links" aria-label="On this page">
	<span class="meta">Jump to:</span>
	<?php foreach ($order as $key): ?>
		<?php if ($sections[$key] !== ''): ?><a href="#<?=$jump[$key][0]?>"><?=$jump[$key][1]?></a><?php endif; ?>
	<?php endforeach; ?>
</nav>

<?php foreach ($order as $key): ?>
	<?=$sections[$key]?>
<?php endforeach; ?>
