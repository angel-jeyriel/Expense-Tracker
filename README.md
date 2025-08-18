# Expense Tracker

A web-based application for tracking personal expenses, built with **Laravel 12**, **Filament**, **Livewire**, **Tailwind CSS**, **Alpine.js**, and **Chart.js**.  
Features include adding and editing transactions, managing categories and budgets, handling recurring expenses with automated scheduling, and visualizing spending with charts.

---

## ✨ Features

- **User Authentication**  
  Secure login and registration using Laravel Breeze.

- **Transaction Management**  
  Add, edit, and delete transactions with details like description, amount, category, and date.  
  Transactions can be created via Livewire forms or the Filament admin panel.

- **Recurring Expenses**  
  Support for daily, weekly, or monthly recurring expenses.  
  Recurring entries are stored in a dedicated table and linked to user transactions with the **next occurrence date** calculated automatically.

- **Categories and Budgets**  
  Manage expense categories (global and user-specific).  
  Set budgets and receive notifications when exceeding thresholds.

- **Reports and Analytics**  
  Filter transactions by date and category.  
  Visualize spending patterns with **Chart.js bar and pie charts**.

- **Admin Panel with Filament**  
  Manage transactions, categories, and recurring expenses from a professional, responsive admin dashboard.  
  Filament hooks are extended to create recurring expense records automatically when adding/editing transactions.

- **Responsive UI**  
  Mobile-friendly layout with a collapsible navigation bar using Alpine.js and Tailwind CSS.

---

## 🛠 Tech Stack

- **Backend**: Laravel 12, Livewire  
- **Frontend**: Tailwind CSS, Alpine.js, Chart.js  
- **Admin Panel**: [Filament](https://filamentphp.com) v3  
- **Database**: MySQL or PostgreSQL (configurable via `.env`)  
- **Queues**: Laravel Queue for processing recurring expenses  
- **Build Tool**: Vite  

---

## 📦 Prerequisites

Make sure you have the following installed:

- **PHP** >= 8.2  
- **Composer**  
- **Node.js** >= 18.x  
- **npm** >= 9.x  
- **MySQL** or **PostgreSQL**  
- **Git**  

---

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/expense-tracker.git
   cd expense-tracker
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install && npm run build
   ```

4. Configure environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database credentials (MySQL or PostgreSQL supported).

5. Run database migrations:
   ```bash
   php artisan migrate
   ```

6. Seed default categories (optional):
   ```bash
   php artisan db:seed
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

---

## 📊 Usage

- **Transactions**  
  Add expenses manually via the Livewire form or through the Filament admin panel.  
  Transactions require description, amount, category, and date.

- **Recurring Expenses**  
  Choose `daily`, `weekly`, or `monthly` recurrence.  
  The system automatically calculates the next occurrence date and inserts into the `recurring_expenses` table.

- **Reports**  
  Navigate to the reports page to filter expenses by category or date range.  
  Charts display totals per category and trends over time.

- **Admin (Filament)**  
  Access `/admin` (or your configured panel route) for an intuitive dashboard.  
  Transactions edited here automatically sync recurring expenses.

---

## 🔧 Development Notes

- **Recurring Expense Logic**  
  - Shared helper `RecurringExpense::calculateNextOccurrence()` determines the next due date.  
  - Filament `CreateTransaction` and `EditTransaction` hooks ensure recurring expenses are created or updated alongside transactions.  
  - Livewire `AddTransaction` component also integrates this logic.

- **Customization**  
  - Categories can be global (shared) or user-specific.  
  - Extend Filament panels to manage budgets and notifications.  

---

## 📜 License

This project is open-source under the [MIT License](LICENSE).
