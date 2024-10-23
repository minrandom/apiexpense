## Expense Tracker API

This repository contains the Expense Tracker API built using **Laravel** and **MySQL**. The API provides functionality to manage users, profile, income, expenses, categories, and payment methods. It also integrates with **Google Drive** for image uploads and stores receipts and profile pictures.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
  - [Authentication](#authentication)
  - [Profile](#profile)
  - [Income](#income)
  - [Expenses](#expenses)
  - [Categories](#categories)
  - [Payment Methods](#payment-methods)
- [Google Drive Integration](#google-drive-integration)

---

## Features

- JWT-based authentication.
- CRUD operations for user profiles.
- CRUD operations for income and expenses, including image uploads (receipts).
- CRUD operations for categories and payment methods.
- Image uploads for profile pictures, income receipts, and expense receipts, with Google Drive integration.
- Role-based routes for users.

---

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/minrandom/apiexpense.git
   cd apiexpense
   ```

2. **Install dependencies:**
   Make sure you have **Composer** installed on your system.
   ```bash
   composer install
   ```

3. **Set up the environment file:**
   - Copy the `.env.example` file to `.env`.
   ```bash
   cp .env.example .env
   ```
   - Configure your database, Google API credentials, and any other necessary environment variables (API keys, email settings, etc.) in the `.env` file.

4. **Generate an application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**
   Set up the database tables by running the migrations.
   ```bash
   php artisan migrate
   ```

6. **Set up storage link (for image uploads):**
   Create a symbolic link between storage and public folders.
   ```bash
   php artisan storage:link
   ```

7. **Run the application:**
   ```bash
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`.

---

## API Endpoints

### Authentication

- **Login**:  
  `POST /api/login`  
  **Payload:**
  ```json
  {
    "email": "user@example.com",
    "password": "your_password"
  }
  ```

- **Register**:  
  `POST /api/register`  
  **Payload:**
  ```json
  {
    "name": "John Doe",
    "email": "user@example.com",
    "password": "your_password",
    "password_confirmation": "your_password"
  }
  ```

- **Logout** (requires JWT token):  
  `POST /api/logout`

---

### Profile

- **Get Profile** (requires JWT token):  
  `GET /api/profile`  
  **Response:**
  ```json
  {
    "id": 1,
    "user_id": "1",
    "birthday": "2000-08-10",
    "gender": "male",
    "job": "programmer",
    "profile_pic_url": "https://your-api.com/storage/profile_pictures/filename.jpg",
    "created_at": "2024-10-18T12:28:42.000000Z",
    "updated_at": "2024-10-18T12:28:42.000000Z"
  }
  ```

- **Update or Create Profile**:  
  `POST /api/profile`  
  **Payload:**
  ```json
  {
    "birthday": "1995-05-15",
    "gender": "male",
    "job": "developer",
    "file": "profile_image.jpg"  // file upload (optional)
  }
  ```

---

### Income

- **Get All Incomes** (requires JWT token):  
  `GET /api/incomes`  
  **Response:**
  ```json
  [
    {
      "id": 1,
      "user_id": "1",
      "category_id": "2",
      "payment_method_id": "3",
      "amount": "5000",
      "datetime": "2024-10-18T12:28:42.000000Z",
      "notes": "Freelance work",
      "receipt_url": "https://your-api.com/storage/income_receipts/receipt.jpg"
    }
  ]
  ```

- **Add New Income**:  
  `POST /api/incomes`  
  **Payload:**
  ```json
  {
    "category_id": "1",
    "payment_method_id": "2",
    "amount": "1000",
    "datetime": "2024-10-20",
    "notes": "Salary",
    "file": "income_receipt.jpg"  // file upload (optional)
  }
  ```

---

### Expenses

- **Get All Expenses** (requires JWT token):  
  `GET /api/expenses`  
  **Response:**
  ```json
  [
    {
      "id": 1,
      "user_id": "1",
      "category_id": "2",
      "payment_method_id": "3",
      "amount": "2000",
      "datetime": "2024-10-18T12:28:42.000000Z",
      "notes": "Groceries",
      "receipt_url": "https://your-api.com/storage/expense_receipts/receipt.jpg"
    }
  ]
  ```

- **Add New Expense**:  
  `POST /api/expenses`  
  **Payload:**
  ```json
  {
    "category_id": "1",
    "payment_method_id": "2",
    "amount": "150",
    "datetime": "2024-10-20",
    "notes": "Dinner",
    "file": "expense_receipt.jpg"  // file upload (optional)
  }
  ```

---

### Categories

- **Get All Categories** (requires JWT token):  
  `GET /api/categories`  
  **Response:**
  ```json
  [
    {
      "id": 1,
      "name": "Salary",
      "type": "income"
    },
    {
      "id": 2,
      "name": "Groceries",
      "type": "expense"
    }
  ]
  ```

- **Add New Category**:  
  `POST /api/categories`  
  **Payload:**
  ```json
  {
    "name": "Bonus",
    "type": "income"
  }
  ```

---

### Payment Methods

- **Get All Payment Methods** (requires JWT token):  
  `GET /api/payment-methods`  
  **Response:**
  ```json
  [
    {
      "id": 1,
      "name": "Credit Card",
      "user_id": null  // system-wide method, available to all users
    },
    {
      "id": 2,
      "name": "Bank Transfer",
      "user_id": "1"  // user-specific method
    }
  ]
  ```

- **Add New Payment Method**:  
  `POST /api/payment-methods`  
  **Payload:**
  ```json
  {
    "name": "PayPal"
  }
  ```

---

## Google Drive Integration

The **Google Drive** integration allows users to upload receipts and profile pictures to Google Drive. Follow these steps to configure Google Drive API:

1. Obtain Google API credentials and create a new project in the **Google Cloud Console**.
2. Enable the **Google Drive API** for your project.
3. Download the credentials file (`credentials.json`) and store it in the `app/` folder of your Laravel project.
4. Set up the `GoogleDriveService` class for uploading files.

To upload files, use the endpoints for profile, income, or expense with the `file` field in the payload.

---

That's it! 🎉 You are now ready to run the Expense Tracker API and integrate it with your frontend.

---

You can copy this to your `README.md`, and make any tweaks based on additional configurations or routes you might add later. Let me know if you need further adjustments!
