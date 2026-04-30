
README - Added Product CRUD for CodeIgniter 3 project
----------------------------------------------------

Files added/modified:
- application/models/Product_model.php
- application/controllers/Product.php
- application/views/product/index.php
- application/views/product/form.php
- application/views/template/sidebar.php (backup created as sidebar.php.bak)
- database_products.sql (SQL to create products table + sample data)

What you must check before running:
1. Database: import the earlier provided 'sistem_sales_order' DB or run `database_products.sql` to create products table.
2. Make sure application/config/database.php points to your database and the 'sistem_sales_order' database is used.
3. Ensure your auth controller sets session userdata:
   - 'logged_in' => TRUE
   - 'username'  => (user)
   - 'role'      => 'admin' (for admin access)
4. The views expect `template/header.php` and `template/footer.php` to exist. They were referenced from the project.
5. If using base_url() or site_url(), configure base_url in application/config/config.php.

How to use:
- Login as admin (role stored in session as 'admin').
- Open: http://yourhost/ci_v3/index.php/product
- Create / Edit / Delete products from the UI.

Notes:
- A backup of the original sidebar was saved as application/views/template/sidebar.php.bak
- If your project uses CSRF or form validation hooks, adapt the form accordingly.
