# 💰 Personal Finance Tracker

A comprehensive web application for managing personal finances.

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Active%20Development-brightgreen)

---

## 📋 **Table of Contents**

- [Features](#-features)
- [Demo](#-demo)
- [Installation](#-installation)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Architecture](#-architecture)
- [Development](#-development)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ **Features**

### **Core Functionality**
- 🔐 **Secure Authentication** - User registration, login, password reset with email verification
- 💳 **Multi-Account Management** - Track multiple bank accounts, credit cards, and investment accounts
- 📊 **Transaction Tracking** - Add, edit, categorize, and search all financial transactions
- 📈 **Advanced Reporting** - Visual charts, spending analysis, and custom date range reports
- 🎯 **Budget Management** - Create budgets, track spending against goals, receive alerts
- 📱 **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices

### **Advanced Features**
- 👥 **Family Accounts** - Multi-user households with role-based permissions
- 🔄 **Recurring Transactions** - Automated handling of regular income and expenses  
- 📂 **Bulk Import/Export** - CSV import for transactions, PDF/Excel export for reports
- 🔔 **Smart Notifications** - Email alerts for budget overruns, low balances, and unusual spending
- 🔌 **RESTful API** - Full API access for mobile apps and third-party integrations
- 📊 **Data Analytics** - Spending trends, forecasting, and financial insights

---

## 🎬 **Demo**

### **Live Demo**
Visit the live demo: **[https://finance-tracker-demo.herokuapp.com](https://finance-tracker-demo.herokuapp.com)**

**Demo Credentials:**
- Email: `demo@example.com`
- Password: `Demo123!`

### **Screenshots**

| Dashboard | Transaction List | Budget Overview |
|-----------|------------------|-----------------|
| ![Dashboard](docs/screenshots/dashboard.png) | ![Transactions](docs/screenshots/transactions.png) | ![Budgets](docs/screenshots/budgets.png) |

---

## 🚀 **Installation**

### **Prerequisites**
- PHP 8.2+ with extensions: PDO, MySQLi, cURL, GD, OpenSSL
- MySQL 8.0+ or MariaDB 10.4+
- Composer (for dependency management)
- Node.js 16+ and npm (for asset compilation)
- Web server (Apache/Nginx) or PHP built-in server

### **Quick Start**

```bash
# Clone the repository
git clone https://github.com/yourusername/personal-finance-tracker.git
cd personal-finance-tracker

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=finance_tracker
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run database migrations
php artisan migrate --seed

# Compile assets
npm run build

# Start the development server
php artisan serve
```

### **Docker Installation** (Alternative)

```bash
# Clone and navigate to project
git clone https://github.com/yourusername/personal-finance-tracker.git
cd personal-finance-tracker

# Start with Docker Compose
docker-compose up -d

# Run migrations inside container
docker-compose exec app php artisan migrate --seed

# Access at http://localhost:8000
```

---

## 📖 **Usage**

### **Getting Started**

1. **Create Account:** Register with your email and verify your account
2. **Add Bank Accounts:** Set up your checking, savings, and credit card accounts
3. **Add Transactions:** Start logging your income and expenses
4. **Create Categories:** Organize transactions with custom categories
5. **Set Budgets:** Create monthly budgets to track your spending goals
6. **View Reports:** Analyze your financial data with built-in reporting tools

### **Key Workflows**

#### **Adding Transactions**
```
Dashboard → Add Transaction → Select Account → Choose Category → Enter Details → Save
```

#### **Creating Budgets**
```
Budgets → Create New Budget → Select Categories → Set Limits → Choose Time Period → Save
```

#### **Generating Reports**
```
Reports → Choose Report Type → Select Date Range → Apply Filters → Generate Report
```

### **Importing Data**

The system supports CSV import with the following format:

```csv
Date,Description,Amount,Category,Account
2024-01-15,"Grocery Store",-85.50,"Food","Checking"
2024-01-15,"Salary Deposit",3000.00,"Salary","Checking"
```

---

## 🔌 **API Documentation**

### **Authentication**
All API endpoints require authentication using JWT tokens.

```bash
# Get access token
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}

# Use token in subsequent requests
Authorization: Bearer {your_jwt_token}
```

### **Core Endpoints**

```bash
# Accounts
GET    /api/accounts           # List all accounts
POST   /api/accounts           # Create new account
GET    /api/accounts/{id}      # Get specific account
PUT    /api/accounts/{id}      # Update account
DELETE /api/accounts/{id}      # Delete account

# Transactions  
GET    /api/transactions       # List transactions (with filters)
POST   /api/transactions       # Create new transaction
GET    /api/transactions/{id}  # Get specific transaction
PUT    /api/transactions/{id}  # Update transaction
DELETE /api/transactions/{id}  # Delete transaction

# Reports
GET    /api/reports/spending   # Get spending analysis
GET    /api/reports/income     # Get income analysis
GET    /api/reports/budgets    # Get budget performance
```

### **API Rate Limiting**
- **Authenticated users:** 1000 requests per hour
- **Unauthenticated:** 100 requests per hour

Full API documentation available at: `/api/documentation`

---

## 🏗️ **Architecture**

### **Technology Stack**

- **Backend:** PHP 8.2+ with Laravel 10
- **Database:** MySQL 8.0+ with Eloquent ORM
- **Frontend:** Blade Templates, Alpine.js, Tailwind CSS
- **Charts:** Chart.js for data visualization
- **Authentication:** Laravel Sanctum for API tokens
- **File Processing:** League/CSV for imports/exports
- **PDF Generation:** DomPDF for report generation
- **Email:** Laravel Mail with queue processing

### **Project Structure**

```
├── app/
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic services
│   └── Jobs/                  # Queue jobs
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/              # Test data
├── resources/
│   ├── views/                # Blade templates
│   ├── js/                   # JavaScript files
│   └── css/                  # Stylesheets
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
├── tests/                    # PHPUnit tests
└── docker/                   # Docker configuration
```

### **Database Schema**

```sql
users (id, email, password, first_name, last_name, created_at)
accounts (id, user_id, name, type, balance, currency)
categories (id, user_id, name, type, color)
transactions (id, account_id, category_id, amount, description, date)
budgets (id, user_id, category_id, amount, period, start_date)
```

---

## 💻 **Development**

### **Development Setup**

```bash
# Install development dependencies
composer install --dev
npm install

# Set up development environment
cp .env.example .env.local
php artisan key:generate

# Run development servers
php artisan serve          # Backend (http://localhost:8000)
npm run dev               # Frontend asset watcher
```

### **Code Standards**

- **PHP:** PSR-12 coding standard with PHP-CS-Fixer
- **JavaScript:** ESLint with Airbnb configuration
- **Git:** Conventional Commits specification
- **Documentation:** PHPDoc blocks for all methods

### **Pre-commit Hooks**

```bash
# Install pre-commit hooks
composer install --dev
./vendor/bin/grumphp git:init

# Hooks will automatically run:
# - PHP-CS-Fixer (code formatting)
# - PHPStan (static analysis)  
# - PHPUnit (tests)
# - ESLint (JavaScript linting)
```

---

## 🧪 **Testing**

### **Running Tests**

```bash
# Run all tests
php artisan test

# Run specific test types
php artisan test --testsuite=Feature  # Feature tests
php artisan test --testsuite=Unit     # Unit tests

# Run with coverage
php artisan test --coverage
```

### **Test Structure**

- **Unit Tests:** Model methods, service classes, utilities
- **Feature Tests:** HTTP endpoints, authentication, business logic
- **Browser Tests:** End-to-end workflows with Laravel Dusk

### **Test Coverage Goals**
- **Overall Coverage:** >85%
- **Critical Paths:** >95% (authentication, transactions, calculations)
- **Models:** >90%
- **Controllers:** >80%

---

## 🚢 **Deployment**

### **Environment Setup**

```bash
# Production environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_user
MAIL_PASSWORD=your_mail_password

# Queue Configuration (Redis recommended)
QUEUE_CONNECTION=redis
REDIS_HOST=your_redis_host
```

### **Deployment Checklist**

- [ ] Configure production environment variables
- [ ] Set up SSL certificate (Let's Encrypt recommended)
- [ ] Configure web server (Apache/Nginx)
- [ ] Set proper file permissions
- [ ] Configure database with proper indexes
- [ ] Set up Redis for caching and queues
- [ ] Configure scheduled tasks (cron jobs)
- [ ] Set up monitoring and error tracking
- [ ] Configure automated backups
- [ ] Test all functionality in staging environment

### **Deployment Commands**

```bash
# Production deployment
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan queue:restart
```

---

## 🤝 **Contributing**

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch:** `git checkout -b feature/amazing-feature`
3. **Make your changes** following our code standards
4. **Write/update tests** for your changes
5. **Commit your changes:** `git commit -m 'Add amazing feature'`
6. **Push to branch:** `git push origin feature/amazing-feature`
7. **Open a Pull Request**

### **Development Guidelines**

- Follow PSR-12 coding standards
- Write tests for new functionality
- Update documentation as needed
- Use conventional commit messages
- Ensure all tests pass before submitting

### **Reporting Issues**

Please use the [GitHub issue tracker](https://github.com/yourusername/personal-finance-tracker/issues) to report bugs or request features.

---

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 **Acknowledgments**

- **Laravel Community** for the excellent framework and documentation
- **Chart.js** for beautiful, responsive charts
- **Tailwind CSS** for the utility-first CSS framework
- **All Contributors** who have helped improve this project

---

## 📞 **Support**

- **Documentation:** [Wiki](https://github.com/yourusername/personal-finance-tracker/wiki)
- **Issues:** [GitHub Issues](https://github.com/yourusername/personal-finance-tracker/issues)
- **Discussions:** [GitHub Discussions](https://github.com/yourusername/personal-finance-tracker/discussions)
- **Email:** support@yourapp.com

---

**Made with ❤️ by [Your Name](https://github.com/yourusername)**

*If you find this project helpful, please consider giving it a ⭐ on GitHub!*