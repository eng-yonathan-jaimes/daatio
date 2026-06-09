<?php $__env->startSection('title', __('messages.new_transaction_title')); ?>

<?php
$clients = \Modules\Clients\app\Models\Client::where('client_active', true)
    ->orderBy('client_name')
    ->get()
    ->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->client_name . ' ' . $c->client_last_name,
        'phone' => $c->client_phone_number,
        'doc' => $c->client_document_number ?? '',
    ]);

$products = \Modules\Products\app\Models\Product::orderBy('product_name')
    ->get()
    ->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->product_name,
        'stock' => (float) $p->product_weight,
        'state' => $p->product_state,
    ]);
?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <h1><?php echo e(__('messages.new_transaction_title')); ?></h1>
    <p><?php echo e(__('messages.new_transaction_desc')); ?></p>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?>

    <?php if($errors->has('items') && is_array($errors->get('items'))): ?>
        <?php $__currentLoopData = $errors->get('items'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemErrors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_array($itemErrors)): ?>
                <?php $__currentLoopData = $itemErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <br><?php echo e($e); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('transactions.quick.store')); ?>" id="tx-form">
    <?php echo csrf_field(); ?>

    <div class="card" style="margin-bottom:1.5rem;max-width:700px;">
        <div style="display:flex;gap:1.5rem;align-items:end;">
            <div class="form-group" style="flex:1;margin-bottom:0;position:relative;">
                <label for="client-search"><?php echo e(__('messages.client')); ?> <span style="color:#DC2626;">*</span></label>
                <input type="text" id="client-search" autocomplete="off" placeholder="<?php echo e(__('messages.type_name_phone')); ?>" style="width:100%;">
                <input type="hidden" name="client_id" id="client-id" value="<?php echo e(old('client_id')); ?>">
                <div class="search-dropdown" id="client-dropdown"></div>
            </div>
            <div class="form-group" style="flex:1;margin-bottom:0;">
                <label for="direction"><?php echo e(__('messages.direction')); ?> <span style="color:#DC2626;">*</span></label>
                <select id="direction" name="direction" required style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                    <option value=""><?php echo e(__('messages.select_direction')); ?></option>
                    <option value="buying" <?php echo e(old('direction') == 'buying' ? 'selected' : ''); ?>><?php echo e(__('messages.buying_from_client')); ?></option>
                    <option value="selling" <?php echo e(old('direction') == 'selling' ? 'selected' : ''); ?>><?php echo e(__('messages.selling_to_client')); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem;max-width:700px;">
        <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.line_items')); ?></div>
        <div class="form-group" style="position:relative;">
            <label for="product-search"><?php echo e(__('messages.add_product')); ?></label>
            <input type="text" id="product-search" autocomplete="off" placeholder="<?php echo e(__('messages.type_product_name')); ?>" style="width:100%;">
            <div class="search-dropdown" id="product-dropdown"></div>
        </div>

        <table id="items-table" style="margin-top:1rem;display:none;width:100%;">
            <thead>
                <tr>
                    <th><?php echo e(__('messages.product_name')); ?></th>
                    <th style="width:130px;"><?php echo e(__('messages.weight_label')); ?></th>
                    <th style="width:80px;"><?php echo e(__('messages.in_stock_label')); ?></th>
                    <th style="width:40px;"></th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
        </table>
        <div id="items-empty" style="text-align:center;color:var(--color-text-muted);padding:1rem;"><?php echo e(__('messages.no_items_added')); ?></div>
    </div>

    <div class="card" style="max-width:700px;margin-bottom:1.5rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="total_value_display"><?php echo e(__('messages.total_value')); ?> <span style="color:#DC2626;">*</span></label>
            <input type="text" id="total_value_display" value="<?php echo e(old('total_value')); ?>" placeholder="0" required style="max-width:300px;font-family:var(--font-mono);">
            <input type="hidden" name="total_value" id="total_value" value="<?php echo e(old('total_value')); ?>">
        </div>
    </div>

    <div class="card" style="max-width:700px;">
        <div class="form-group">
            <label for="amount_paid_display"><?php echo e(__('messages.amount_paid_now')); ?></label>
            <div style="display:flex;gap:0.5rem;align-items:end;">
                <input type="text" id="amount_paid_display" value="<?php echo e(old('amount_paid', '0')); ?>" placeholder="0" style="max-width:200px;font-family:var(--font-mono);">
                <button type="button" class="btn btn-outline" style="font-size:0.8rem;padding:0.5rem 0.75rem;" onclick="settleFull()"><?php echo e(__('messages.pay_total')); ?></button>
            </div>
            <input type="hidden" name="amount_paid" id="amount_paid" value="<?php echo e(old('amount_paid', '0')); ?>">
            <div style="font-size:0.8rem;color:var(--color-text-muted);margin-top:0.25rem;"><?php echo e(__('messages.amount_paid_hint')); ?></div>
        </div>

        <div id="balance-summary" style="background:var(--color-active-nav-bg);border-radius:8px;padding:1rem;margin-top:0.5rem;display:none;">
            <div style="font-size:0.85rem;color:var(--color-text-muted);margin-bottom:0.25rem;"><?php echo e(__('messages.summary')); ?></div>
            <div id="summary-text" style="font-weight:600;"></div>
        </div>

        <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary" style="font-size:1rem;padding:0.75rem 2rem;"><?php echo e(__('messages.record_transaction')); ?></button>
        </div>
    </div>
</form>

<script>
const clients = <?php echo json_encode($clients, 15, 512) ?>;
const products = <?php echo json_encode($products, 15, 512) ?>;
let itemIndex = 0;
const LANG = {
    settled: <?php echo json_encode(__('messages.desc_settled'), 15, 512) ?>,
    youOwe: <?php echo json_encode(__('messages.you_owe_client'), 15, 512) ?>,
    clientOwes: <?php echo json_encode(__('messages.client_owes_you'), 15, 512) ?>,
    favor: <?php echo json_encode(__('messages.favor'), 15, 512) ?>,
    debit: <?php echo json_encode(__('messages.debit'), 15, 512) ?>,
    paidOf: <?php echo json_encode(' ' . __('messages.of', [], 'es') !== 'of' ? ' de ' : ' of ') ?>,
    selectClient: <?php echo json_encode(__('messages.select_client_alert'), 15, 512) ?>,
    notEnoughStock: <?php echo json_encode(__('messages.not_enough_stock_alert'), 15, 512) ?>,
    out: 'OUT'
};

function setupSearch(inputId, dropdownId, items, renderItem, onSelect) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    let selectedIdx = -1;
    let currentMatches = [];

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase();
        currentMatches = items.filter(it => renderItem(it, 'search').toLowerCase().includes(q)).slice(0, 8);
        selectedIdx = -1;
        if (currentMatches.length && q) {
            dropdown.innerHTML = currentMatches.map((m, i) =>
                `<div class="search-item" data-idx="${i}">${renderItem(m, 'html')}</div>`
            ).join('');
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    input.addEventListener('keydown', (e) => {
        const dropdownItems = dropdown.querySelectorAll('.search-item');
        if (!dropdownItems.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); selectedIdx = Math.min(selectedIdx + 1, dropdownItems.length - 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); selectedIdx = Math.max(selectedIdx - 1, 0); }
        else if (e.key === 'Enter') { e.preventDefault(); dropdownItems[selectedIdx]?.click(); return; }
        else if (e.key === 'Escape') { dropdown.style.display = 'none'; input.blur(); return; }
        dropdownItems.forEach((el, i) => el.classList.toggle('active', i === selectedIdx));
    });

    dropdown.addEventListener('click', (e) => {
        const item = e.target.closest('.search-item');
        if (!item) return;
        const idx = parseInt(item.dataset.idx);
        onSelect(currentMatches[idx], input);
        dropdown.style.display = 'none';
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });
}

// Client search
setupSearch('client-search', 'client-dropdown', clients,
    (c, mode) => mode === 'search' ? [c.name, c.phone, c.doc].filter(Boolean).join(' ') : c.name + ' — ' + c.phone,
    (c, input) => {
        input.value = c.name + ' — ' + c.phone;
        document.getElementById('client-id').value = c.id;
    }
);

// Product search
function addItemRow(product) {
    itemIndex++;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${product.name}
            <input type="hidden" name="items[${itemIndex}][product_id]" value="${product.id}">
        </td>
        <td><input type="number" step="0.0001" name="items[${itemIndex}][weight]" placeholder="${product.stock > 0 ? 'max ' + product.stock.toFixed(4) : '0'}" style="width:100%;padding:0.5rem;border:1px solid var(--color-border);border-radius:6px;font-size:0.85rem;"></td>
        <td style="font-size:0.8rem;${product.stock > 0 ? 'color:var(--color-text-muted);' : 'color:#DC2626;font-weight:600;'}">${product.stock > 0 ? product.stock.toFixed(2)+'g' : 'OUT'}</td>
        <td><button type="button" class="btn btn-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem;color:#DC2626;" onclick="this.closest('tr').remove();toggleTable();">&times;</button></td>
    `;
    document.getElementById('items-body').appendChild(row);
    toggleTable();
}

setupSearch('product-search', 'product-dropdown', products,
    (p, mode) => mode === 'search' ? p.name : (
        p.stock > 0
            ? `${p.name} <span style="color:var(--color-text-muted);font-size:0.8rem;">— ${p.stock.toFixed(2)}g in stock</span>`
            : `${p.name} <span style="color:#DC2626;font-size:0.75rem;font-weight:600;">OUT OF STOCK</span>`
    ),
    (p, input) => {
        addItemRow(p);
        input.value = '';
        input.focus();
    }
);

function toggleTable() {
    const hasItems = document.getElementById('items-body').children.length > 0;
    document.getElementById('items-table').style.display = hasItems ? '' : 'none';
    document.getElementById('items-empty').style.display = hasItems ? 'none' : '';
}

// Validate stock on submit
// Currency formatting
function formatCurrency(input, hidden) {
    let raw = input.value.replace(/[^0-9.]/g, '');
    let parts = raw.split('.');
    if (parts[0]) parts[0] = parseInt(parts[0], 10).toLocaleString('en-US');
    input.value = parts.join('.');
    hidden.value = raw || '0';
}

// Form submit validation
document.getElementById('tx-form').addEventListener('submit', function(e) {
    const direction = document.getElementById('direction').value;
        if (!document.getElementById('client-id').value) {
            e.preventDefault();
            alert(LANG.selectClient);
            return;
        }
    if (direction === 'selling') {
        const rows = document.querySelectorAll('#items-body tr');
        for (const row of rows) {
            const productName = row.cells[0].textContent.trim();
            const weightInput = row.querySelector('input[type=number]');
            const weight = parseFloat(weightInput.value) || 0;
            const stockCell = row.cells[2].textContent.trim();
            const stock = stockCell === 'OUT' ? 0 : (parseFloat(stockCell.replace('g','')) || 0);
            if (weight > stock) {
                e.preventDefault();
                alert(LANG.notEnoughStock.replace(':name', productName).replace(':available', stock.toFixed(4)+'g'));
                weightInput.focus();
                return;
            }
        }
    }
});
// Currency formatting
function formatCurrency(input, hidden) {
    let raw = input.value.replace(/[^0-9.]/g, '');
    let parts = raw.split('.');
    if (parts[0]) parts[0] = parseInt(parts[0], 10).toLocaleString('en-US');
    input.value = parts.join('.');
    hidden.value = raw || '0';
}

document.getElementById('total_value_display').addEventListener('input', function() {
    formatCurrency(this, document.getElementById('total_value'));
    updateSummary();
});
document.getElementById('amount_paid_display').addEventListener('input', function() {
    formatCurrency(this, document.getElementById('amount_paid'));
    updateSummary();
});
document.getElementById('direction').addEventListener('change', updateSummary);

function updateSummary() {
    const total = parseFloat(document.getElementById('total_value').value) || 0;
    const paid = parseFloat(document.getElementById('amount_paid').value) || 0;
    const dir = document.getElementById('direction').value;
    const remaining = Math.max(0, total - paid);
    const box = document.getElementById('balance-summary');
    const text = document.getElementById('summary-text');

    if (!dir || total <= 0) { box.style.display = 'none'; return; }

    box.style.display = 'block';
    if (remaining === 0) {
        text.innerHTML = '<span style="color:#059669;">' + LANG.settled + '</span> — ' + '<?php echo e(__('messages.settled_summary')); ?>';
    } else if (dir === 'buying') {
        text.innerHTML = '<span style="color:var(--color-primary);">' + LANG.youOwe + '</span> $' + remaining.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' (' + LANG.favor + '). $' + paid.toLocaleString('en-US', {minimumFractionDigits: 2}) + LANG.paidOf + '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2}) + '.';
    } else {
        text.innerHTML = '<span style="color:#DC2626;">' + LANG.clientOwes + '</span> $' + remaining.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' (' + LANG.debit + '). $' + paid.toLocaleString('en-US', {minimumFractionDigits: 2}) + LANG.paidOf + '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2}) + '.';
    }
}

function settleFull() {
    const totalRaw = document.getElementById('total_value').value;
    if (!totalRaw || parseFloat(totalRaw) <= 0) return;
    document.getElementById('amount_paid_display').value = parseInt(totalRaw, 10).toLocaleString('en-US');
    document.getElementById('amount_paid').value = totalRaw;
    updateSummary();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/transactions/quick.blade.php ENDPATH**/ ?>