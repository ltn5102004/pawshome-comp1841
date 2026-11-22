<?php
// Source:
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
// The buttons of one row in admin/requests.php. Needs $request (a row from requestSelect()).
// Its own template because the same HTML is used when the page loads AND when js/main.js swaps it in
// after a stage button was pressed.
?>
<form action="requests.php" method="post" class="inline-form js-ajax-form">
	<?=csrfField()?>
	<input type="hidden" name="id" value="<?=html($request['id'])?>">
	<?php // Only the moves that are allowed from the current stage (see requestActions in includes/Functions.php) ?>
	<?php foreach (requestActions($request['status']) as $stage => $buttonLabel): ?>
		<button class="btn btn-small<?=$stage == 'Approved' ? '' : ' btn-outline'?>" type="submit" name="action" value="<?=$stage?>"><?=html($buttonLabel)?><span class="visually-hidden"> request from <?=html($request['username'])?></span></button>
	<?php endforeach; ?>
</form>
<form action="requests.php" method="post" class="inline-form" data-confirm="Delete this request?">
	<?=csrfField()?>
	<input type="hidden" name="id" value="<?=html($request['id'])?>">
	<button class="btn btn-small btn-danger" type="submit" name="action" value="delete">Delete</button>
</form>
