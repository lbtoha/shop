# This Softivus Primary Dashboard 🎟️

Welcome to you in the Softivus Primary Dashboard. Start developing project using this dashboard.

---

## 🚀 Features

-   **User Management**: Secure authentication and authorization for participants and admins.
-   **Admin Management**: Create and manage admins with different roles and permissions.
-   **Localization**: Support for multiple languages for a global audience.
-   **Payment Integration**: Seamlessly integrated with popular gateways like Stripe, Skrill, Braintree, and more.
-   **Currency** Support multiple currencies with all payment gateways
-   **Notifications**: Notify users via email and SMS about lotteries, results, and updates.
-   **Real-Time Updates**: Use Reverb for real-time broadcast updates (if enabled).
-   **Mobile Friendly**: Fully responsive design for a seamless experience on all devices.

---

## 🛠️ Tech Stack

### Backend

-   **Framework**: Laravel 12
-   **Database**: MySQL / MariaDB
-   **Style**: Tailwindcss
-   **Real-Time Communication**: firebase
-   **Caching**: Redis/Memcache

### Frontend

-   **Framework**: Laravel Blade Engine
-   **Alternative Frontend**: Alpine.js

### Deployment

-   **Web Server**: Cpnel / Apache / Nginx
-   **Environment**: PHP 8.3^

### Payment Gateways

-   Integrated with:
    -   **Stripe**
    -   **Skrill**
    -   **Braintree**
    -   **Paypal**
    -   **Flutterwave**
    -   **Mollie**
    -   **PlayStack**

---

## 📂 Folder Structure

```plaintext
dashboard/
├── app/               # Application logic
├── bootstrap/         # Framework bootstrap files
├── config/            # Configuration files
├── database/          # Database migrations & seeds
├── public/            # Front-facing assets
├── resources/         # Views and frontend resources
├── routes/            # Routes configuration
├── storage/           # Logs, cache, and file uploads
├── tests/             # Automated tests
├── vendor/            # Composer dependencies
└── .env               # Environment configuration file

```

#### Installation

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate


Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

    npm install && npm run dev:admin and npm run dev:client

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000
