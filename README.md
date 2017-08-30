# HotsApi

[HotsApi.net](http://hotsapi.net/) is an open Heroes of the Storm replay database where everyone can download replays. It stores replays in a public AWS S3 bucket (currently in "Requester pays" mode) and provides and API to query replay metadata.

Currently API is still in alpha and may change

# Installation

HotsApi is a PHP/Laravel app so the easiest way to run it locally is using [Homestead](https://laravel.com/docs/5.4/homestead). Alternatively, you can use a cookbook [hotsapi.chef](https://github.com/poma/hotsapi.chef) repo that can automatically install all the dependencies and configure webserver.

## Homestead

In addition to defaul homestead config you will need:

* Install [heroprotocol](https://github.com/Blizzard/heroprotocol) parser: `cd /opt && sudo git clone https://github.com/Blizzard/heroprotocol.git`
* Make a globally availabe heroprotocol executable: `sudo ln -s /opt/heroprotocol/heroprotocol.py /usr/bin/heroprotocol`
* Configure `.env` file
* Run `composer install`
* Run `php artisan migrate`
* Make sure `storage` dir is writable

## Chef

* SSH into a clean Ubuntu 16.04 installation
* Clone a chef repo `https://github.com/poma/hotsapi.chef.git`
* `cd hotsapi.chef`
* Modify a config file with your `.env` values `cp chef.example.json chef.json && vi chef.example`
* Run chef `./bootstrap.sh`
* Run hotsapi deploy script `cd /var/www/hotsapi && ./deploy.sh`

