# Footwear eCommerce Website

## Project Overview

This is a minimalist eCommerce website developed as a project by **Prashant Dahal**, a BCA 5th Semester student at **Bhaktapur Multiple Campus**. The website allows users to browse footwear products, add them to a cart, place orders, and make payments using eSewa (Nepal's online payment gateway) or Cash on Delivery (COD). It includes user and admin functionalities, with a responsive design and secure database operations.

### Features

- **User Features**:
  - Register and log in with username/email and password.
  - Browse products, add to cart, and update/remove items.
  - Place orders with eSewa or COD payment options.
  - View order history in the user profile.
- **Admin Features**:
  - Admin login (Username: `admin`, Password: `123`).
  - Access to an admin dashboard (located in `admin/` directory).
- **Payment Integration**:
  - eSewa test payment gateway (`https://rc-epay.esewa.com.np/api/epay/main/v2/form`, Merchant ID: `EPAYTEST`).
  - Secure transaction handling with signature generation.
- **Security**:
  - Password hashing for user accounts.
  - Prepared statements to prevent SQL injection.
  - Input sanitization to prevent XSS attacks.
- **Responsive Design**: Styled with `frontend.css` for a clean, user-friendly interface.

## Technologies Used

- **Backend**: PHP (MySQLi and PDO for database operations)
- **Frontend**: HTML, CSS (`frontend.css`), JavaScript (`frontend.js`)
- **Database**: MySQL (database: `footwear_ecommerce`)
- **Server**: XAMPP (Apache, MySQL)
- **Payment Gateway**: eSewa (test environment)
- **Environment**: Developed and tested on Windows (XAMPP in `C:\xampp\htdocs\footwear_ecommerce`)

## Prerequisites

- XAMPP (with Apache and MySQL enabled)
- PHP 8.2+ with `pdo_mysql` and `curl` extensions enabled
- MySQL (phpMyAdmin for database management)
- Web browser (e.g., Chrome, Firefox)
- eSewa test credentials (ID: `9806800001`, Password: `Nepal@123`, Token: `123456`)

## Setup Instructions

### 1. Install XAMPP

- Download and install XAMPP from https://www.apachefriends.org/.
- Start Apache and MySQL from the XAMPP Control Panel.

### 2. Configure PHP

- Open `C:\xampp\php\php.ini` and ensure the following extensions are enabled:

  ```ini
  extension=pdo_mysql
  extension=curl
  ```
- Verify `session.save_path = "C:/xampp/tmp"` is set and the directory is writable.
- Restart Apache after changes.

### 3. Set Up the Project

- Clone or copy the project files to `C:\xampp\htdocs\footwear_ecommerce`.
- Ensure the folder name is `footwear_ecommerce` (or update `BASE_URL` in `includes/config.php` if different).

### 4. Set Up the Database

- Open phpMyAdmin (`http://localhost/phpmyadmin`).
- Create a database named `footwear_ecommerce`.
- Import `database.sql` from the project root to create tables and insert initial data:

  ```sql
  SOURCE C:/xampp/htdocs/footwear_ecommerce/database.sql;
  ```
- The database includes:
  - `admin`: Admin credentials (`rochak`/`123`).
  - `user`: User accounts with hashed passwords.
  - `product`: Sample footwear products with images.
  - `order`: Stores order details (`total_amount`, `payment_method`).
  - `payment`: Tracks payment status and eSewa transaction IDs.
  - `cart`: Manages user cart items.
  - `review`: Stores product reviews.

### 5. Add Placeholder Images

- Create a directory `assets/images/` in the project root.
- Add placeholder images (`shoe1.jpg` to `shoe6.jpg`) for products, as referenced in the `product` table.

### 6. Test the Application

- Open a browser and navigate to `http://localhost/footwear_ecommerce/`.
- Register a user via `register.php` or log in with an existing user.
- For admin access, use `http://localhost/footwear_ecommerce/login.php` with Username: `rochak`, Password: `123`, and check “Login as Admin.”
- Test eSewa payment:
  - Add products to the cart, proceed to checkout, and select “eSewa.”
  - You’ll be redirected to the eSewa test payment page.
  - Use test credentials: ID: `9806800001`, Password: `Nepal@123`, Token: `123456`.
  - After payment, verify redirection to `payment_verify.php`.

## File Structure

```
footwear_ecommerce/
├── admin/
│   ├── dashboard.php        # Admin dashboard
│   ├── logout.php           # Admin logout
├── assets/
│   ├── css/
│   │   ├── frontend.css     # Styles for frontend
│   ├── js/
│   │   ├── frontend.js      # Client-side validation
│   ├── images/
│   │   ├── shoe1.jpg        # Product images
│   │   ├── ...
├── includes/
│   ├── config.php           # Base configuration (BASE_URL, MySQLi connection)
│   ├── db_connect.php       # PDO connection for checkout and payment
│   ├── esewa_config.php     # eSewa configuration (endpoint, merchant ID, secret key)
│   ├── functions.php        # Utility functions (isLoggedIn, generateSignature)
│   ├── header.php           # Common header with navigation
│   ├── footer.php           # Common footer
├── cart.php                 # Manage cart (add, update, remove items)
├── checkout.php             # Order creation and payment method selection
├── database.sql             # Database schema and initial data
├── index.php                # Homepage with product listing
├── login.php                # User and admin login
├── logout.php               # User logout
├── payment.php              # eSewa payment form
├── payment_verify.php       # Handles eSewa success/failure callbacks
├── profile.php              # User profile and order history
├── register.php             # User registration
```

## Database Schema

- **admin**: `id`, `username`, `password`
- **user**: `id`, `username`, `email`, `password`, `created_at`
- **product**: `id`, `name`, `description`, `price`, `image`, `stock`
- **order**: `id`, `user_id`, `total_amount`, `payment_method` (esewa/cod), `status` (pending/completed/cancelled), `created_at`
- **payment**: `id`, `order_id`, `transaction_uuid`, `amount`, `status` (pending/completed/failed), `transaction_id`, `created_at`
- **cart**: `id`, `user_id`, `product_id`, `quantity`
- **review**: `id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`

## eSewa Integration

- **Endpoint**: `https://rc-epay.esewa.com.np/api/epay/main/v2/form` (test environment)
- **Merchant ID**: `EPAYTEST`
- **Secret Key**: `8gBm/:&EnhH.1/q`
- **Process**:
  - `checkout.php` creates an order and redirects to `payment.php` for eSewa.
  - `payment.php` generates a signed form and auto-submits to eSewa.
  - `payment_verify.php` handles success (`q=su`) or failure (`q=fu`) responses.

## Troubleshooting

- **Foreign Key Error in cart.php**:
  - Ensure users exist in the `user` table (`SELECT * FROM user;` in phpMyAdmin).
  - Verify `$_SESSION['user_id']` is set correctly after login.
- **Undefined Key "total" in profile.php**:
  - Ensure the `order` table uses `total_amount` (check with `DESCRIBE `order`;`).
- **eSewa Redirect Issues**:
  - Check browser console (F12 &gt; Console) for “Submitting eSewa form...” or errors.
  - Verify `payment.php` form parameters in Network tab.
  - Check PHP error log (`C:\xampp\php\logs\php_error_log`) for session or PDO errors.
  - Ensure `BASE_URL` matches your folder name (`http://localhost/footwear_ecommerce/`).
- **Database Errors**:
  - Re-run `database.sql` to ensure correct schema.
  - Check PDO and MySQLi connections in `config.php` and `db_connect.php`.

## Developer

- **Name**: Prashant Dahal
- **Program**: BCA, 5th Semester
- **Institution**: Bhaktapur Multiple Campus
- **Project Submission**: July 2025
- 
## screenshot

Register
  <img width="658" height="468" alt="Image" src="https://github.com/user-attachments/assets/6db2fb40-3468-4095-8c9f-1dfce249cc14" />

## Notes

- Ensure placeholder images (`shoe1.jpg` to `shoe6.jpg`) are in `assets/images/`.
- Test thoroughly in the eSewa test environment before moving to production.
- Contact the developer or instructor for support with setup or errors.