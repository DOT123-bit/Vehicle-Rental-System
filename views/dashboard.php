<?php
declare(strict_types=1);

$greeting = match (true) {
    (int) date('G') < 12 => 'Good morning',
    (int) date('G') < 18 => 'Good afternoon',
    default => 'Good evening',
};
?>
<section class="page-heading dashboard-heading">
    <div><span class="eyebrow"><span class="live-dot"></span> YOUR OPERATIONS, AT A GLANCE</span><h1><?= e($greeting) ?>, <?= e(explode(' ', $userName)[0]) ?>.</h1><p>Here's what's moving across your fleet today.</p></div>
    <a class="button button-dark" href="index.php?page=bookings&new=1"><span aria-hidden="true">＋</span> New booking</a>
</section>
<section class="metric-grid" aria-label="Fleet overview">
    <article class="metric-card metric-highlight"><div class="metric-top"><span>FLEET SIZE</span><span class="metric-mark">01</span></div><div class="metric-value"><?= $stats['fleet'] ?><span class="metric-unit">vehicles</span></div><div class="metric-foot"><span class="status-dot status-green"></span><?= $stats['available'] ?> ready to rent</div></article>
    <article class="metric-card"><div class="metric-top"><span>ON THE BOOKS</span><span class="metric-mark">02</span></div><div class="metric-value"><?= $stats['bookings'] ?><span class="metric-unit">reservations</span></div><div class="metric-foot">Confirmed or in progress</div></article>
    <article class="metric-card"><div class="metric-top"><span>YOUR CUSTOMERS</span><span class="metric-mark">03</span></div><div class="metric-value"><?= $stats['customers'] ?><span class="metric-unit">people</span></div><div class="metric-foot">Across all bookings</div></article>
    <article class="metric-card"><div class="metric-top"><span>REVENUE TO DATE</span><span class="metric-mark">04</span></div><div class="metric-value"><span class="metric-currency">$</span><?= number_format($stats['revenue'], 0) ?></div><div class="metric-foot">Active and completed rentals</div></article>
</section>
<section class="dashboard-grid">
    <div class="dashboard-primary">
        <div class="section-heading"><div><span class="eyebrow">UP NEXT</span><h2>Upcoming bookings</h2></div><a class="text-link" href="index.php?page=bookings">All bookings <span aria-hidden="true">→</span></a></div>
        <?php if ($upcoming): ?>
            <div class="booking-list">
                <?php foreach ($upcoming as $booking): ?>
                    <article class="booking-row">
                        <div class="booking-date"><strong><?= e(date('d', strtotime($booking['start_date']))) ?></strong><span><?= e(strtoupper(date('M', strtotime($booking['start_date'])))) ?></span></div>
                        <div class="booking-person"><strong><?= e($booking['customer_name']) ?></strong><span><?= e($booking['make'] . ' ' . $booking['model']) ?> <span class="muted-separator">·</span> <?= e(date('M j', strtotime($booking['start_date']))) ?>–<?= e(date('M j', strtotime($booking['end_date']))) ?></span></div>
                        <span class="status-pill status-<?= e(strtolower($booking['status'])) ?>"><?= e($booking['status']) ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-inline"><span>NO UPCOMING BOOKINGS</span><p>A quiet calendar is a good time to add a new reservation.</p><a class="text-link" href="index.php?page=bookings&new=1">Create a booking →</a></div>
        <?php endif; ?>
        <div class="section-heading fleet-heading"><div><span class="eyebrow">READY WHEN YOU ARE</span><h2>Fleet at a glance</h2></div><a class="text-link" href="index.php?page=vehicles">View fleet <span aria-hidden="true">→</span></a></div>
        <div class="mini-fleet-list">
            <?php foreach ($fleetPreview as $vehicle): ?>
                <article class="mini-fleet-row">
                    <div class="vehicle-thumb" <?php if ($vehicle['image_url']): ?>style="background-image:url('<?= e($vehicle['image_url']) ?>')"<?php endif; ?>></div>
                    <div class="mini-fleet-info"><strong><?= e($vehicle['make'] . ' ' . $vehicle['model']) ?></strong><span><?= e($vehicle['year'] . ' · ' . $vehicle['registration']) ?></span></div>
                    <span class="availability-tag <?= $vehicle['is_available'] ? 'is-available' : 'is-unavailable' ?>"><span class="status-dot"></span><?= $vehicle['is_available'] ? 'Available' : 'On rent' ?></span>
                    <span class="mini-rate">$<?= e(number_format((float) $vehicle['daily_rate'], 0)) ?><small> / day</small></span>
                </article>
            <?php endforeach; ?>
            <?php if (!$fleetPreview): ?><div class="empty-inline"><p>Your fleet is ready for its first vehicle.</p><a class="text-link" href="index.php?page=vehicles">Add a vehicle →</a></div><?php endif; ?>
        </div>
    </div>
    <aside class="dashboard-aside">
        <div class="aside-panel fleet-status-panel">
            <div class="section-heading compact-heading"><div><span class="eyebrow">YOUR GARAGE</span><h2>Fleet status</h2></div><span class="panel-number">01</span></div>
            <div class="fleet-ring-wrap"><div class="fleet-ring" style="--fleet-progress:<?= $stats['fleet'] ? round($stats['available'] / $stats['fleet'] * 100) : 0 ?>%"><div><strong><?= $stats['fleet'] ? round($stats['available'] / $stats['fleet'] * 100) : 0 ?><small>%</small></strong><span>AVAILABLE</span></div></div><div class="ring-legend"><span><i class="legend-dot legend-available"></i>Ready <b><?= $stats['available'] ?></b></span><span><i class="legend-dot legend-rented"></i>On rent <b><?= $stats['fleet'] - $stats['available'] ?></b></span></div></div>
            <div class="category-list">
                <?php foreach ($categoryCounts as $category): ?>
                    <div><span><?= e($category['category']) ?></span><span class="category-bar"><i style="width:<?= $stats['fleet'] ? round((int) $category['count'] / $stats['fleet'] * 100) : 0 ?>%"></i></span><b><?= (int) $category['count'] ?></b></div>
                <?php endforeach; ?>
                <?php if (!$categoryCounts): ?><p class="empty-category">Add vehicles to see fleet mix.</p><?php endif; ?>
            </div>
            <a class="aside-link" href="index.php?page=vehicles">Manage your fleet <span aria-hidden="true">↗</span></a>
        </div>
        <div class="aside-note"><span class="note-icon">↗</span><div><strong>Room for one more?</strong><p>Add a vehicle to grow your fleet.</p><a href="index.php?page=vehicles&new=1">Add a vehicle <span aria-hidden="true">→</span></a></div></div>
        <div class="category-note"><span class="eyebrow">ON THE ROAD</span><p>Good service is<br>always in season.</p><span class="note-decoration">M.</span></div>
    </aside>
</section>