<section class="page-heading">
    <div><span class="eyebrow">YOUR GARAGE</span><h1>The fleet.</h1><p>Every vehicle, ready for its next set of keys.</p></div>
    <button class="button button-dark" type="button" data-dialog-open="vehicle-dialog"><span aria-hidden="true">＋</span> Add vehicle</button>
</section>
<section class="table-section">
    <div class="table-toolbar"><div><span class="table-count"><?= count($vehicles) ?> VEHICLES</span><span class="toolbar-divider"></span><span class="muted-copy">Manage your vehicles and availability.</span></div><label class="search-field"><span aria-hidden="true">⌕</span><input type="search" placeholder="Search the fleet" aria-label="Search vehicles" data-table-search></label></div>
    <div class="table-scroll"><table class="data-table" data-search-table><thead><tr><th>VEHICLE</th><th>REGISTRATION</th><th>CATEGORY</th><th>RATE / DAY</th><th>STATUS</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
        <?php foreach ($vehicles as $vehicle): ?>
            <tr data-search-row>
                <td><div class="table-vehicle"><div class="vehicle-thumb" <?php if ($vehicle['image_url']): ?>style="background-image:url('<?= e($vehicle['image_url']) ?>')"<?php endif; ?>></div><div><strong><?= e($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']) ?></strong><span><?= e($vehicle['transmission'] . ' · ' . $vehicle['seats']) ?> seats</span></div></div></td>
                <td><span class="plate-number"><?= e($vehicle['registration']) ?></span></td><td><?= e($vehicle['category']) ?></td><td><strong>$<?= e(number_format((float) $vehicle['daily_rate'], 2)) ?></strong></td>
                <td><span class="availability-tag <?= $vehicle['is_available'] ? 'is-available' : 'is-unavailable' ?>"><span class="status-dot"></span><?= $vehicle['is_available'] ? 'Available' : 'Unavailable' ?></span></td>
                <td><form action="actions.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="intent" value="toggle_vehicle"><input type="hidden" name="return_page" value="vehicles"><input type="hidden" name="vehicle_id" value="<?= (int) $vehicle['id'] ?>"><button class="table-action" type="submit"><?= $vehicle['is_available'] ? 'Mark unavailable' : 'Mark available' ?></button></form></td>
            </tr>
        <?php endforeach; ?>
    </tbody></table></div>
    <?php if (!$vehicles): ?><div class="empty-state"><span class="eyebrow">A FRESH START</span><h2>Your fleet starts here.</h2><p>Add your first vehicle to begin taking bookings.</p><button class="button button-primary" type="button" data-dialog-open="vehicle-dialog">Add a vehicle</button></div><?php endif; ?>
    <div class="no-results" data-no-results hidden>No vehicles match that search.</div>
</section>
<dialog class="form-dialog" id="vehicle-dialog">
    <form class="dialog-card" method="post" action="actions.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="intent" value="add_vehicle"><input type="hidden" name="return_page" value="vehicles">
        <div class="dialog-header"><div><span class="eyebrow">GROW YOUR GARAGE</span><h2>Add a vehicle</h2></div><button class="icon-button dialog-close" type="button" aria-label="Close dialog" data-dialog-close>×</button></div>
        <div class="form-grid"><label>Make<input name="make" placeholder="Toyota" required maxlength="80"></label><label>Model<input name="model" placeholder="Corolla" required maxlength="100"></label><label>Year<input name="year" type="number" min="1980" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" required></label><label>Registration<input name="registration" placeholder="ABC-1234" required maxlength="24"></label><label>Category<select name="category" required><option value="Economy">Economy</option><option value="Sedan">Sedan</option><option value="SUV">SUV</option><option value="Luxury">Luxury</option><option value="Van">Van</option></select></label><label>Transmission<select name="transmission"><option>Automatic</option><option>Manual</option></select></label><label>Seats<input name="seats" type="number" min="1" max="12" value="5" required></label><label>Daily rate<input name="daily_rate" type="number" min="1" step="0.01" placeholder="55.00" required></label></div>
        <div class="dialog-actions"><button class="button button-quiet" type="button" data-dialog-close>Cancel</button><button class="button button-primary" type="submit">Add to fleet <span aria-hidden="true">→</span></button></div>
    </form>
</dialog>