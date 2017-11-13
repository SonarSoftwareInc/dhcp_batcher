# Sonar DHCP Batcher

## What is this?

A tool for batching DHCP requests on large networks, and handling Option 82 requests for delivery to [Sonar](https://sonar.software).

## Installation

This tool has been tested on Ubuntu 16.04. It should work on any Linux system with the correct packages installed. There is an installation script in the root directly (install-ubuntu.sh) that will automatically set this up for you. If you plan to install on Ubuntu, you can just skip to **Simple installation**.

If you're not planning on using Ubuntu, you will need:

* PHP 7.0+ with the following extensions:
..* OpenSSL
..* PDO
..* Mbstring
..* Tokenizer
..* XML
* PostgreSQL 9.6+
* A functioning web server (e.g. Apache, Nginx, Caddy) that serves up the `dhcp_batcher` folder inside the repository.

### Simple installation

1. Install [Ubuntu 16.04](https://www.ubuntu.com/download/server)
2. Install git by typing `sudo apt-get install git`
3. Check out this repository //TODO: Add full instructions here
4. Enter the directory you checked out (typically `cd dhcp-batcher`, but you can run `ls` to see a list of directories in your current directory to check.)
5. Type `chmod +x install-ubuntu.sh`
6. Type `./install-ubuntu.sh`

## Configuration

Coming soon.
