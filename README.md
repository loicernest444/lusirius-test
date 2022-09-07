<h1 align="center"><b>Lusirius Test</b></h1>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# **Deployment**

## **Prerequisites**
- install composer
- install Docker-compose in your linux distribution
- If you don't have the JSON keyfile, activate Cloud vision in your google account and create new key for the project, download it and copy it as key.json into project root directory.

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
$ ./vendor/bin/sail artisan storage:link
```
```php
$ ./vendor/bin/sail npm run dev
```
to generate swagger documentation:
```php
$ ./vendor/bin/sail artisan l5-swagger:generate
```
to build and run containers execute:
```php
$ ./vendor/bin/sail up
```
if apache2 is running on port 80 run ```sudo service apache2 stop``` before retry

**_Now your application is available on http://localhost_**
# **API Routes**
### All API routes with Schema and examples are available on http://localhost/api/docs
![API documentation](public/swagger.png?raw=true "How API documentation is look")
<br>
<br>
===>**Just click on try it to test any route**
<br>
<br>

# **UI usage**
The UI is directly available on http://localhost, i have build it with vue.js 3 and vuex. In the UI you have only one page where you can see the list of reports. 
- Preview the reported image by clicking on it
- On the probability column you have the probability of containing sensitive content
- On the EVALUATED column, the green color means that the image report was evaluated and the red color means was not
- On the APPROVED column,  the green color means that the image report was approved; the red color means was rejected, the gray color means that the report was not moderated and you also have the label indicator
- On the ACTIONS column, you can hit the check icon to approve or reject the Image Report; you can hit the archive icon to archive the report; and you can revaluate the report by hit refresh icon
- Sort reports by decreasing probability of containing sensitive by clicking on **probability** column header

![Example Reports list](public/reports_list.png?raw=true "Example Reports list sort by decreasing probability")

## Callback

- Here http://localhost/api/callback-test is the callback example that i have implement, you can add it as image report callback but your endpoint must be an online endpoint.