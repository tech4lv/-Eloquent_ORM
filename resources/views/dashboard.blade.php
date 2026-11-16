<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f1f5f9;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #6366f1;
            --accent-2: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 14px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ---------- Header ---------- */
        header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #fff;
            padding: 1.5rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(79, 70, 229, .25);
        }
        header h1 { font-size: 1.4rem; font-weight: 700; letter-spacing: -.02em; }
        header p { font-size: .85rem; opacity: .8; margin-top: .2rem; }
        .badge {
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            padding: .4rem .9rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 500;
        }

        /* ---------- Layout ---------- */
        main { max-width: 1200px; margin: 0 auto; padding: 2rem 2.5rem; }

        .section-title {
            font-size: .95rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 1rem;
        }

        /* ---------- Stat Cards ---------- */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.4rem;
            border: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(15,23,42,.08); }
        .card .icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .icon.indigo { background: #eef2ff; }
        .icon.green  { background: #ecfdf5; }
        .icon.amber  { background: #fffbeb; }
        .icon.rose   { background: #fff1f2; }
        .card .label { font-size: .8rem; color: var(--muted); font-weight: 500; }
        .card .value { font-size: 1.6rem; font-weight: 700; margin-top: .3rem; letter-spacing: -.02em; }
        .card .hint  { font-size: .75rem; color: var(--muted); margin-top: .2rem; }

        /* ---------- Table ---------- */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(15,23,42,.05);
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left;
            padding: .9rem 1.25rem;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        tbody td {
            padding: .85rem 1.25rem;
            font-size: .9rem;
            border-bottom: 1px solid var(--border);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .12s ease; }
        tbody tr:hover { background: #f8fafc; }
        .price { font-weight: 600; color: #059669; }
        .id-chip {
            display: inline-block;
            background: #eef2ff;
            color: #4f46e5;
            font-size: .78rem;
            font-weight: 600;
            padding: .2rem .6rem;
            border-radius: 8px;
        }
        .date { color: var(--muted); font-size: .85rem; }

        /* ---------- States ---------- */
        #error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: none;
            font-size: .9rem;
        }
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 10px;
            height: 88px;
        }
        @keyframes shimmer { to { background-position: -200% 0; } }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>Product Dashboard</h1>
            <p>Inventory overview &middot; Live data from the Product API</p>
        </div>
        <span class="badge">API v1</span>
    </header>

    <main>
        <p id="error">Unable to reach <code>/api/v1/products/stats</code>. Make sure <code>php artisan serve</code> is running.</p>

        <p class="section-title">Overview</p>
        <div class="cards" id="cards" style="display:none;">
            <div class="card">
                <div class="icon indigo">&#128230;</div>
                <div>
                    <div class="label">Total Products</div>
                    <div class="value" id="total-products">-</div>
                    <div class="hint">Items in catalog</div>
                </div>
            </div>
            <div class="card">
                <div class="icon green">&#128722;</div>
                <div>
                    <div class="label">Units in Stock</div>
                    <div class="value" id="units-in-stock">-</div>
                    <div class="hint">Available units</div>
                </div>
            </div>
            <div class="card">
                <div class="icon amber">&#128176;</div>
                <div>
                    <div class="label">Inventory Value</div>
                    <div class="value" id="inventory-value">-</div>
                    <div class="hint">Total catalog value</div>
                </div>
            </div>
            <div class="card">
                <div class="icon rose">&#9888;</div>
                <div>
                    <div class="label">Low Stock</div>
                    <div class="value" id="low-stock">-</div>
                    <div class="hint">Never ordered</div>
                </div>
            </div>
        </div>
        <div class="cards" id="skeletons">
            <div class="skeleton"></div>
            <div class="skeleton"></div>
            <div class="skeleton"></div>
            <div class="skeleton"></div>
        </div>

        <p class="section-title">Recent Products</p>
        <div class="table-wrap" id="table-wrap" style="display:none;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody id="recent-body"></tbody>
            </table>
        </div>
    </main>

    <script>
        fetch('/api/v1/products/stats')
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(json => {
                const s = json.data;
                const fmt = n => Number(n).toLocaleString();
                const money = n => '$' + Number(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

                document.getElementById('total-products').textContent = fmt(s.total_products);
                document.getElementById('units-in-stock').textContent = fmt(s.total_units_in_stock);
                document.getElementById('inventory-value').textContent = money(s.inventory_value);
                document.getElementById('low-stock').textContent = fmt(s.low_stock_count);

                document.getElementById('skeletons').style.display = 'none';
                document.getElementById('cards').style.display = '';

                const tbody = document.getElementById('recent-body');
                s.recent_products.forEach(p => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><span class="id-chip">#${p.id}</span></td>
                        <td>${p.name}</td>
                        <td class="price">${money(p.price)}</td>
                        <td class="date">${new Date(p.created_at).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'})}</td>`;
                    tbody.appendChild(tr);
                });
                document.getElementById('table-wrap').style.display = '';
            })
            .catch(() => {
                document.getElementById('skeletons').style.display = 'none';
                document.getElementById('error').style.display = '';
            });
    </script>
</body>
</html>
