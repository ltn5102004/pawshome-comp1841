<?php
// Source:
//   - W3C (2023) Accessible Rich Internet Applications (WAI-ARIA) 1.2 - aria-current="step". https://www.w3.org/TR/wai-aria-1.2/
//   - MDN Web Docs (no date) <ol>: The Ordered List element. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/ol
// Progress tracker for one adoption request. Needs $request (a row from requestSelect()).
// The bar only shows what has REALLY happened, using the days saved by moveRequest():
//   New          request sent, waiting for a reply
//   Interviewing request sent, interview in progress (since interviewdate)
//   Approved     request sent, interview (or "skipped" when no interview was ever recorded), approved on decisiondate;
//                the last step says "Adopted" once the pet has gone to its new home
//   Rejected     request sent, [interview, if one took place], not successful on decisiondate

$status = $request['status'];
$day = function ($date) {
	return $date ? formatDate($date) : '';
};
// each step: [label, state, small text under the label]
// state = done | current | rejected | skipped | '' (not reached yet)
$steps = [['Request sent', 'done', $day($request['requestdate'])]];

if ($status == 'Rejected') {
	if ($request['interviewdate']) {
		$steps[] = ['Interview', 'done', $day($request['interviewdate'])];
	}
	$steps[] = ['Not successful', 'rejected', $day($request['decisiondate'])];
} else {
	if ($status == 'New') {
		$steps[] = ['Interview', '', ''];
	} elseif ($status == 'Interviewing') {
		$steps[] = ['Interview', 'current', $day($request['interviewdate'])];
	} elseif ($request['interviewdate']) {
		$steps[] = ['Interview', 'done', $day($request['interviewdate'])];
	} else {
		// approved without an interview ever being recorded: do not pretend there was one
		$steps[] = ['Interview', 'skipped', 'skipped'];
	}
	if ($status == 'Approved') {
		$steps[] = [$request['petstatus'] == 'Adopted' ? 'Adopted - welcome home!' : 'Approved', 'done', $day($request['decisiondate'])];
	} else {
		$steps[] = ['Decision', '', ''];
	}
}

// the step screen readers announce as "current step": the last one that has been reached
$active = 0;
foreach ($steps as $i => $step) {
	if ($step[1] != '' && $step[1] != 'skipped') {
		$active = $i;
	}
}
?>
<ol class="tracker<?=$status == 'Rejected' ? ' tracker-rejected' : ''?>" aria-label="Progress of the request for <?=html($request['petname'])?>">
	<?php foreach ($steps as $i => $step): ?>
		<li class="<?=$step[1]?>" <?=$i == $active ? 'aria-current="step"' : ''?>>
			<span class="tracker-dot" aria-hidden="true"><?=$step[1] == 'done' ? '&#10003;' : ($step[1] == 'rejected' ? '&#10007;' : ($step[1] == 'skipped' ? '&ndash;' : $i + 1))?></span>
			<span class="tracker-label"><?=html($step[0])?></span>
			<?php if ($step[2] != ''): ?><span class="tracker-date"><?=html($step[2])?></span><?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
<?php if ($status == 'New'): ?>
	<p class="meta tracker-note">Waiting for a reply.</p>
<?php endif; ?>
