## About RumahImpian

**RumahImpian** is a modern, comprehensive Real Estate Marketplace platform designed to connect property seekers with trusted agents. Built on the robust **Laravel 11** framework, it leverages **FilamentPHP** for a powerful administrative backend and **Livewire** for a dynamic, reactive frontend experience.

## Key Features

- 🗺️ **Interactive Map Search:** A mobile-responsive map interface powered by [Leaflet.js](https://leafletjs.com/), featuring geolocation and card sliders.
- 🔍 **Advanced Filtering:** "Sticky" search bar with filters for price range, property type, location, and bedrooms.
- ⚡ **Agent Portal:** A dedicated dashboard for agents to manage listings, inquiries, and profiles.
- 📄 **Auto-Generated Brochures:** Instantly download PDF brochures for any property, complete with QR codes generated via **DomPDF**.
- ❤️ **Wishlist & Compare:** Save favorite homes and compare up to 3 properties side-by-side using local storage.
- 📱 **Fully Responsive:** Optimized UI for both desktop and mobile, including a custom drawer navigation for mobile users.

## Tech Stack

- **Framework:** Laravel 11.x
- **Admin Panel:** FilamentPHP v3
- **Frontend:** Blade Components, TailwindCSS v3
- **Interactivity:** Alpine.js, Livewire
- **Database:** MySQL 8.0+
- **PDF Generation:** barryvdh/laravel-dompdf
- **Maps:** Leaflet.js with CartoDB Tiles

## Installation Guide

Follow these steps to set up the project locally.

### 1. Prerequisites
Ensure you have the following installed on your machine:
- [PHP 8.2](https://www.php.net/) or higher
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- MySQL / MariaDB

### 2. Clone the Repository
```bash
git clone https://github.com/your-username/rumahimpian-marketplace.git
cd rumahimpian-marketplace
```

### 3. Install Dependencies
Install both PHP and JavaScript packages.

```bash
composer install
npm install
```

### 4. Environment Setup
Copy the example environment file and generate your application key.

```bash
cp .env.example .env
php artisan key:generate
```

**Configure Database:** Open the `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=property_marketplace
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Seeding (Important)
This project comes with a robust seeder that generates **50+ realistic properties** across Jakarta, Bandung, and Bali.

> **Note:** This command wipes the database and reseeds it fresh.

```bash
php artisan migrate:fresh --seed
```

### 6. Link Storage & Build Assets
Link the public storage to serve images and compile the frontend assets.

```bash
php artisan storage:link
npm run build
```

### 7. Run the Application

If you are using Laragon, the site should be available at: http://property-marketplace.test

```bash
php artisan serve
```

The site will be available at [http://property-marketplace.test](http://property-marketplace.test).

## 🔑 Login Credentials

The seeder creates the following default users for testing purposes.

| Role | Email | Password | Description |
| :--- | :--- | :--- | :--- |
| **Admin / Owner** | `admin@rumahimpian.id` | `password` | Full access to all settings. |
| **Agency Agent** | `siti@grandrealty.co.id` | `password` | Linked to "Grand Realty". |
| **Independent Agent** | `budi@gmail.com` | `password` | No agency affiliation. |

Agent Portal URL: http://property-marketplace.test/portal

## 📂 Key Routes

Here are the primary routes available in the application:

| Feature | Route | Description |
| :--- | :--- | :--- |
| **Home** | `/` | Main landing page with search. |
| **Map Search** | `/map` | Interactive map view of all properties. |
| **Property Detail** | `/property/{id}/{slug}` | Full details, gallery, and agent contact. |
| **Download PDF** | `/property/{id}/{slug}/pdf` | Generates a brochure with QR code. |
| **Wishlist** | `/wishlist` | View saved properties. |
| **Compare** | `/compare` | Compare up to 3 properties side-by-side. |
| **Agent Profile** | `/agent/{id}` | View agent details and their listings. |
| **Agent Portal** | `/portal` | **Admin/Agent Login Area** (Filament). |

## Contributing

Contributions are welcome! If you find a bug or want to add a feature, please open an issue or submit a pull request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).