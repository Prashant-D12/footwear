function validateProductForm(form) {
    const name = form.name.value.trim();
    const description = form.description.value.trim();
    const price = parseFloat(form.price.value);
    const stock = parseInt(form.stock.value);
    const image = form.image.value.trim();
    if (!name || !description || !price || !stock || !image) {
        alert('All fields are required.');
        return false;
    }
    if (price <= 0) {
        alert('Price must be greater than 0.');
        return false;
    }
    if (stock < 0) {
        alert('Stock cannot be negative.');
        return false;
    }
    if (!image.startsWith('assets/images/')) {
        alert('Image path must start with assets/images/.');
        return false;
    }
    return true;
}