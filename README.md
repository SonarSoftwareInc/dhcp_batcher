# Sonar DHCP Batcher

## What is this?

A tool for batching DHCP requests on large networks, and handling Option 82 requests for delivery to [Sonar](https://sonar.software).

## Requirements

This tool doesn't do a lot. It receives DHCP assignments from your DHCP server(s), batches them into a single submission to Sonar, sends the batch, and logs any failures. There are two main possible bottlenecks:

* The HTTP server can't process requests fast enough. Since validation, authentication, and database access all consume resources, a very large amount of incoming requests can cause issues.
* The database can't read/write fast enough. This is mostly going to be a function of disk access speed.

If you are only handling assignments for a few thousand subscribers, and you don't anticipate long, unending floods of DHCP releases and renewals, a fairly modest server will work fine - a couple of cores, 4GB of RAM, and decently fast disks, preferably SSDs. If you expect large amounts of requests (tens/hundreds of thousands of subscribers, or very short DHCP leases) then additional resources will be required. More RAM and cores will help significantly, but you should monitor the server resources and increase where appropriate.

Please note that it is simple (and encouraged) to run multiple batchers on very large networks. It will scale much better to point 50 DHCP servers at batcher A, and 50 DHCP servers at batcher B, than it will to point 100 DHCP servers at a scaled up batcher. However, you should not run multiple batchers behind a load balancer, as it defeats the purpose of batching requests if the same batcher is not receiving all requests from one DHCP server. You certainly could run a failover configuration though, using something like [vrrpd](https://github.com/fredbcode/Vrrpd).

That being said, start small - most networks don't handle huge floods of DHCP requests, as they are normally reasonably spaced out based on lease times. The biggest thing you can do to prevent performance issues is not to set very short DHCP lease times.

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

### Resetting your password

//TODO: write me

### Logging in

To login, access the server IP/hostname in a browser (e.g. http://192.168.100.1.) Login using the username and password created in the **Creating an initial user** section.

### Delivering DHCP leases to the batcher

//TODO: write me

### Linking the batcher to Sonar

There is an .env file located in your dhcp_batcher directory (typically /usr/share/dhcp_batcher.) This file has three properties in it - `SONAR_URL`, `SONAR_USERNAME`, and `SONAR_PASSWORD`. Enter the URL of your Sonar instance (e.g. https://example.sonar.software) in the `SONAR_URL` field. Enter a valid username and password that you can use to login to your instance in the `SONAR_USERNAME` and `SONAR_PASSWORD` field. This user requires *Account Create* and *Account Update* permissions, and should not be given any other permissions. **Do not use an administrative account here!**

You can test the user by executing `/usr/bin/php /usr/share/dhcp_batcher/artisan sonar:test`.

### Enabling SSL

It is strongly recommended that you secure this server using SSL. You can get a free Let's Encrypt certificate in order to this. There is a tutorial available [here](https://www.digitalocean.com/community/tutorials/how-to-set-up-let-s-encrypt-with-nginx-server-blocks-on-ubuntu-16-04) that steps you through configuration Let's Encrypt with nginx on Ubuntu. If you utilized the Ubuntu installation script, you should be able to follow these instructions to setup a free SSL certificate quickly. The nginx configuration file referenced in the tutorial is at `/etc/nginx/sites-available/default`.