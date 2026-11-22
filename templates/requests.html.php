<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) <a>: mailto: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
//   - MDN Web Docs (no date) aria-pressed (filter buttons). https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-pressed
?>
<?php
$role = isShelterStaff() ? 'staff' : 'owner';
// How many requests are in each stage, for the filter buttons
$stageCounts = array_count_values(array_column($requests, 'status'));
?>
<header class="role-hero role-<?=$role?> role-hero-small">
	<div class="role-hero-main">
		<span class="avatar" aria-hidden="true"><?=icon('send')?></span>
		<div>
			<p class="role-chip"><?=$role == 'staff' ? 'Shelter staff &middot; ' . html(currentUser()['sheltername']) : 'Pet owner'?></p>
			<h1>Requests for my pets</h1>
			<p class="role-hero-text">
				<?php if ($role == 'staff'): ?>
					Adoption requests for the pets of your shelter and for any pet you listed yourself.
				<?php else: ?>
					People who would like to adopt a pet you listed. Contact them, then move each request through the stages.
				<?php endif; ?>
			</p>
		</div>
	</div>
</header>

<?php if (empty($requests)): ?>
	<p class="empty">Nobody has asked to adopt your pets yet.</p>
<?php else: ?>
	<?php // How the stages work, in one line, so nobody has to guess ?>
	<ol class="stage-help" aria-label="How a request moves">
		<li><span class="badge badge-new">New</span> read it</li>
		<li><span class="badge badge-interviewing">Interviewing</span> call or meet the person</li>
		<li><span class="badge badge-approved">Approved</span> the pet is reserved for them</li>
	</ol>

	<?php // Filter buttons. Hidden until js/main.js shows them (without JavaScript every request is simply listed). ?>
	<div class="stage-filter" hidden role="group" aria-label="Show requests by stage">
		<button type="button" class="chip" data-stage-filter="" aria-pressed="true">All <span class="chip-count"><?=count($requests)?></span></button>
		<?php foreach (requestStages() as $stage): ?>
			<button type="button" class="chip chip-<?=strtolower($stage)?>" data-stage-filter="<?=$stage?>" aria-pressed="false"><?=$stage?> <span class="chip-count"><?=$stageCounts[$stage] ?? 0?></span></button>
		<?php endforeach; ?>
	</div>
	<p class="stage-filter-empty empty" hidden>No requests in this stage.</p>

	<div class="review-list">
		<?php foreach ($requests as $request): ?>
			<?php // data-request lets js/main.js find the card; .js-request-main is what gets replaced after a button press ?>
			<article class="request-card review-card stage-<?=strtolower(html($request['status']))?>" data-request="<?=html($request['id'])?>">
				<img class="thumb" src="<?=html(imagePath($request['petimage']))?>" alt="" width="96" height="72">
				<div class="request-body">
					<div class="js-request-main">
						<?php include __DIR__ . '/requestcard.html.php'; ?>
					</div>
					<?php // The message "moved to ..." appears here for 5 seconds, under the buttons ?>
					<div class="action-wrap"><span class="action-note" role="status" aria-live="polite"></span></div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
