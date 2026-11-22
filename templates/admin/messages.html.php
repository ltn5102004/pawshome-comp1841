<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) <a>: mailto: and tel: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Messages</h1>
		<p class="admin-sub">Sent with the contact form. Reply by e-mail, then delete the message when it is dealt with.</p>
	</div>
</header>

<?php if (empty($messages)): ?>
	<p class="all-clear admin-card"><?=icon('check')?> No messages. The inbox is empty.</p>
<?php else: ?>
	<div class="message-list">
		<?php foreach ($messages as $message): ?>
			<article class="admin-card message">
				<div class="card-head">
					<h2><?=html($message['subject'])?></h2>
					<time class="meta" datetime="<?=html($message['datesent'])?>"><?=date('j M Y, H:i', strtotime($message['datesent']))?></time>
				</div>
				<p class="meta">From <strong><?=html($message['name'])?></strong> &lt;<?=html($message['email'])?>&gt;</p>
				<p class="message-body"><?=nl2br(html($message['body']))?></p>
				<div class="form-actions">
					<a class="btn btn-small" href="mailto:<?=html($message['email'])?>?subject=Re: <?=rawurlencode($message['subject'])?>">Reply by e-mail<span class="visually-hidden"> to <?=html($message['name'])?></span></a>
					<form action="messages.php" method="post" class="inline-form" data-confirm="Delete this message?">
						<?=csrfField()?>
						<input type="hidden" name="delete" value="<?=html($message['id'])?>">
						<button class="btn btn-small btn-danger" type="submit">Delete<span class="visually-hidden"> message from <?=html($message['name'])?></span></button>
					</form>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
