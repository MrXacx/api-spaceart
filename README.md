# API-SpaceArt

> REST API to manage sign, posts and services from SpaceArt.

**ATTENTION!** This repository was created after the commit [ad0a4b4](https://github.com/MrXacx/spaceart/commit/e7915af8d6122693a4a91090e14335389acbf07b) from [MrXacx/spaceart](https://github.com/MrXacx/spaceart/)

**ATTENTION**: Our routes are [here](./docs/SUMMARY).

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP: Make sure you have PHP for runnig the API. You can download it [here](https://www.php.net/downloads.php).

- Composer: Make sure you have Composer installed on your system to install our dependencies. You can download it [here](https://getcomposer.org/download/).

- XAMPP: Make sure you have XAMPP to manager your database. You can download it [here](https://www.apachefriends.org/pt_br/download.html).

- Git: Make sure you have Git to clone our project. You can download it [here](https://git-scm.com/).

## Getting Started

To get started with the API SpaceArt, follow these steps:

1. Clone the repository to your local machine using the following command:

`git clone https://github.com/MrXacx/api-spaceart.git`

2. Install the project dependencies:

`composer install`

3. Start Apache and MySQL on XAMPP

4. Create the database structure with [config's files](./config/)

5. Insert your phpMyAdmin credentials into [setup.ini](./public_html/src/setup.ini)

6. Start the development server:

`php -S localhost:8000 -t public_html`
