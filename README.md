<h1 align="center"><b>Pictureworks Test</b></h1>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Deployment

## Prerequisites
- install composer
- install Docker-compose in your linux distro

To deploy the project, run commands below in the project root directory.<br>
PS: you can create "sail" alias
```bash
$ composer install
```
```bash
$ ./vendor/bin/sail npm install
```
```bash
$ cp .env.example .env
```
```php
$ ./vendor/bin/sail artisan key:generate
```
```php
$ ./vendor/bin/sail artisan migrate
```
```php
$ ./vendor/bin/sail npm run dev
```
to build and run containers execute:
```php
$ ./vendor/bin/sail up
```
if apache2 is running on port 80 run ```sudo service apache2 stop``` before retry

**_Now your application is available in http://localhost_**
# **API Routes**
### **1. http://127.0.0.1/api/reports**
That is the route to fetch all image reports with <b>GET</b> method


### **2. http://127.0.0.1/report-image**
That is the route to report and image reports with <b>POST</b> method

### **3. API route http://127.0.0.1/api/{something}**
This api route is use to send sthg datas for updating
- Example :
```json
{
  "id": 2,
  "password": "720DF6C2482218518FA20FDC52D4DED7ECC043AB",
  "comments": "Json test"
}
```
