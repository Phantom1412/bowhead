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

If you don't wish to install all requirements into the osx main system create a virtual 
environment. Here is a nice tutorial what you need to set this up:
http://sourabhbajaj.com/mac-setup/Python/virtualenv.html
If you have other versions of python then setup the venv to your folder where is the bowhead set.
This is the command what will take your built in python2.7 (check what python version do you have)
and set it to the bowhead folder:
virtualenv --python=/usr/bin/python2.7 /Library/WebServer/Documents/bowhead

When this is set you can start to follow the commands. Please note that if you are running 
this in venv then all this will be installed into folder other hand it will go into
system folder.

### Set up commands
```
1. https://github.com/deakzsolt/bowhead.git2. cd bowhead
2. php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
3. php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
4. php composer-setup.php
5. php -r "unlink('composer-setup.php');"
6. php composer.phar update
7. curl -O http://pear.php.net/go-pear.phar
8. sudo php -d detect_unicode=0 go-pear.phar
9. sudo pecl install trader
10. cp .env.example .env
11. php artisan key:generate
12. mysql -u root -p bowhead < app/Scripts/DBdump.sql
```

>Before you start the migrate setup in .env file connection to you Database, and then continue

```
13. php artisan migrate
14. php artisan db:seed
```

> You will need also the redis server what will run in the background while you wish to use the bot

```
15. brew install redis
```

# How to start/use

### Start Redis Server 
> Before you start ot execute the commands start the redis server and leave it running in the background.
Type in the following command to start it:
```
redis-server
```

If you have filled the API's in your .env file then you should start to see data flowing into the
mysql database under the "bh_tickers". This way the cron is importing the streaming data so you don't
need to use the fx_stream to import the data.

### Example usage
Before you start playing around check with the following command is all set:
```
php artisan bowhead:example_usage
```
This list should show you all green at the end (check the indicator for weekly it drops error)

### Testing Candles
Now we can move to test the candles with the following command:
```
php artisan bowhead:test_candles
```

### Check Signal
We wish to see how the basic signals work and do they drop error anywhere in the code. For this test use this command:
```
php artisan bowhead:example_signals
```
Should show you green,red for BTC-USD pair.


TODO check do we need this API handler for Poloniex
???. php composer.phar require andreas-glaser/poloniex-php-client dev-master

TODO check the multidimensional array handler not to use only first from array

TODO test strategies and go trough