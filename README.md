# HotsApi [![Build Status](https://travis-ci.org/poma/hotsapi.svg?branch=master)](https://travis-ci.org/poma/hotsapi)

[HotsApi.net](http://hotsapi.net/) is an open Heroes of the Storm replay database where everyone can download replays. It stores replays in a public AWS S3 bucket (currently in "Requester pays" mode) and provides and API to query replay metadata. Use  [Hotsapi.Uploader](http://hotsapi.net/upload) ([repo link](https://github.com/poma/Hotsapi.Uploader)) to upload your replay files.

Currently API is still in alpha and may change

# Installation

HotsApi is a PHP/Laravel app so the easiest way to run it locally is using [Homestead](https://laravel.com/docs/5.4/homestead). Alternatively, you can use a cookbook from [hotsapi.chef](https://github.com/poma/hotsapi.chef) repo that can automatically install all the dependencies and configure webserver.

## Homestead

In addition to defaul homestead config you will need:

* Install [heroprotocol](https://github.com/Blizzard/heroprotocol) parser: `cd /opt && sudo git clone https://github.com/Blizzard/heroprotocol.git`
* Make a globally availabe heroprotocol executable: `sudo ln -s /opt/heroprotocol/heroprotocol.py /usr/bin/heroprotocol`
* Make sure heroprotocol has executable permission `chmod +x /opt/heroprotocol/heroprotocol.py`
* Configure `.env` file `cp .env.example .env`
* Run `composer install`
* Run `php artisan migrate`
* Make sure `storage` dir is writable

## Chef

* SSH into a clean Ubuntu 16.04 installation
* Clone a chef repo `git clone https://github.com/poma/hotsapi.chef.git`
* `cd hotsapi.chef`
* Create a chef config file `cp chef.example.json chef.json`
* Modify `chef.json` if needed (test server should be able to start without any modifications)
* Run chef `sudo ./bootstrap.sh`

# Contributing

Pull requests are very much appreciated, with community involvement we could get much more features in much shorter time. You can see the list of current tasks in [project](https://github.com/poma/hotsapi/projects/1) page, you can freely pick one from "backlog" or "high priority" columns and start working on it.
