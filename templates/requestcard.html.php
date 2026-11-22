<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) <a>: mailto: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
// The content of one adoption request card on requests.php. Needs $request (a row from requestSelect()).
// It is its own template because the same HTML is used when the page loads AND when js/main.js
// replaces the card after a stage button was pressed (see requests.php, JSON answer).
?>
<?php // data-stage: read by the stage filter in js/main.js (it is replaced together with the card after a move) ?>
<h2 data-stage="<?=html($request['status'])?>"><a href="pet.php?id=<?=html($request['petid'])?>"><?=html($request['petname'])?></a>
	<span class="badge badge-<?=strtolower(html($request['status']))?>"><?=html($request['status'])?></span></h2>
<p class="meta">
	From <strong><?=html($request['username'])?></strong>
	(<a href="mailto:<?=html($request['useremail'])?>"><?=html($request['useremail'])?></a>)
	&middot; sent <?=formatDate($request['requestdate'])?>
</p>
<blockquote class="request-message">&ldquo;<?=nl2br(html($request['message']))?>&rdquo;</blockquote>

<?php if ($request['hometype']): ?>
	<p class="meta"><strong>Adopter profile:</strong></p>
	<ul class="profile-summary">
		<li><?=html($request['hometype'])?></li>
		<li><?=html($request['experience'])?></li>
		<li>Budget <?=html($request['budget'])?> / month</li>
		<li><?=$request['householdagrees'] ? 'Household agrees' : 'Household has not agreed yet'?></li>
		<?php if ($request['otherpets']): ?><li>Other pets: <?=html($request['otherpets'])?></li><?php endif; ?>
	</ul>
<?php else: ?>
	<p class="meta">This person has not filled in an adopter profile.</p>
<?php endif; ?>

<?php include __DIR__ . '/requesttracker.html.php'; ?>

<?php // Only the moves that are allowed from the current stage (see requestActions in includes/Functions.php) ?>
<form action="requests.php" method="post" class="form review-form js-ajax-form">
	<?=csrfField()?>
	<input type="hidden" name="id" value="<?=html($request['id'])?>">
	<div class="form-actions">
		<?php foreach (requestActions($request['status']) as $stage => $buttonLabel): ?>
			<button class="btn btn-small<?=$stage == 'Approved' ? '' : ' btn-outline'?>" type="submit" name="action" value="<?=$stage?>"><?=html($buttonLabel)?><span class="visually-hidden"> request from <?=html($request['username'])?></span></button>
		<?php endforeach; ?>
	</div>
</form>
