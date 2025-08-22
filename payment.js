
// Get selected items from localStorage
const selectedItems = JSON.parse(localStorage.getItem('cart')) || [];

const availableCoupons = {
    'FRESH10': 10,  // 10% discount
    'HEALTHY5': 5   // 5% discount
  };
  
  let subtotal = 0;
  let discount = 0;
  
  function loadItems() {
    const itemsList = document.getElementById('selected-items');
    selectedItems.forEach(item => {
      const li = document.createElement('li');
      li.textContent = `${item.name} - ₹${item.price}`;
      itemsList.appendChild(li);
      subtotal += item.price;
    });
  
    document.getElementById('subtotal').textContent = subtotal;
    document.getElementById('total').textContent = subtotal;
  }
  
  function applyCoupon() {
    const couponInput = document.getElementById('coupon').value.trim().toUpperCase();
    const message = document.getElementById('coupon-message');
  
    if (availableCoupons[couponInput]) {
      const discountPercent = availableCoupons[couponInput];
      discount = (subtotal * discountPercent) / 100;
      message.textContent = `Coupon Applied: ${discountPercent}% OFF`;
    } else {
      discount = 0;
      message.textContent = "Invalid Coupon Code";
    }
  
    document.getElementById('discount').textContent = Math.round(discount);
    document.getElementById('total').textContent = Math.round(subtotal - discount);
  }
  
  // Load items on page load
  window.onload = loadItems;
  