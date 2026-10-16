<section class="page-heading">
    <div><span class="eyebrow">THE ROAD AHEAD</span><h1>Bookings.</h1><p>Reservations, returns, and everything in between.</p></div>
    <button class="button button-dark" type="button" data-dialog-open="booking-dialog"><span aria-hidden="true">＋</span> New booking</button>
</section>
<section class="table-section">
    <div class="table-toolbar"><div><span class="table-count"><?= count($bookings) ?> RESERVATIONS</span><span class="toolbar-divider"></span><span class="muted-copy">Track each journey from pickup to return.</span></div><label class="search-field"><span aria-hidden="true">⌕</span><input type="search" placeholder="Search bookings" aria-label="Search bookings" data-table-search></label></div>
    <div class="table-scroll"><table class="data-table" data-search-table><thead><tr><th>BOOKING</th><th>CUSTOMER</th><th>VEHICLE</th><th>DATES</th><th>TOTAL</th><th>STATUS</th></tr></thead><tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr data-search-row>
                <td><span class="booking-id">#<?= e(str_pad((string) $booking['id'], 4, '0', STR_PAD_LEFT)) ?></span></td>
                <td><div class="table-person"><strong><?= e($booking['customer_name']) ?></strong><span><?= e($booking['customer_phone']) ?></span></div></td>
                <td><div class="table-person"><strong><?= e($booking['make'] . ' ' . $booking['model']) ?></strong><span><?= e($booking['registration']) ?></span></div></td>
                <td><div class="table-person"><strong><?= e(date('M j', strtotime($booking['start_date']))) ?> – <?= e(date('M j, Y', strtotime($booking['end_date']))) ?></strong><span><?= (int) (new DateTimeImmutable($booking['start_date']))->diff(new DateTimeImmutable($booking['end_date']))->days + 1 ?> rental days</span></div></td>
                <td><strong>$<?= e(number_format((float) $booking['total_amount'], 2)) ?></strong></td>
                <td><form class="status-form" action="actions.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="intent" value="update_booking_status"><input type="hidden" name="return_page" value="bookings"><input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>"><label class="sr-only" for="status-<?= (int) $booking['id'] ?>">Status for booking <?= (int) $booking['id'] ?></label><select id="status-<?= (int) $booking['id'] ?>" class="status-select status-<?= e(strtolower($booking['status'])) ?>" name="status" data-auto-submit><?php foreach (['Confirmed', 'Active', 'Completed', 'Cancelled'] as $status): ?><option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select><noscript><button class="table-action" type="submit">Save</button></noscript></form></td>
            </tr>
        <?php endforeach; ?>
    </tbody></table></div>
    <?php if (!$bookings): ?><div class="empty-state"><span class="eyebrow">FIRST JOURNEY</span><h2>No bookings yet.</h2><p>Your next reservation is only a few details away.</p><button class="button button-primary" type="button" data-dialog-open="booking-dialog">Create a booking</button></div><?php endif; ?>
    <div class="no-results" data-no-results hidden>No bookings match that search.</div>
</section>
<dialog class="form-dialog" id="booking-dialog">
    <form class="dialog-card" method="post" action="actions.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="intent" value="add_booking"><input type="hidden" name="return_page" value="bookings">
        <div class="dialog-header"><div><span class="eyebrow">A NEW JOURNEY</span><h2>Create a booking</h2></div><button class="icon-button dialog-close" type="button" aria-label="Close dialog" data-dialog-close>×</button></div>
        <?php if ($bookableVehicles): ?>
            <div class="form-grid"><label class="field-wide">Vehicle<select name="vehicle_id" required><?php foreach ($bookableVehicles as $vehicle): ?><option value="<?= (int) $vehicle['id'] ?>"><?= e($vehicle['make'] . ' ' . $vehicle['model'] . ' · ' . $vehicle['registration'] . ' · $' . number_format((float) $vehicle['daily_rate'], 2) . '/day') ?></option><?php endforeach; ?></select></label><label>Customer name<input name="customer_name" placeholder="Alex Morgan" required maxlength="120"></label><label>Phone number<input name="customer_phone" type="tel" placeholder="+1 555 000 1234" required maxlength="32"></label><label>Email address<input name="customer_email" type="email" placeholder="alex@example.com" maxlength="190"></label><label>Pickup date<input name="start_date" type="date" min="<?= date('Y-m-d') ?>" required data-start-date></label><label>Return date<input name="end_date" type="date" min="<?= date('Y-m-d') ?>" required data-end-date></label><label class="field-wide">Notes <span class="optional-label">OPTIONAL</span><textarea name="notes" rows="2" maxlength="500" placeholder="Pickup details or special requests"></textarea></label></div>
            <div class="dialog-hint">The total is calculated from the vehicle's daily rate and rental dates.</div>
            <div class="dialog-actions"><button class="button button-quiet" type="button" data-dialog-close>Cancel</button><button class="button button-primary" type="submit">Confirm booking <span aria-hidden="true">→</span></button></div>
        <?php else: ?>
            <div class="empty-inline"><p>Add an available vehicle before creating a booking.</p><a class="text-link" href="index.php?page=vehicles">Go to your fleet →</a></div>
        <?php endif; ?>
    </form>
</dialog>