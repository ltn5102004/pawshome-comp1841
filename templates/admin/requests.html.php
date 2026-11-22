<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - Coyier, C. (2011) Responsive Data Tables, CSS-Tricks. https://css-tricks.com/responsive-data-tables/
//   - MDN Web Docs (no date) <details>: The Details disclosure element (adopter profile opens on demand). https://developer.mozilla.org/en-US/docs/Web/HTML/Element/details
//   - MDN Web Docs (no date) <a>: mailto: and tel: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Adoption requests</h1>
		<p class="admin-sub">Owners and shelter staff answer their own requests. You can see all of them and step in when nobody else can.</p>
	</div>
</header>

<?php if (empty($requests)): ?>
	<p class="empty">There are no adoption requests yet.</p>
<?php else: ?>
	<section class="admin-card">
		<?php $filterTarget = 'requests-table'; $filterLabel = 'Search by pet, person, status or shelter'; include __DIR__ . '/tablefilter.html.php'; ?>
		<div class="table-wrap">
			<table class="admin-table" id="requests-table">
				<caption class="visually-hidden">Adoption requests</caption>
				<thead>
					<tr><th scope="col">Pet</th><th scope="col">Adopter</th><th scope="col">Message</th><th scope="col">Sent</th><th scope="col">Stage</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
				</thead>
				<tbody>
					<?php foreach ($requests as $request): ?>
						<?php // data-request, .js-status and .js-actions are used by js/main.js to update the row without reloading ?>
						<tr data-request="<?=html($request['id'])?>">
							<td data-label="Pet">
								<div class="cell-with-thumb">
									<img class="thumb" src="<?=html(imagePath($request['petimage'], '../'))?>" alt="" width="56" height="42">
									<div>
										<a class="cell-title" href="../pet.php?id=<?=html($request['petid'])?>"><?=html($request['petname'])?></a>
										<span class="cell-sub">Handled by: <?=html(handlerLabel($request))?></span>
									</div>
								</div>
							</td>
							<td data-label="Adopter">
								<span class="cell-title"><?=html($request['username'])?></span>
								<a class="cell-sub" href="mailto:<?=html($request['useremail'])?>"><?=html($request['useremail'])?></a>
								<?php // Adopter profile comes from the LEFT JOIN in requestSelect() ?>
								<?php if ($request['hometype']): ?>
									<details class="cell-details">
										<summary>Adopter profile</summary>
										<ul class="profile-summary">
											<li><?=html($request['hometype'])?></li>
											<li><?=html($request['experience'])?></li>
											<li>Budget <?=html($request['budget'])?> / month</li>
											<li><?=$request['householdagrees'] ? 'Household agrees' : 'Household has not agreed yet'?></li>
											<?php if ($request['otherpets']): ?><li>Other pets: <?=html($request['otherpets'])?></li><?php endif; ?>
										</ul>
									</details>
								<?php else: ?>
									<span class="cell-sub">No adopter profile yet</span>
								<?php endif; ?>
							</td>
							<td data-label="Message" class="cell-message"><?=html($request['message'])?></td>
							<td data-label="Sent"><?=formatDate($request['requestdate'])?></td>
							<td data-label="Stage"><span class="js-status"><span class="badge badge-<?=strtolower(html($request['status']))?>"><?=html($request['status'])?></span></span></td>
							<td class="actions" data-label="Actions">
								<?php // .js-status and .js-actions are swapped by js/main.js after a button press (no page reload) ?>
								<div class="js-actions"><?php include __DIR__ . '/requestactions.html.php'; ?></div>
								<div class="action-wrap"><span class="action-note" role="status" aria-live="polite"></span></div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
<?php endif; ?>
