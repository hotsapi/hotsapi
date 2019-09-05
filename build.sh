#!/usr/bin/env bash
docker build -f docker/Dockerfile.heroprotocol -t hotsapi/heroprotocol .
docker build -f docker/Dockerfile.parser -t hotsapi/parser .
docker build -f docker/Dockerfile.hotsapi -t hotsapi/hotsapi .
docker build -f docker/Dockerfile.webserver -t hotsapi/webserver .
