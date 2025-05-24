# ClickToBuy - E-Commerce Platform

ClickToBuy is a fully-featured MVC e-commerce application built with Laravel, providing a comprehensive online shopping experience for customers and powerful management tools for administrators.

## Requirements

- PHP >= 8.1
- MySQL
- Composer

## Installation Steps

1. Clone the repository
   ```
   git clone https://github.com/mennanoseer/ClickToBuy.git
   ```

2. Navigate to the project directory
   ```
   cd clicktobuy
   ```

3. Install dependencies
   ```
   composer install
   ```

4. Create a copy of .env file
   ```
   cp .env.example .env
   ```

5. Generate application key
   ```
   php artisan key:generate
   ```

6. Configure your database in `.env` file
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=clicktobuy
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run migrations with seed data
   ```
   php artisan migrate --seed
   ```

8. Create a symbolic link for storage
   ```
   php artisan storage:link
   ```

9. Start the development server
   ```
   php artisan serve
   ```

10. Access the website at `http://localhost:8000`

## Default Users

1. Admin User:
   - Email: admin@example.com
   - Password: password

2. Customer User:
   - Email: customer@example.com
   - Password: password

## External Data Integration

ClickToBuy supports importing product data from external sources:

### Available API Sources

1. **Fake Store API** - Limited to 20 products
   - Simple product data with categories
   - Access using the admin panel or CLI command

2. **DummyJSON API** - Up to 100 products
   - Detailed product data with multiple images
   - More product attributes and data variety

### Importing Products

#### Via Admin Panel

1. Log in as an admin user
2. Go to the Products section
3. Click the "Import Products" button
4. Select data source and number of products
5. Click "Import"

#### Via CLI (Command Line Interface)

Import products from external API:
```bash
php artisan products:import [count] --source=[dummyjson|fakestoreapi]
```
Example:
```bash
php artisan products:import 50 --source=dummyjson
```

Generate synthetic test products:
```bash
php artisan products:generate [count]
```
Example:
```bash
php artisan products:generate 1000
```

## Features

### Customer Features

- **User Authentication**: Secure login, registration, and password recovery
- **Product Browsing**: Browse products by category with filtering and search
- **Shopping Cart**: Add/remove items, update quantities
- **Wishlist**: Save products for future reference
- **Checkout Process**: Multiple payment options with secure processing
- **Order Tracking**: View order history and track shipment status
- **User Profile**: Manage personal information and view order history
- **Reviews and Ratings**: Leave feedback on purchased products

### Admin Features

- **Dashboard**: Overview of sales, recent orders, and low stock alerts
- **Product Management**: Add, edit, delete products and manage inventory
- **Order Management**: Process orders, update status, generate invoices
- **Customer Management**: View and manage customer accounts and details
- **Category Management**: Organize products with hierarchical categories
- **Review Moderation**: Approve, edit, or remove customer reviews
- **External Data Integration**: Import products from external APIs
- **Statistical Reports**: Sales reports, popular products, and customer analytics

## Technical Features

- **MVC Architecture**: Clean separation of concerns for maintainability
- **Responsive Design**: Works across desktop and mobile devices
- **Data Validation**: Robust server-side and client-side validation
- **Security**: CSRF protection, input sanitization, and secure authentication
- **Multiple Payment Methods**: Credit Card, PayPal, and Bank Transfer
- **Image Handling**: Support for both local and external image URLs
- **External Data Integration**: API connections to import product data

## Database Structure

ClickToBuy uses a relational database with the following key tables:

- **users**: User authentication data
- **customers**: Customer-specific information
- **admins**: Admin-specific information
- **products**: Product details and inventory
- **categories**: Product categories with hierarchy
- **orders**: Customer orders with status tracking
- **payments**: Payment information with polymorphic relations
- **reviews**: Product reviews and ratings

## Product Image Handling

The system supports both local image uploads and external image URLs:

- Local images are stored in: `storage/app/public/products/`
- External images are used directly from their sources
- Image type detection is automatic using `filter_var($url, FILTER_VALIDATE_URL)`
- The product model has a dynamic accessor for the correct image path

## Troubleshooting

### Common Issues

1. **Database Connection Issues**
   - Verify your database credentials in the `.env` file
   - Ensure MySQL service is running

2. **Image Upload Problems**
   - Check storage permissions
   - Verify the symbolic link is created: `php artisan storage:link`

3. **External API Import Failures**
   - Check your internet connection
   - Some APIs may have rate limits or require authentication

4. **Product Description Length**
   - If importing descriptions fails, ensure you've run the migration: 
     `php artisan migrate --path=database/migrations/2025_05_23_000000_modify_product_description_column.php`

### Debug Mode

For development environments, enable debug mode in your `.env` file:
```
APP_DEBUG=true
```

## Technology Stack

ClickToBuy is built using the following technologies:

- **Backend Framework**: Laravel 10.x
- **Database**: MySQL
- **Frontend**: Bootstrap, jQuery, JavaScript
- **Template Engine**: Blade
- **Authentication**: Laravel Built-in Auth
- **CSS Preprocessor**: SASS
- **Package Management**: Composer, NPM
- **Deployment**: Supports standard Laravel deployment methods

## Recent Updates

The latest updates to the ClickToBuy platform include:

- External Data Integration from multiple API sources
- Database schema optimization for product descriptions
- Enhanced image handling for both local and external URLs
- Fixed display issues in product listings
- Removal of non-working sorting functionality
- Better product import experience via UI
- Command-line tools for product data management

## License

ClickToBuy is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
