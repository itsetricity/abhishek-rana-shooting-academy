<?php
$events=public_records('events');
usort($events,fn($a,$b)=>strcmp(field($a,'date','9999') ?: '9999',field($b,'date','9999') ?: '9999'));
?>
<div class="event-hub" data-calendar>
 <div class="calendar-card"><div class="calendar-top"><div><span class="eyebrow">Your next visit</span><h3 data-month-title><?=e(date('F Y'))?></h3></div><div class="calendar-controls"><button type="button" data-prev aria-label="Previous month">←</button><button type="button" data-today>This month</button><button type="button" data-next aria-label="Next month">→</button></div></div>
 <div class="calendar-week" aria-hidden="true"><?php foreach(['M','T','W','T','F','S','S'] as $day): ?><span><?=$day?></span><?php endforeach ?></div>
 <div class="calendar-days" data-days aria-label="Event dates"></div><p class="calendar-key"><span></span> Event scheduled <i></i> Today</p><noscript><p>Enable JavaScript to browse the month calendar. All published events are listed alongside it.</p></noscript></div>
 <div class="event-agenda"><div class="agenda-heading"><h3>Coming up at the academy</h3><button type="button" class="text-link" data-show-all>Show all events ↗</button></div><p data-calendar-status role="status" aria-live="polite">Select a marked date to see its events.</p>
 <div class="event-items"><?php foreach($events as $event): $date=field($event,'date'); ?><article class="event-card" data-event-date="<?=e($date)?>"><div class="event-date"><strong><?=$date?e(date('d',strtotime($date))):'TBA'?></strong><span><?=$date?e(date('M',strtotime($date))):'Date'?></span></div><div><span class="eyebrow"><?=e(field($event,'category','Academy session'))?></span><h4><a href="<?=detail_link($event)?>"><?=e($event['title'])?> ↗</a></h4><p><?=e(field($event,'summary'))?></p><?php if(field($event,'time')): ?><span class="event-meta"><?=e(field($event,'time'))?></span><?php endif ?></div></article><?php endforeach ?></div>
 <p class="calendar-empty" data-empty <?=$events?'hidden':''?>>No confirmed events yet. New dates will appear here as soon as they are announced.</p><a class="text-link" href="/demo-class">Request your own demo session <?=icon('arrow')?></a></div>
</div>
