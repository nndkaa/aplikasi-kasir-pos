<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Retail POS</title>
    <!-- Memanggil CSS dari folder public Laravel -->
    <link rel="stylesheet" href="{{ asset('css/chasier.css') }}">
</head>
<body>

<div class="pos-container">
    <!-- HEADER -->
    <header class="header">
        <div>
            <div class="store-name">TOKO RETAIL MAKMUR</div>
            <div class="store-info">Jl. Contoh No. 123, Jember Telp. 0812-xxxx-xxxx</div>
        </div>
        <div class="transaction-info">
            <div>No. Transaksi</div>
            <div class="transaction-number" id="transactionNumber">TRX-20260911-001</div>
            <div id="currentDate"></div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="main">
        <!-- LEFT SECTION -->
        <section>
            <!-- SEARCH & CUSTOMER -->
            <div class="card">
                <div class="card-header">Tambah Barang</div>
                <div class="card-body">
                    <div class="search-area">
                        <div class="search-input">
                            <input type="text" id="searchProduct" placeholder="Cari nama barang / kode / barcode (F2)..." autocomplete="off">
                            <div class="product-results" id="productResults"></div>
                        </div>
                        <button class="btn btn-primary" onclick="searchProduct()">Cari</button>
                    </div>
                    
                    <div class="customer-area">
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <select class="form-control">
                                <option>Umum</option>
                                <option>Pelanggan Member</option>
                                <option>Member VIP</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>No. Member / HP</label>
                            <input type="text" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CART -->
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Keranjang Belanja</span>
                        <span id="itemCount">0 Item</span>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Barang</th>
                                <th width="100">Harga</th>
                                <th width="120" class="text-center">Qty</th>
                                <th width="120" class="text-right">Subtotal</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="6" class="empty-cart">
                                    Keranjang masih kosong.<br>Silakan cari atau scan barang.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- RIGHT ASIDE -->
        <aside>
            <div class="card">
                <div class="card-header">Ringkasan Pembayaran</div>
                <div class="card-body">
                    <div class="payment-summary">
                        <div class="summary-row">
                            <span>Total Item</span>
                            <strong id="totalQty">0</strong>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong id="subtotal">Rp 0</strong>
                        </div>
                        <div class="summary-row">
                            <span>Diskon (%)</span>
                            <input type="number" id="discountPercent" value="0" min="0" max="100" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row">
                            <span>Diskon (Rp)</span>
                            <input type="number" id="discountAmount" value="0" min="0" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row">
                            <span>Pajak / PPN</span>
                            <input type="number" id="tax" value="0" min="0" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row">
                            <span>Biaya Lain</span>
                            <input type="number" id="otherFee" value="0" min="0" onchange="calculateTotal()">
                        </div>

                        <div class="total-box">
                            <div class="total-label">TOTAL AKHIR</div>
                            <div class="total-value" id="grandTotal">Rp 0</div>
                        </div>

                        <div class="payment-box">
                            <div class="payment-label">Uang Dibayar (F4)</div>
                            <input type="number" id="payment" class="payment-input" placeholder="0" oninput="calculateChange()">

                            <div class="payment-label" style="margin-top:15px">Metode Pembayaran</div>
                            <div class="payment-method">
                                <button class="active" onclick="selectPayment(this)">Tunai</button>
                                <button onclick="selectPayment(this)">QRIS</button>
                                <button onclick="selectPayment(this)">Debit</button>
                                <button onclick="selectPayment(this)">Kredit</button>
                                <button onclick="selectPayment(this)">E-Wallet</button>
                                <button onclick="selectPayment(this)">Transfer</button>
                            </div>

                            <div class="change-box" id="changeBox">
                                <div class="change-label">KEMBALIAN</div>
                                <div class="change-value" id="change">Rp 0</div>
                            </div>
                        </div>

                        <div class="action-area">
                            <button class="btn btn-warning" onclick="holdTransaction()">Tahan</button>
                            <button class="btn btn-danger" onclick="cancelTransaction()">Batal (ESC)</button>
                            <button class="btn btn-success btn-pay" onclick="processPayment()">BAYAR & CETAK</button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>
</div>

<!-- SCRIPT JAVASCRIPT KASIR -->
<script>
    const products = [
        { id: 1, code: "BRG001", barcode: "899123456001", name: "Indomie Goreng", price: 3500 },
        { id: 2, code: "BRG002", barcode: "899123456002", name: "Aqua 600ml", price: 4000 },
        { id: 3, code: "BRG003", barcode: "899123456003", name: "Teh Botol Sosro", price: 5000 },
        { id: 4, code: "BRG004", barcode: "899123456004", name: "Beras 5 Kg", price: 75000 },
        { id: 5, code: "BRG005", barcode: "899123456005", name: "Minyak Goreng 1 Liter", price: 18000 },
        { id: 6, code: "BRG006", barcode: "899123456006", name: "Gula Pasir 1 Kg", price: 17000 }
    ];

    let cart = [];

    const searchInput = document.getElementById("searchProduct");
    searchInput.addEventListener("input", searchProduct);

    function searchProduct() {
        const keyword = searchInput.value.toLowerCase().trim();
        const resultBox = document.getElementById("productResults");
        
        if (!keyword) {
            resultBox.style.display = "none";
            return;
        }

        const results = products.filter(product =>
            product.name.toLowerCase().includes(keyword) ||
            product.code.toLowerCase().includes(keyword) ||
            product.barcode.includes(keyword)
        );

        if (results.length === 0) {
            resultBox.innerHTML = `<div class="product-item" style="padding:10px;">Barang tidak ditemukan</div>`;
        } else {
            resultBox.innerHTML = results.map(product => `
                <div class="product-item" onclick="addToCart(${product.id})" style="padding:10px; cursor:pointer; border-bottom:1px solid #f1f1f1;">
                    <div style="font-size:11px; color:#6c757d">${product.barcode} / ${product.code}</div>
                    <div class="product-name" style="font-weight:bold;">${product.name}</div>
                    <div class="product-price" style="color:#28a745; font-size:12px;">${formatRupiah(product.price)}</div>
                </div>
            `).join("");
        }
        resultBox.style.display = "block";
    }

    function formatRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }).format(value);
    }

    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        const existing = cart.find(item => item.id === productId);

        if (existing) {
            existing.qty++;
        } else {
            cart.push({ ...product, qty: 1 });
        }

        searchInput.value = "";
        document.getElementById("productResults").style.display = "none";
        renderCart();
    }

    function renderCart() {
        const body = document.getElementById("cartBody");
        if (cart.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-cart">
                        Keranjang masih kosong.<br>Silakan cari atau scan barang.
                    </td>
                </tr>
            `;
            calculateTotal();
            return;
        }

        body.innerHTML = cart.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <strong>${item.name}</strong>
                    <div style="font-size:11px; color:#6c757d">${item.code}</div>
                </td>
                <td>${formatRupiah(item.price)}</td>
                <td>
                    <div class="qty-control">
                        <button onclick="changeQty(${item.id}, -1)">-</button>
                        <input type="number" value="${item.qty}" min="1" onchange="updateQty(${item.id}, this.value)">
                        <button onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td class="text-right">
                    <strong>${formatRupiah(item.price * item.qty)}</strong>
                </td>
                <td class="text-center">
                    <button class="remove-btn" onclick="removeItem(${item.id})">x</button>
                </td>
            </tr>
        `).join("");

        calculateTotal();
    }

    function changeQty(id, amount) {
        const item = cart.find(item => item.id === id);
        if (!item) return;
        item.qty += amount;
        if (item.qty <= 0) {
            removeItem(id);
            return;
        }
        renderCart();
    }

    function updateQty(id, qty) {
        const item = cart.find(item => item.id === id);
        if (!item) return;
        item.qty = Math.max(1, parseInt(qty) || 1);
        renderCart();
    }

    function removeItem(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function getGrandTotal() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.qty;
        });

        const discountPercent = parseFloat(document.getElementById("discountPercent").value) || 0;
        const discountAmount = parseFloat(document.getElementById("discountAmount").value) || 0;
        const tax = parseFloat(document.getElementById("tax").value) || 0;
        const otherFee = parseFloat(document.getElementById("otherFee").value) || 0;

        const percentDiscount = (subtotal * discountPercent) / 100;
        const totalDiscount = percentDiscount + discountAmount;

        return Math.max(0, subtotal - totalDiscount + tax + otherFee);
    }

    function calculateTotal() {
        let totalQty = 0;
        let subtotal = 0;
        cart.forEach(item => {
            totalQty += item.qty;
            subtotal += item.price * item.qty;
        });

        document.getElementById("totalQty").innerText = totalQty;
        document.getElementById("itemCount").innerText = totalQty + " Item";
        document.getElementById("subtotal").innerText = formatRupiah(subtotal);
        document.getElementById("grandTotal").innerText = formatRupiah(getGrandTotal());
        calculateChange();
    }

    function calculateChange() {
        const grandTotal = getGrandTotal();
        const payment = parseFloat(document.getElementById("payment").value) || 0;
        const change = payment - grandTotal;
        const changeBox = document.getElementById("changeBox");
        const changeElement = document.getElementById("change");

        if (change >= 0) {
            changeBox.classList.remove("short-payment");
            changeElement.innerText = formatRupiah(change);
            changeBox.querySelector(".change-label").innerText = "KEMBALIAN";
        } else {
            changeBox.classList.add("short-payment");
            changeElement.innerText = formatRupiah(Math.abs(change));
            changeBox.querySelector(".change-label").innerText = "UANG KURANG";
        }
    }

    function selectPayment(button) {
        document.querySelectorAll(".payment-method button").forEach(btn => {
            btn.classList.remove("active");
        });
        button.classList.add("active");
    }

    function holdTransaction() {
        if (cart.length === 0) {
            alert("Tidak ada transaksi untuk ditahan.");
            return;
        }
        alert("Transaksi berhasil ditahan.");
    }

    function processPayment() {
        if (cart.length === 0) {
            alert("Keranjang masih kosong.");
            return;
        }
        const total = getGrandTotal();
        const payment = parseFloat(document.getElementById("payment").value) || 0;
        
        if (payment < total) {
            alert("Uang pembayaran masih kurang.");
            return;
        }

        const change = payment - total;
        alert(
            "Pembayaran berhasil!\n\n" +
            "Total : " + formatRupiah(total) + "\n" +
            "Bayar : " + formatRupiah(payment) + "\n" +
            "Kembali: " + formatRupiah(change)
        );
        
        cancelTransaction();
    }

    function cancelTransaction() {
        if (cart.length > 0 && !confirm("Batalkan transaksi ini?")) {
            return;
        }
        cart = [];
        document.getElementById("payment").value = "";
        document.getElementById("discountPercent").value = 0;
        document.getElementById("discountAmount").value = 0;
        document.getElementById("tax").value = 0;
        document.getElementById("otherFee").value = 0;
        renderCart();
    }

    document.getElementById("currentDate").innerText = new Date().toLocaleString("id-ID");

    document.addEventListener("keydown", function (event) {
        if (event.key === "F2") {
            event.preventDefault();
            searchInput.focus();
        }
        if (event.key === "F4") {
            event.preventDefault();
            document.getElementById("payment").focus();
        }
        if (event.key === "Escape") {
            cancelTransaction();
        }
    });

    renderCart();
</script>

</body>
</html>