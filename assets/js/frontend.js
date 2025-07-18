function validateLoginForm(form) {
    const identifier = form.identifier.value.trim();
    const password = form.password.value.trim();
    if (!identifier || !password) {
        alert('All fields are required.');
        return false;
    }
    return true;
}

function validateRegisterForm(form) {
    const username = form.username.value.trim();
    const email = form.email.value.trim();
    const password = form.password.value.trim();
    if (!username || !email || !password) {
        alert('All fields are required.');
        return false;
    }
    if (!/^[a-zA-Z0-9]+$/.test(username)) {
        alert('Username must be alphanumeric.');
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Invalid email format.');
        return false;
    }
    if (password.length < 6) {
        alert('Password must be at least 6 characters.');
        return false;
    }
    return true;
}

function validateQuantity(form) {
    const quantity = parseInt(form.quantity.value);
    const max = parseInt(form.quantity.max);
    if (quantity < 1 || quantity > max) {
        alert('Quantity must be between 1 and ' + max);
        return false;
    }
    return true;
}

function validateCheckoutForm(form) {
    const paymentMethod = form.payment_method.value;
    if (!paymentMethod) {
        alert('Please select a payment method.');
        return false;
    }
    return true;
}