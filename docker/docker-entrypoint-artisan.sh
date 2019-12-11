#!/usr/bin/env bash

# give time for the MySQL container to init
sleep 5

# Attempt to run migrations
/opt/goss/goss -g /opt/goss/goss-mysql-ready.yaml validate -r 30s -s 2s
