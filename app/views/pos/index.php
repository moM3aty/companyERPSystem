<?php
// app/views/pos/index.php
$products = $data['products'] ?? [];
$categories = $data['categories'] ?? [];
$customers = $data['customers'] ?? [];

// تحويل المنتجات إلى JSON لتسهيل التعامل معها عبر الجافاسكريبت
$productsJson = json_encode($products);
?>

<style>
    .pos-wrapper {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
        height: calc(100vh - 140px);
    }
    
    .pos-products-area {
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow: hidden;
    }

    /* Filters */
    .pos-filters {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .pos-filter-btn {
        padding: 8px 16px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .pos-filter-btn.active, .pos-filter-btn:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    /* Search */
    .pos-search {
        position: relative;
    }
    .pos-search input {
        width: 100%;
        padding: 12px 20px 12px 40px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        font-size: 15px;
        outline: none;
    }
    .pos-search i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    /* Products Grid */
    .pos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
        overflow-y: auto;
        padding-right: 5px;
        align-content: start;
    }
    .pos-item {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
    }
    .pos-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }
    .pos-item-title {
        font-weight: 800;
        color: var(--text-dark);
        font-size: 14px;
        margin-bottom: 8px;
    }
    .pos-item-price {
        font-family: 'Fira Code', monospace;
        color: var(--success);
        font-weight: 900;
        font-size: 16px;
    }
    .pos-item-stock {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 5px;
    }

    /* Cart Area */
    .pos-cart-area {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .pos-cart-header {
        padding: 16px 20px;
        background: var(--slate-50);
        border-bottom: 1px solid var(--border-color);
        font-weight: 800;
        font-size: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .pos-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }
    
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px dashed var(--border-color);
    }
    .cart-item-info { flex: 1; }
    .cart-item-title { font-weight: 700; font-size: 14px; color: var(--text-dark); }
    .cart-item-price { font-family: monospace; font-size: 13px; color: var(--text-muted); }
    
    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--slate-50);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-dark);
    }
    .qty-btn:hover { background: var(--slate-200); }
    .qty-display {
        font-family: monospace;
        font-weight: 800;
        width: 30px;
        text-align: center;
    }

    .pos-cart-footer {
        padding: 20px;
        background: var(--slate-50);
        border-top: 1px solid var(--border-color);
    }
    .pos-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 20px;
        font-weight: 900;
        color: var(--text-dark);
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .pos-wrapper { grid-template-columns: 1fr; height: auto; }
        .pos-grid { max-height: 400px; }
    }
</style>

<div class="pos-wrapper d-print-none">
    
    <!-- الجانب الأيمن: المنتجات -->
    <div class="pos-products-area">
        
        <div class="pos-search">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="ابحث باسم المنتج أو الباركود (SKU)..." onkeyup="filterProducts()">
        </div>

        <div class="pos-filters">
            <button class="pos-filter-btn active" onclick="filterCategory('all', this)">الكل</button>
            <?php foreach($categories as $cat): ?>
                <button class="pos-filter-btn" onclick="filterCategory('<?php echo htmlspecialchars($cat); ?>', this)"><?php echo htmlspecialchars($cat); ?></button>
            <?php endforeach; ?>
        </div>

        <div class="pos-grid" id="productsGrid">
            <!-- سيتم حقن المنتجات هنا بواسطة JavaScript -->
        </div>
    </div>

    <!-- الجانب الأيسر: السلة والعميل -->
    <div class="pos-cart-area">
        <div class="pos-cart-header">
            <span><i class="fas fa-shopping-cart text-primary"></i> سلة المشتريات</span>
            <span class="badge badge-primary" id="cartCount">0</span>
        </div>
        
        <div class="p-3 border-bottom">
            <label class="form-label" style="font-size:12px;">اختر العميل (اختياري)</label>
            <select id="customerSelect" class="form-control form-control-sm">
                <option value="">عميل نقدي (Walk-in)</option>
                <?php foreach($customers as $c): ?>
                    <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pos-cart-items" id="cartItems">
            <!-- سيتم حقن عناصر السلة هنا -->
            <div class="text-center text-muted" style="padding: 40px 0; opacity: 0.5;" id="emptyCartMsg">
                <i class="fas fa-basket-shopping fa-3x mb-3"></i>
                <p>السلة فارغة. ابدأ بإضافة المنتجات.</p>
            </div>
        </div>

        <div class="pos-cart-footer">
            <div class="pos-total-row">
                <span>الإجمالي المطلوب:</span>
                <span class="text-success font-monospace" style="direction:ltr;"><span id="cartTotal">0.00</span> ر.س</span>
            </div>
            
            <form action="<?php echo URLROOT; ?>/pos/checkout" method="POST" id="checkoutForm">
                <input type="hidden" name="customer_id" id="formCustomerId" value="">
                <div id="formHiddenInputs"></div> <!-- حقول المنتجات المخفية -->
                
                <button type="button" class="btn btn-success w-100" style="padding: 15px; font-size: 16px;" onclick="submitCheckout()">
                    <i class="fas fa-check-circle"></i> إتمام البيع (الدفع)
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const productsData = <?php echo $productsJson; ?>;
    let cart = {}; 
    let currentCategory = 'all';

    // 1. عرض المنتجات
    function renderProducts(query = '') {
        const grid = document.getElementById('productsGrid');
        grid.innerHTML = '';
        
        let filtered = productsData.filter(p => {
            const matchName = p.name.toLowerCase().includes(query.toLowerCase()) || (p.sku && p.sku.toLowerCase().includes(query.toLowerCase()));
            const matchCat = currentCategory === 'all' || p.category_name === currentCategory;
            return matchName && matchCat;
        });

        if(filtered.length === 0) {
            grid.innerHTML = '<div class="text-muted w-100 text-center py-4">لا توجد منتجات مطابقة.</div>';
            return;
        }

        filtered.forEach(p => {
            const priceFormatted = parseFloat(p.price).toFixed(2);
            grid.innerHTML += `
                <div class="pos-item" onclick="addToCart(${p.id})">
                    <div class="pos-item-title">${p.name}</div>
                    <div>
                        <div class="pos-item-price">${priceFormatted}</div>
                        <div class="pos-item-stock"><i class="fas fa-box"></i> متوفر: ${p.quantity}</div>
                    </div>
                </div>
            `;
        });
    }

    // 2. الفلترة
    function filterProducts() {
        const query = document.getElementById('searchInput').value;
        renderProducts(query);
    }

    function filterCategory(cat, btn) {
        currentCategory = cat;
        document.querySelectorAll('.pos-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterProducts();
    }

    // 3. إدارة السلة
    function addToCart(productId) {
        const product = productsData.find(p => p.id === productId);
        if(!product) return;

        if(cart[productId]) {
            if(cart[productId].qty < product.quantity) {
                cart[productId].qty++;
            } else {
                alert('عفواً، لا يوجد مخزون كافٍ لهذا المنتج.');
            }
        } else {
            cart[productId] = { ...product, qty: 1 };
        }
        updateCartUI();
    }

    function changeQty(productId, delta) {
        if(cart[productId]) {
            const product = productsData.find(p => p.id === productId);
            let newQty = cart[productId].qty + delta;
            
            if(newQty > product.quantity) {
                alert('لقد تجاوزت الكمية المتاحة في المخزن.');
                return;
            }
            
            if(newQty <= 0) {
                delete cart[productId];
            } else {
                cart[productId].qty = newQty;
            }
            updateCartUI();
        }
    }

    function updateCartUI() {
        const cartItemsDiv = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        let total = 0;
        let count = 0;
        
        cartItemsDiv.innerHTML = '';
        const items = Object.values(cart);

        if(items.length === 0) {
            cartItemsDiv.appendChild(emptyMsg);
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
            items.forEach(item => {
                const subtotal = item.qty * item.price;
                total += subtotal;
                count += item.qty;

                cartItemsDiv.innerHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">${parseFloat(item.price).toFixed(2)} ر.س</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="qty-btn" onclick="changeQty(${item.id}, -1)"><i class="fas fa-minus" style="font-size:10px;"></i></button>
                            <span class="qty-display">${item.qty}</span>
                            <button class="qty-btn" onclick="changeQty(${item.id}, 1)"><i class="fas fa-plus" style="font-size:10px;"></i></button>
                            <button class="qty-btn text-danger ms-2" onclick="changeQty(${item.id}, -999)" style="border-color:#fca5a5; background:#fef2f2;"><i class="fas fa-trash" style="font-size:12px;"></i></button>
                        </div>
                    </div>
                `;
            });
        }

        document.getElementById('cartTotal').innerText = total.toFixed(2);
        document.getElementById('cartCount').innerText = count;
    }

    // 4. إتمام الدفع (Submit)
    function submitCheckout() {
        const items = Object.values(cart);
        if(items.length === 0) {
            alert('السلة فارغة!');
            return;
        }

        // إعداد العميل
        document.getElementById('formCustomerId').value = document.getElementById('customerSelect').value;

        // إعداد حقول المنتجات المخفية ليتم إرسالها للـ PHP
        const hiddenInputs = document.getElementById('formHiddenInputs');
        hiddenInputs.innerHTML = '';
        
        items.forEach(item => {
            hiddenInputs.innerHTML += `
                <input type="hidden" name="product_id[]" value="${item.id}">
                <input type="hidden" name="quantity[]" value="${item.qty}">
                <input type="hidden" name="price[]" value="${item.price}">
            `;
        });

        // إرسال النموذج
        document.getElementById('checkoutForm').submit();
    }

    // تهيئة أولية
    window.onload = () => { renderProducts(); };
</script>