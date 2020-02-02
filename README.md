To run this application, you should have PHP 7.2 and Symfony 5.0. Also its recommended to install [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html).

To start project, just run `symfony server:start` and go to http://127.0.0.1:8000

***Creating authors***
To create author, run `bin/console create-author` command, for example:
```
bin/console create-author --email zrino.pernar@gmail.com --password actualPassword Zrino Pernar 24-07-1994 "my biography" male Zagreb
```
