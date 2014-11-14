```
NB! This is reference project from 2010.
Tags: PHP5, OOP, MVC, namespaces, interfaces, exceptions,
      PDO, reflection, REST, factory, DAO, client-server.
```

Theater ticket booking service
==============================

Prototype of web-based ticket booking service. Consists of independent server and client PHP applications communicating using restful web-services.

## Requirements

- OS: Any where php runs (tested only on Windows)
- Database: SQLite, PostgreSQL (tested only on 8.4)
- PHP >=5.3

This application was developed on PHP 5.3.1 using PHP 5.3 specific features like namespaces and closures. Application does not contain any OS specific logic and should run on all systems which have PHP support.

## Security

System expects all incoming requests to be signed. Request signing logic is similar to the one used by Flickr: http://www.flickr.com/services/api/auth.spec.html#signing

## Directory structure

```
client/                         Client application
  config/development.php        Client configuration file
  lib/                          Client application files
  public/                       Web document root
  views/                        View templates

server/
  config/development.php        Server configuration file
  lib/                          Server application files
  data/ts.s3db                  SQLite database (development environment)
  public/                       Web document root
  setup_database.sql            Database dump

lib/                            Shared libraries
  Jaer/                         Small library created for this project
  tsSDK/                        Ticket selling service SDK package

README.md                       This file
architecture.pdf                Architecture overview
```

## Screenshots

![client](http://i.imgur.com/1oswQid.png)
![server](http://i.imgur.com/qjhzk2x.png)
