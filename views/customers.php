<section class="page-heading">
    <div><span class="eyebrow">THE PEOPLE BEHIND THE JOURNEYS</span><h1>Customers.</h1><p>A little more familiar with every trip.</p></div>
</section>
<section class="table-section">
    <div class="table-toolbar"><div><span class="table-count"><?= count($customers) ?> CUSTOMERS</span><span class="toolbar-divider"></span><span class="muted-copy">Every customer is added automatically with their first booking.</span></div><label class="search-field"><span aria-hidden="true">⌕</span><input type="search" placeholder="Search customers" aria-label="Search customers" data-table-search></label></div>
    <div class="table-scroll"><table class="data-table" data-search-table><thead><tr><th>CUSTOMER</th><th>CONTACT</th><th>BOOKINGS</th><th>LIFETIME VALUE</th><th>FIRST SEEN</th></tr></thead><tbody>
        <?php foreach ($customers as $customer): ?>
            <?php $customerInitials = implode('', array_map(static fn (string $part): string => strtoupper(substr($part, 0, 1)), array_slice(explode(' ', $customer['name']), 0, 2))); ?>
            <tr data-search-row><td><div class="table-vehicle"><span class="customer-avatar"><?= e($customerInitials) ?></span><div><strong><?= e($customer['name']) ?></strong><span>Customer #<?= e(str_pad((string) $customer['id'], 4, '0', STR_PAD_LEFT)) ?></span></div></div></td><td><div class="table-person"><strong><?= e($customer['email'] ?: 'No email added') ?></strong><span><?= e($customer['phone']) ?></span></div></td><td><?= (int) $customer['booking_count'] ?></td><td><strong>$<?= e(number_format((float) $customer['lifetime_value'], 2)) ?></strong></td><td><?= e(date('M j, Y', strtotime($customer['created_at']))) ?></td></tr>
        <?php endforeach; ?>
    </tbody></table></div>
    <?php if (!$customers): ?><div class="empty-state"><span class="eyebrow">YOUR COMMUNITY</span><h2>Every great trip begins with a hello.</h2><p>Customers appear here when you create their first booking.</p><a class="button button-primary" href="index.php?page=bookings&new=1">Create a booking</a></div><?php endif; ?>
    <div class="no-results" data-no-results hidden>No customers match that search.</div>
</section>