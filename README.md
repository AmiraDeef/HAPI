<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/AmiraDeef/HAPI/actions"><img src="https://github.com/AmiraDeef/HAPI/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About HAPI Backend API (حابي) 🌿

**HAPI (حابي)** — inspired by the Egyptian god of the Nile, symbolizing fertility and growth — is the **API backend** of an AI & IoT-powered agricultural system, designed to support Egyptian farmers through smart crop management solutions.

This repository contains **only the backend API**, developed using **Laravel**, with a focus on building a scalable, secure, and extensible RESTful API.

---

## Features

- API for **AI-based crop disease detection**.
- API for **IoT data integration and real-time monitoring**.
- Manage **land and crop history** data.
- Generate **smart farming recommendations** based on AI and IoT data.
- **API authentication** using Laravel Sanctum.

---

## Tech Stack

- **Laravel 10** (PHP framework)
- **MySQL** (Relational Database)
- **Sanctum** for secure API authentication.
- Integrations with **AI models** and **IoT devices**.

---

## Notes
- This is only the backend API, and does not include AI models, IoT devices, or frontend apps.
- Designed with focus on Egyptian agriculture needs, but flexible to expand.


## Installation

```bash
# Clone the repository
git clone https://github.com/AmiraDeef/HAPI.git
cd HAPI

# Install dependencies
composer install

# Copy and configure .env
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Serve the application
php artisan serve

