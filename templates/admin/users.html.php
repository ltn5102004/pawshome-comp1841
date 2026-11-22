<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Coyier, C. (2011) Responsive Data Tables, CSS-Tricks. https://css-tricks.com/responsive-data-tables/
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - MDN Web Docs (no date) <a>: mailto: and tel: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Users</h1>
		<p class="admin-sub">Make someone an admin, link a member to the shelter they work for (that makes them its staff), or remove an account.</p>
	</div>
</header>

<section class="admin-card">
	<?php $filterTarget = 'users-table'; $filterLabel = 'Search by name, e-mail, role or shelter'; include __DIR__ . '/tablefilter.html.php'; ?>
	<div class="table-wrap">
		<table class="admin-table" id="users-table">
			<caption class="visually-hidden">Users</caption>
			<thead><tr><th scope="col">User</th><th scope="col">Joined</th><th scope="col">Role</th><th scope="col">Works for</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
			<tbody>
				<?php foreach ($users as $user): ?>
					<tr>
						<td data-label="User">
							<span class="cell-title"><?=html($user['name'])?></span>
							<a class="cell-sub" href="mailto:<?=html($user['email'])?>"><?=html($user['email'])?></a>
						</td>
						<td data-label="Joined"><?=formatDate($user['datejoined'])?></td>
						<td data-label="Role"><span class="badge badge-<?=html($user['role'])?>"><?=html(ucfirst($user['role']))?></span><?php if ($user['shelterid']): ?> <span class="badge badge-staff">Staff</span><?php endif; ?></td>
						<td data-label="Works for">
							<?php if ($user['role'] == 'admin'): ?>
								<span class="meta">PawsHome</span>
							<?php elseif ($user['id'] == currentUserId()): ?>
								<?=html($user['sheltername'] ?? 'No shelter')?>
							<?php else: ?>
								<?php // Choose a shelter to make this person its staff, or "No shelter" to remove the link ?>
								<form action="users.php" method="post" class="inline-form shelter-assign">
									<?=csrfField()?>
									<input type="hidden" name="id" value="<?=html($user['id'])?>">
									<label class="visually-hidden" for="shelter-<?=html($user['id'])?>">Shelter for <?=html($user['name'])?></label>
									<select id="shelter-<?=html($user['id'])?>" name="shelterid">
										<option value="">No shelter</option>
										<?php foreach ($shelters as $shelter): ?>
											<option value="<?=html($shelter['id'])?>" <?=$user['shelterid'] == $shelter['id'] ? 'selected' : ''?>><?=html($shelter['name'])?></option>
										<?php endforeach; ?>
									</select>
									<button class="btn btn-small btn-outline" type="submit" name="assignshelter" value="1">Save<span class="visually-hidden"> shelter for <?=html($user['name'])?></span></button>
								</form>
							<?php endif; ?>
						</td>
						<td class="actions" data-label="Actions">
							<?php if ($user['id'] == currentUserId()): ?>
								<span class="meta">This is you</span>
							<?php else: ?>
								<form action="users.php" method="post" class="inline-form">
									<?=csrfField()?>
									<input type="hidden" name="id" value="<?=html($user['id'])?>">
									<?php if ($user['role'] == 'member'): ?>
										<button class="btn btn-small btn-outline" type="submit" name="role" value="admin">Make admin<span class="visually-hidden"> <?=html($user['name'])?></span></button>
									<?php else: ?>
										<button class="btn btn-small btn-outline" type="submit" name="role" value="member">Make member<span class="visually-hidden"> <?=html($user['name'])?></span></button>
									<?php endif; ?>
								</form>
								<form action="users.php" method="post" class="inline-form" data-confirm="Delete <?=html($user['name'])?> and their adoption requests?">
									<?=csrfField()?>
									<input type="hidden" name="id" value="<?=html($user['id'])?>">
									<button class="btn btn-small btn-danger" type="submit" name="delete" value="1">Delete<span class="visually-hidden"> <?=html($user['name'])?></span></button>
								</form>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
