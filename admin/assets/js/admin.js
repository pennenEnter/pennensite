/**
 * PENNEN Admin — Client-side Interactivity (Vanilla JS)
 */
document.addEventListener('DOMContentLoaded', function () {
  // 1. Auto calculate discount percentage
  const priceInput = document.getElementById('productPrice');
  const mrpInput = document.getElementById('productMrp');
  const discountInput = document.getElementById('productDiscount');

  function calculateDiscount() {
    if (!priceInput || !discountInput) return;
    const price = parseFloat(priceInput.value) || 0;
    const mrp = mrpInput ? parseFloat(mrpInput.value) || 0 : 0;

    if (mrp > price && price > 0) {
      const disc = Math.round(((mrp - price) / mrp) * 100);
      discountInput.value = disc;
    } else if (mrp > 0 && price >= mrp) {
      discountInput.value = 0;
    }
  }

  if (priceInput) priceInput.addEventListener('input', calculateDiscount);
  if (mrpInput) mrpInput.addEventListener('input', calculateDiscount);

  // 2. Image upload preview
  const fileInputs = document.querySelectorAll('.upload-input');
  fileInputs.forEach(function (inp) {
    inp.addEventListener('change', function () {
      const file = inp.files[0];
      const targetId = inp.dataset.preview;
      const previewImg = document.getElementById(targetId);

      if (file && previewImg) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.classList.add('active');
        };
        reader.readAsDataURL(file);
      }
    });
  });

  // 3. Confirm action dialog
  const deleteForms = document.querySelectorAll('.confirm-delete-form');
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      const name = form.dataset.name || 'this product';
      if (!confirm('Are you sure you want to delete "' + name + '"? This will permanently remove it from the public catalogue.')) {
        e.preventDefault();
      }
    });
  });
});
