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
3. Check out this repository by typing `git clone https://github.com/sonarsoftware/dhcp_batcher`
4. Enter the directory you checked out (typically `cd dhcp_batcher`, but you can run `ls` to see a list of directories in your current directory to check.)
5. Type `chmod +x install-ubuntu.sh`
6. Type `./install-ubuntu.sh`

### Installation on an operating system other than Ubuntu

**You can skip this section if you followed the Simple installation section.**

If you're not planning on using Ubuntu, you will need:

* PHP 7.0+ with the following extensions:
    * OpenSSL
    * PDO
    * Mbstring
    * Tokenizer
    * XML
    * PGSQL
    * zip
* PostgreSQL 9.5+
* A functioning web server (e.g. Apache, Nginx, Caddy) that serves up the `dhcp_batcher` folder inside the repository. Check out `conf/default` for an example nginx configuration file.
* Redis

After setup, you will need to copy the `.env.example` file in the `dhcp_batcher` directory to `.env` and run `php artisan key:generate`. You'll need to create a PostgreSQL database, and enter the database name, username, and password in the `.env` file in the `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` fields respectively.

Once this is done, install [Composer](https://getcomposer.org) and run `composer install` inside the `dhcp_batcher` directory.

You will need to setup cron to run the Sonar scheduler. Check out the `sonar_scheduler` file in the `conf` directory for an example file that could be placed in `/etc/cron.d` or wherever is appropriate for your OS.

Finally, run the following commands (substituting /usr/share/dhcp_batcher/dhcp_batcher with wherever you installed the DHCP batcher to)

* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan migrate --force
* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan config:cache
* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan route:cache

You may need to reload PHP-FPM after the last two commands, if you are using PHP-FPM.

Any time you modify your .env file, you must run `/usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan config:cache` and reload PHP-FPM.

## Configuration

### Creating an initial user

After initial installation, you can create a new user. Type `php /usr/share/dhcp_batcher/dhcp_batcher/artisan make:user test@example.com`, replacing `test@example.com` with the user's email address, to generate a new user account. You can run this multiple times if you want to have additional users created.

### Resetting your password

Since the DHCP batcher has no capacity to send email, there is no *Forgot Password* function available in the web interface. To reset a user password, run `php /usr/share/dhcp_batcher/dhcp_batcher/artisan reset test@example.com` replacing `test@example.com` with the user's email address. You will be provided with a new random password on the command line.

### Logging in

To login, access the server IP/hostname in a browser (e.g. http://192.168.100.1.) Login using the username and password created in the **Creating an initial user** section.

### Delivering DHCP leases to the batcher

For this tool to work, your DHCP server must deliver leases to the batcher in a standard format. The data can either be sent as JSON in a POST, or can be encoded as GET parameters.

The format in JSON is show below. To send as GET parameters, simply encode each parameter into the URL.

```
{
    "leased_mac_address": "00:AA:BB:CC:DD:EE",
    "ip_address": "192.168.100.2",
    "remote_id": "BB:CC:DD:11:22:33", #Can be null
    "expired": false
}
```

`leased_mac_address` is the MAC address of the requesting device, and must be the MAC address of an inventory item on the customer account, unless `remote_id` is also specified, in which case, the `remote_id` will be used to find the customer account. `ip_address` is the IP address being assigned or expired. `remote_id` is optional. If you're using Option 82, this should be the MAC address of the relay agent. This must correspond to the MAC address of an inventory item on a customer account for the IP to be assigned. Finally, `expired` specifies whether or not the lease is expired or new. If this is a renewal or new assignment, `expired` should be `false`. Otherwise, expired should be `true`.

This data should be sent to the DHCP batcher server at `/api/dhcp_assignments` using basic HTTP authentication. The username and password to use for authentication is created by adding a DHCP server within the Sonar DHCP Batcher web interface.

Below is an example script you can use on a [MikroTik](http://mikrotik.com) DHCP server. This tool should work with any DHCP server that can create a HTTP request upon lease assignment or expiration.

```
:global username "dhcp_batcher_username" #The username for this DHCP server setup in the batcher.
:global password "dhcp_batcher_password" #The password for this DHCP server setup in the batcher.
:global url "batcher.example.com" #The URL of your batcher, can also be an IP address.
:global mode "http" #This should be 'http' or 'https' depending on if your batcher is using SSL. The default is 'http'.

:if ($leaseBound = 0) do={
   /tool fetch url="$mode://$url/api/dhcp_assignments?ip_address=$leaseActIP&leased_mac_address=$leaseActMAC&expired=1" mode=$mode keep-result=no user=$username password=$password
} else={
   { :delay 1 };
   :local remoteID
   :set remoteID [/ip dhcp-server lease get [find where address=$leaseActIP] agent-remote-id]
   /tool fetch url="$mode://$url/api/dhcp_assignments?ip_address=$leaseActIP&leased_mac_address=$leaseActMAC&remote_id=$remoteID&expired=0" mode=$mode keep-result=no user=$username password=$password
};
```

You can test this script by running the following command directly from your MikroTik terminal. You must replace the $url, $mode, $username, and $password variables with valid information.

```
/tool fetch url="$mode://$url/api/dhcp_assignments\?ip_address=192.168.100.1&leased_mac_address=00:00:00:00:00:00&remote_id=&expired=0" mode=$mode keep-result=no user=$username password=$password
```

### Linking the batcher to Sonar

There is an .env file located in your dhcp_batcher directory (typically /usr/share/dhcp_batcher.) This file has three properties in it - `SONAR_URL`, `SONAR_USERNAME`, and `SONAR_PASSWORD`. Enter the URL of your Sonar instance (e.g. https://example.sonar.software) in the `SONAR_URL` field. Enter a valid username and password that you can use to login to your instance in the `SONAR_USERNAME` and `SONAR_PASSWORD` field. This user requires *Account Create* and *Account Update* permissions, and should not be given any other permissions. **Do not use an administrative account here!**

You can test the user by executing `/usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan sonar:test`.

Once you're done, execute `/usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan config:cache` followed by `sudo systemctl reload php7.0-fpm`.

### Enabling SSL

It is strongly recommended that you secure this server using SSL. You can get a free Let's Encrypt certificate in order to this. There is a tutorial available [here](https://www.digitalocean.com/community/tutorials/how-to-set-up-let-s-encrypt-with-nginx-server-blocks-on-ubuntu-16-04) that steps you through configuration Let's Encrypt with nginx on Ubuntu. If you utilized the Ubuntu installation script, you should be able to follow these instructions to setup a free SSL certificate quickly. The nginx configuration file referenced in the tutorial is at `/etc/nginx/sites-available/default`.

### Upgrading

You can upgrade by running `php /usr/share/dhcp_batcher/dhcp_batcher/upgrade.php`, or just by checking out this repository and copying the files over the top. If you don't use the upgrade script, it's important to run the following commands after copying the repository files over:

* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan migrate --force
* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan config:cache
* /usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan route:cache
* sudo systemctl reload php7.0-fpm

If you'd like updates to happen automatically, you can add a cron script to run the upgrade file each day. There's an example script in `conf` called `auto_upgrades` - if you copy this into `/etc/cron.d` and then run `chmod 644 /etc/cron.d/auto_upgrades`, then your system will automatically upgrade every night at midnight if there's a new version.

## Troubleshooting

**By default, the batcher will not log many errors. You can enable enhanced logging by setting `APP_DEBUG` to `true` in your `.env` file. You will need to run `/usr/bin/php /usr/share/dhcp_batcher/dhcp_batcher/artisan config:cache` after modifying this file.**

Any errors are logged to `storage/logs/laravel.log`. You can view this in a standard installation by typing `tail -f /usr/share/dhcp_batcher/dhcp_batcher/storage/logs/laravel.log` and initiating some calls from your DHCP server.

You should not leave debug mode permanently enabled, unless you setup some kind of [log rotation](http://manpages.ubuntu.com/manpages/xenial/man8/logrotate.8.html) mechanism on the log file, as it will eventually grow to consume all storage space available.