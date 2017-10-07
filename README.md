# Travel Book

## Requirements 

* Docker
* Docker Compose
* Docker Repository: iamluc/host-manager

## Installation

Use this command:

```bash
cp .env.dist .env
```

This file will be used for set environment variables.


## Start application

Use this command:

```bash
make start
```

## Access to application

You can go to: [http://web.travelbook/](http://web.travelbook/)

For go to API documentation, you can follow this link: [http://web.travelbook/api/resources](http://web.travelbook/api/resources) 


## Future

* Date format function locale
* Improvements css: filters -> 2 columns ? with in tables ? Using Saas
* Improvements js: datetimepicker bugs
* Improvements style in user features
* Live notifications
* Steps 1 and 2 travel: if place does not exists then create it
* Add possibility to delete travel or picture
* Add behat tests (with selenium)
* Add ELK for logs ?
