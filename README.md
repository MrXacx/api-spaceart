# API-SpaceArt

> REST API to manage authentication and services from SpaceArt.

**ATTENTION!** This repository was created after the commit [ad0a4b4](https://github.com/MrXacx/spaceart/commit/e7915af8d6122693a4a91090e14335389acbf07b) from [MrXacx/spaceart](https://github.com/MrXacx/spaceart/)

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP: Make sure you have PHP 8.1 or higher for running the API. You can download it [here](https://www.php.net/downloads.php).

- Composer: Make sure you have Composer installed on your system to install our dependencies. You can download it [here](https://getcomposer.org/download/).

- Laragon: Make sure you have Laragon server to host the PHP files and the database. You can download it [here](https://laragon.org/download/).

- Git: Make sure you have Git to clone our project. You can download it [here](https://git-scm.com/).

## Getting Started

To get started with the API SpaceArt, follow these steps:

1. Clone the repository to your local machine using the following command: `git clone https://github.com/MrXacx/api-spaceart.git`
2. Install the project dependencies: `composer install`
3. Generate your laravel key: `php artisan key:generate`
4. Start server on Laragon
5. Insert your DBMS credentials into environment variables (.env):
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    ```
6. Run the migrations: `php artisan migrate`
7. Optionally, seed your database with mocked data: `php artisan db:seed`
