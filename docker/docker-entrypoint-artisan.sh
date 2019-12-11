#!/usr/bin/env bash

/opt/goss -g goss-mysql-ready.yaml validate -r 30s -s 2s &&
php artisan migrate
