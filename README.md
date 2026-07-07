# Soberat (Backend API)

> The RESTful API and core logic engine powering the Soberat cross-platform mobile application.

## 🚀 Overview

This repository contains the backend infrastructure for the Soberat platform. Built with Laravel and PHP, it securely handles user authentication, data persistence, and the complex biometric algorithms required to calculate Blood Alcohol Content (BAC) and estimated time to sobriety. 

The API is specifically engineered to serve the frontend hybrid mobile client, providing fast, reliable data points for historical consumption tracking and dynamic dashboard updates.

### Key Features
* **RESTful API Architecture:** Seamlessly serves the mobile frontend with structured JSON responses.
* **Secure Authentication:** Implements Laravel Passport for robust, token-based OAuth2 user authentication.
* **Biometric Processing:** Houses the core algorithms for calculating BAC based on user inputs.
* **Data Persistence:** Securely manages and stores historical consumption patterns in a MySQL database.

## 🛠️ Tech Stack

* **Core Framework:** PHP 7.2 / Laravel 7
* **Database:** MySQL
* **Authentication:** Laravel Passport
* **API Routing:** Laravel CORS
* **Asset Bundling:** Laravel Mix (Webpack)
* **Testing:** PHPUnit, Mockery

## 🚦 Getting Started

Follow these instructions to get a local copy of the backend server up and running.

### Prerequisites
* PHP (v7.2.5 or higher)
* Composer
* Node.js & npm
* MySQL

### Build & Run Instructions

1. Clone the repository:
git clone https://github.com/weatherr/Soberat-Back-end.git

2. Navigate into the directory:
cd Soberat-Backend

3. Install the PHP dependencies:
composer install

4. Install the Node dependencies and compile web assets:
npm install
npm run dev

5. Set up your environment variables:
cp .env.example .env
*(Make sure to update the .env file with your local MySQL database credentials).*

6. Generate application key
php artisan key:generate

7. Run the database migrations and install Passport:
php artisan migrate
php artisan passport:install

8. Start the development server:
php artisan serve
