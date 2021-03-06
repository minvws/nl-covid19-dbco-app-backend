# COVID-19 DBCO - backend

## Introduction
This repository contains the backend implementation of the Dutch COVID-19 DBCO app.

* The backend (api and portal) is located in the repository you are currently viewing.
* The iOS app can be found here: https://github.com/minvws/nl-covid19-dbco-app-ios
* The Android app can be found here: https://github.com/minvws/nl-covid19-dbco-app-android
* Designs can be found here: https://github.com/minvws/nl-covid19-dbco-app-design
* Technical documentation can be found here: https://github.com/minvws/nl-covid19-dbco-app-coordination

## Overview

* worker: implements a docker image that can be used to manually or periodically (e.g. cron) run commands
* api: implements the APIs 
* portal: implements a (potentially temporary) portal for healthcare (BCO) workers.

The workers and apis are developed using PHP 7 and the lightweight Slim framework (https://www.slimframework.com)
The portal is developed using PHP 7 and the Laravel 8 framework (https://laravel.com)

## Development

Prerequisites: A working Docker environment

Steps to run a local development environment:

- Create an `.env` file (you can create a copy of `.env.example` to get started). 
- Generate some passwords and enter them in the various .env file settings that are passwords
- Run `bin/setup-dev` to set up the environment (initialize database, install dependencies).

Tip: If you are using oracle instead of postgres, the setup might take a while and the 'Waiting for Oracle to launch on 1521...' seemingly takes forever. Run `./bin/docker-compose-dev logs --follow oracle` to see if Oracle is still busy installing.

If the command has completed successfully, you will be running 4 docker instances:
* The private api will run on port 8081 on localhost
* The public api will run on port 8082
* The healthcare api will run on port 8083
* The [healthcare portal](http://localhost:8084/) will run on port 8084

To start-up the development environment manually (outside of the setup process) you can use
`bin/docker-compose-dev up`. This command uses a docker-compose wrapper script that makes sure the
correct database is attached and adds some other development specific settings. 

To update your environment (e.g. run database migrations etc.) you can use `bin/update-dev`.

If you work on the portal and want to change javascript or css files, please run `./bin/docker-compose-dev exec portal npm run watch`, to ensure your changes get build and packaged to the public/ assets folders. 

If your development environment gets messed up, run `bin/reset-dev` to rebuild the environment.

## Testing

You can run the unit tests using `bin/phpunit`. 

## Development & Contribution process

The development team works on the repository in a private fork (for reasons of compliance with existing processes) and shares its work as often as possible.

If you plan to make non-trivial changes, we recommend to open an issue beforehand where we can discuss your planned changes.
This increases the chance that we might be able to use your contribution (or it avoids doing work if there are reasons why we wouldn't be able to use it).

Note that all commits should be signed using a gpg key.

## Release process

Prepare release (e.g. `VERSION=0.2.0`):

* Update version numbers for each Helm Chart `charts/[APP_NAME]` in `Chart.yaml` and `values.yaml`.
* Create Pull Request and make sure CI and E2E tests are working before merging changes.

Create a release in Github from the (stable) main branch. This will create a Git tag (e.g. `0.2.0`).

Docker Images will be pushed to private Github Container Registry:
`ghcr.io/minvws/nl-covid19-dbco-app-backend-private/[APP_NAME]:[VERSION]`

The `latest` docker tag will track the main branch. This is useful for development or test environments.
In production we will use tags with version numbers only.
