# HotsApi [![Build Status](https://travis-ci.org/hotsapi/hotsapi.svg?branch=master)](https://travis-ci.org/hotsapi/hotsapi) [![Join Discord Chat](https://img.shields.io/discord/650747275886198815?label=Discord&logo=discord)](https://discord.gg/cADfdFP)

[HotsApi.net](https://hotsapi.net/) is an open Heroes of the Storm replay database where everyone can download replays. It stores replays in a public AWS S3 bucket (currently in "Requester pays" mode) and provides an API to query replay metadata. Use  [Hotsapi.Uploader](https://hotsapi.net/upload) ([repo link](https://github.com/poma/Hotsapi.Uploader)) to upload your replay files.

There are API libraries for [Ruby](https://github.com/tbuehlmann/hots_api) and [Python](https://github.com/MakiseKurisu/hotsapi)

Currently API is still in alpha and may change

# Installation

You need to have `docker` and `docker-compose` installed on your machine

```shell script
cp .env.example .env
# edit env file if needed

# bring up mysql service
docker-compose up -d mysql

# run migrations 
docker-compose run artisan migrate:fresh --seed

# populate maps, heroes, talents tables
docker-compose run artisan hotsapi:fetch-translations
docker-compose run artisan hotsapi:fetch-talents

# run webserver, available at localhost:8080
docker-compose up -d hotsapi

# Look at logs
docker-compose logs -f

# parse uploaded replays
docker-compose run artisan hotsapi:parse
```

# Contributing

Pull requests are very much appreciated. With community involvement we could get much more features in much shorter time. You can see the list of current tasks in [project](https://github.com/poma/hotsapi/projects/1) page. You can freely pick one from "backlog" or "high priority" columns and start working on it.
