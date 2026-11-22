<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) The label element and Client-side form validation. https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Review listings</h1>
		<p class="admin-sub">New listings are hidden from the public site until you approve them. Check that the shelter shown matches who posted it.</p>
	</div>
</header>

<?php if (empty($pets)): ?>
	<p class="all-clear admin-card"><?=icon('check')?> Nothing is waiting for review.</p>
<?php else: ?>
	<div class="review-list">
		<?php foreach ($pets as $pet): ?>
			<article class="request-card review-card admin-card">
				<img class="thumb" src="<?=html(imagePath($pet['image'], '../'))?>" alt="" width="96" height="72">
				<div class="request-body">
					<h2><a href="../pet.php?id=<?=html($pet['id'])?>"><?=html($pet['name'])?></a>
						<span class="meta">&middot; <?=html($pet['speciesname'])?>, <?=html($pet['age'])?> <?=$pet['age'] == 1 ? 'year' : 'years'?>, <?=html($pet['gender'])?></span></h2>
					<p class="meta">
						Posted by <strong><?=html($pet['postedby'] ?? 'a deleted user')?></strong> on <?=formatDate($pet['dateadded'])?>
						&middot; <?=html(placeLabel($pet))?>
						<?php include __DIR__ . '/../trustbadge.html.php'; ?>
					</p>
					<?php // Tell the admin what the review needs to check, depending on who posted the listing ?>
					<?php if (isPrivateRehoming($pet)): ?>
						<p class="alert alert-warning review-warning">Private rehoming by a member. There is no shelter to vouch for it, so check the photo and description carefully.</p>
					<?php elseif (!$pet['shelterverified']): ?>
						<p class="alert alert-warning review-warning"><?=html($pet['sheltername'])?> has not been verified yet. Verify the shelter in <a href="shelters.php">Shelters</a> and its staff can publish listings without review.</p>
					<?php endif; ?>
					<p><?=nl2br(html($pet['description']))?></p>

					<form action="approvals.php" method="post" class="form review-form">
						<?=csrfField()?>
						<input type="hidden" name="id" value="<?=html($pet['id'])?>">
						<div class="field">
							<label for="note-<?=html($pet['id'])?>">Reason (needed to reject, shown to the owner)</label>
							<input type="text" id="note-<?=html($pet['id'])?>" name="reviewnote" maxlength="255" class="review-note" placeholder="e.g. Photo is not of the pet described">
						</div>
						<div class="form-actions">
							<button class="btn" type="submit" name="decision" value="Approved">Approve<span class="visually-hidden"> <?=html($pet['name'])?></span></button>
							<button class="btn btn-danger" type="submit" name="decision" value="Rejected">Reject<span class="visually-hidden"> <?=html($pet['name'])?></span></button>
						</div>
					</form>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
