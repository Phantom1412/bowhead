# bowhead
PHP trading bot framework

## Requirements
This version of bowhead is set for php 7.2 and python 2.7
I have set this project on localhost only for testing and to learn something new 
and all was done on Mac OSX. This fork will work on other linux system but I will
never bother to do this for Windows. For Windows users you are on your own. 

### Before you start
Before you start to implement this and follow the commands below note that this was
set in Apache server in localhost. Because of this the chain of commands should be
followed as it is or use the folder name what you have created for this project.

On OSX you have built in apach server and the location is in /Library/WebServer/Documents/.
Here create a folder what you wish but for this tutorial we will use bowhead folder.

### Set up commands
1. https://github.com/deakzsolt/bowhead.git
2. cd bowhead
3. composer update
4. cp .env.example .env (insert here your api keys)
5. php artisan key:generate
6. php artisan migrate
7. php artisan db:seed
8. php composer.phar require andreas-glaser/poloniex-php-client dev-master 
add in Poloniex API creator Andreas



