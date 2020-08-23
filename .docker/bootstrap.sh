#!/bin/bash

echo 'Initializing application'

cp .env.example .env
composer install
