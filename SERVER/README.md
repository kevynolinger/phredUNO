phredUNO server
============
**Be aware that this project is still a work in progress and may not work properly yet!**

This is the server component of the project. It is completely written in PHP (since that was a requirement given by my teacher) and uses the [Ratchet PHP WebSocket] library.

## Requirements
* PHP 7.0 or greater installed
* [Composer] installed
* [Stunnel] - Not required, but recommended

## Clone & Install
1. `git clone git@github.com:kevynpferd/phredUNO.git`  
2. `cd phredUNO/SERVER`  
3. `composer install`

## Execution
1. Import the .sql in the SERVER directory in an EXISTING database
2. Add the credentials to access the database to the config.inc.php found in the SERVER directory
3. Navigate to the SERVER directory
4. Run `php Server.php`

## License
This project is licensed under the MIT license. To see full details, open up [LICENSE.MD].

[Ratchet PHP WebSocket]: https://github.com/ratchetphp/Ratchet 
[Composer]: https://getcomposer.org/
[Stunnel]: https://www.stunnel.org/index.html
[LICENSE.MD]: https://github.com/kevynpferd/phredUNO/blob/master/LICENSE.md
