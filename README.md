# Sonar DHCP Batcher

## What is this?

A tool for batching DHCP requests on large networks, and handling Option 82 requests for delivery to [Sonar](https://sonar.software).

## Requirements

TODO: Write me

## Installation

### Simple installation

Please note that following the instructions below will quickly setup the DHCP batcher on a dedicated server/VM. **These steps will make changes to various system configuration settings, and should not be used if you plan to run this on a shared server.**

1. Install [Ubuntu 16.04](https://www.ubuntu.com/download/server)
2. Install git by typing `sudo apt-get install git`
3. Check out this repository //TODO: Add full instructions here
4. Enter the directory you checked out (typically `cd dhcp-batcher`, but you can run `ls` to see a list of directories in your current directory to check.)
5. Type `chmod +x install-ubuntu.sh`
6. Type `./install-ubuntu.sh`

### Installation on an operating system other than Ubuntu

**You can skip this section if you followed the Simple installation section.**

If you're not planning on using Ubuntu, you will need:

* PHP 7.0+ with the following extensions:
..* OpenSSL
..* PDO
..* Mbstring
..* Tokenizer
..* XML
..* PGSQL
* PostgreSQL 9.5+
* A functioning web server (e.g. Apache, Nginx, Caddy) that serves up the `dhcp_batcher` folder inside the repository. Check out `conf/default` for an example nginx configuration file.
* Redis

After setup, you will need to copy the `.env.example` file in the `dhcp_batcher` directory to `.env` and run `php artisan key:generate`. You'll need to create a PostgreSQL database, and enter the database name, username, and password in the `.env` file in the `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` fields respectively.

Once this is done, install [Composer](https://getcomposer.org) and run `composer install` inside the `dhcp_batcher` directory.

Finally, run the following commands:

* /usr/bin/php /usr/share/dhcp_batcher/artisan migrate --force
* /usr/bin/php /usr/share/dhcp_batcher/artisan config:cache
* /usr/bin/php /usr/share/dhcp_batcher/artisan route:cache

You may need to reload PHP-FPM after the last two commands, if you are using PHP-FPM.

## Configuration

### Creating an initial user

After initial installation, you can create a new user. Type `php /usr/share/dhcp_batcher/artisan make:user test@example.com`, replacing `test@example.com` with the user's email address, to generate a new user account. You can run this multiple times if you want to have additional users created.