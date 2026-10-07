# Business Finance and Scheduling System

A web application for small-business financial management and scheduling: accounts payable and receivable, purchases, suppliers, clients, cash flow and a company calendar, all behind authenticated access.

Built solo as my capstone project (TCC) for the Technical Diploma in Information Technology, 2025.

## Features

- **Dashboard** with an overview of the company's financial position
- **Accounts payable and receivable**, with quick status toggling
- **Purchases** linked to suppliers, with finalize and cancel actions
- **Clients and suppliers** registration (full CRUD)
- **Cash flow** report with export to PDF and Excel
- **Calendar** for company events and due dates
- **User management**
- **Authentication** with login rate limiting (5 attempts per minute)

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Blade, Tailwind CSS 4, Vite |
| Reports | DomPDF, Laravel Excel |
| Tests | Pest |

## Running locally

Requirements: PHP 8.2+, Composer, Node.js and a database supported by Laravel.

```bash
git clone https://github.com/R4P0S1T0/TCC-2025.git
cd TCC-2025

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`, then:

```bash
php artisan migrate
composer run dev
```

The application will be available at `http://localhost:8000`.

## Tests

```bash
composer test
```

## Author

Renan Morano - [LinkedIn](https://www.linkedin.com/in/renanmorano)

## License

MIT
