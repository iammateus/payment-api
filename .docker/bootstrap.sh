#!/bin/bash

echo 'Initializing application'

if [ ! -e .env ]
then
    echo 'Creating .env file'
    cp .env.example .env
fi

composer install
