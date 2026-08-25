# SPCF Clinic Backend: Getting Started Guide

Welcome to the **SPCF Clinic Backend** repository.

## 📋 Prerequisites

Ensure you have the following installed on your local machine before starting:

  * **PHP**: Version 8.4 or higher
  * **Composer**: Dependency Manager for PHP
  * **MySQL**: Database server
  * **Git**: Version control

-----

## 🚀 Installation & Setup

Follow these steps to set up the project locally.

### 1\. Clone the Repository

```bash
git clone https://github.com/SPCF-Clinic/spcf-clinic-backend
cd spcf-clinic-backend
```

### 2\. Install Dependencies

Install PHP and Node dependencies:

```bash
composer install
```

### 3\. Environment Configuration

Create your environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

### 4\. Database Migration & Seeding

On new installation:

```bash
php artisan migrate --seed
```

With existing DB:

```bash
php artisan migrate:fresh --seed
```

-----

## 🏃‍♂️ Running the Application

Start the local development server:

```bash
php artisan serve
```

Start the background processes:

```bash
php artisan schedule:work
```

The API will be available at: `http://127.0.0.1:8000`

-----