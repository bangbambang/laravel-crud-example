## Tech Stack

- PHP 8.x
- MariaDB 10.4
- Laravel 9.x
- SQLite & pdo_sqlite extension for HTTP testing

## Setup

- Make sure dependency is met, clone the repo
- run `composer setup`

## Testing

- Make sure SQLite and pdo_sqlite is enabled
- Create empty sqlite database. e.g `touch [nameofdb].sqlite`
- Edit the following config in `env.testing`, make sure to use absolute path
    `DB_DATABASE=C:\tools\wnmp\WWW\candidate\surplus.sqlite`
- run `composer test`
