<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) CSS grid layout (cards). https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
// Needs $data from dashboardData().
$p = $data['pets']; $r = $data['requests']; $u = $data['users']; $s = $data['shelters'];
// Things that need the admin, with a link to where they are handled. Only items with a number above 0 are "open".
$todo = [
	// [icon, link, number, text for 1, text for more, tone = what kind of task it is (colour + icon + words)]
	['review', 'approvals.php', $p['pending'], 'listing waiting for review', 'listings waiting for review', 'warning'],
	['requests', 'requests.php', $r['new'], 'new adoption request', 'new adoption requests', 'info'],
	['alert', 'requests.php', $data['adminOnly'], 'open request only you can answer (no staff or owner)', 'open requests only you can answer (no staff or owner)', 'error'],
	['shelters', 'shelters.php', $s['total'] - $s['verified'], 'shelter not verified yet', 'shelters not verified yet', 'warning'],
	['messages', 'messages.php', $data['messages'], 'contact message', 'contact messages', 'info']
];
$open = array_filter($todo, function ($t) { return $t[2] > 0; });
?>
<header class="admin-head">
	<div>
		<h1>Dashboard</h1>
		<p class="admin-sub">Hello <?=html(currentUser()['name'])?>, here is what is happening on PawsHome.</p>
	</div>
	<div class="admin-head-actions">
		<a class="btn" href="../addpet.php"><?=icon('plus')?> Add a pet</a>
	</div>
</header>

<section class="stat-grid" aria-label="Totals">
	<a class="stat-card" href="pets.php">
		<span class="stat-icon"><?=icon('pets')?></span>
		<span class="stat-value"><?=html($p['total'])?></span>
		<span class="stat-name">Pets</span>
		<span class="stat-detail"><?=html($p['available'])?> available &middot; <?=html($p['reserved'])?> reserved &middot; <?=html($p['adopted'])?> adopted</span>
	</a>
	<a class="stat-card" href="requests.php">
		<span class="stat-icon"><?=icon('requests')?></span>
		<span class="stat-value"><?=html($r['total'])?></span>
		<span class="stat-name">Adoption requests</span>
		<span class="stat-detail"><?=html($r['new'])?> new &middot; <?=html($r['interviewing'])?> interviewing &middot; <?=html($r['approved'])?> approved</span>
	</a>
	<a class="stat-card" href="users.php">
		<span class="stat-icon"><?=icon('users')?></span>
		<span class="stat-value"><?=html($u['total'])?></span>
		<span class="stat-name">Users</span>
		<span class="stat-detail"><?=html($u['admins'])?> admin &middot; <?=html($u['staff'])?> shelter staff</span>
	</a>
	<a class="stat-card" href="shelters.php">
		<span class="stat-icon"><?=icon('shelters')?></span>
		<span class="stat-value"><?=html($s['total'])?></span>
		<span class="stat-name">Shelters</span>
		<span class="stat-detail"><?=html($s['verified'])?> verified</span>
	</a>
</section>

<div class="dash-grid">
	<section class="admin-card" aria-labelledby="todo-heading">
		<h2 id="todo-heading">Needs your attention</h2>
		<?php if (empty($open)): ?>
			<p class="all-clear"><?=icon('check')?> All clear. Nothing is waiting for you.</p>
		<?php else: ?>
			<ul class="todo-list">
				<?php foreach ($open as $t): ?>
					<li>
						<a href="<?=$t[1]?>">
							<span class="todo-icon tone-<?=$t[5]?>"><?=icon($t[0])?></span>
							<span class="todo-text"><strong><?=html($t[2])?></strong> <?=html($t[2] == 1 ? $t[3] : $t[4])?></span>
							<?=icon('arrow')?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>

	<section class="admin-card" aria-labelledby="latest-requests-heading">
		<div class="card-head">
			<h2 id="latest-requests-heading">Latest adoption requests</h2>
			<a href="requests.php">All requests</a>
		</div>
		<?php if (empty($data['latestRequests'])): ?>
			<p class="meta">No requests yet.</p>
		<?php else: ?>
			<ul class="mini-list">
				<?php foreach ($data['latestRequests'] as $request): ?>
					<li>
						<img class="mini-thumb" src="<?=html(imagePath($request['petimage'], '../'))?>" alt="" width="40" height="40">
						<span class="mini-text"><strong><?=html($request['username'])?></strong> &rarr; <a href="../pet.php?id=<?=html($request['petid'])?>"><?=html($request['petname'])?></a>
							<span class="meta"><?=formatDate($request['requestdate'])?></span></span>
						<span class="badge badge-<?=strtolower(html($request['status']))?>"><?=html($request['status'])?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>

	<section class="admin-card" aria-labelledby="latest-pets-heading">
		<div class="card-head">
			<h2 id="latest-pets-heading">Newest pets</h2>
			<a href="pets.php">All pets</a>
		</div>
		<ul class="mini-list">
			<?php foreach ($data['latestPets'] as $pet): ?>
				<li>
					<img class="mini-thumb" src="<?=html(imagePath($pet['image'], '../'))?>" alt="" width="40" height="40">
					<span class="mini-text"><a href="../pet.php?id=<?=html($pet['id'])?>"><?=html($pet['name'])?></a>
						<span class="meta"><?=html(placeLabel($pet))?></span></span>
					<span class="badge badge-<?=strtolower(html($pet['approval']))?>"><?=html(reviewLabel($pet['approval']))?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
</div>
