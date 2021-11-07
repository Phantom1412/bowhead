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
2. curl -O http://pear.php.net/go-pear.phar
3. sudo php -d detect_unicode=0 go-pear.phar
4. sudo pecl install trader
```
>On Osx if SIP is enabled use /usr/local to install pecl
``` 
5. php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
6. php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
7. php composer-setup.php
8. php -r "unlink('composer-setup.php');"
9. php composer.phar update
10. cp .env.example .env
11. php artisan key:generate
12. mysql -u root -p bowhead < app/Scripts/DBdump.sql
```

>Before you start the migrate setup in .env file connection to you Database, and then continue

```
13. php artisan migrate
14. php artisan db:seed
15. mkfifo quotes 
16. echo "* * * * * `which php` `pwd`/artisan schedule:run >> /dev/null 2>&1" | /usr/bin/crontab
17.  php composer.phar require andreas-glaser/poloniex-php-client dev-master
```

> You will need also the redis server what will run in the background while you wish to use the bot

```
18. brew install redis
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

### Example Signal
We wish to see how the basic signals work and do they drop error anywhere in the code. For this test use this command:
```
php artisan bowhead:example_signals
```

### Example Strategies
Just a simple test to show how the strategies works
```
php artisan bowhead:example_strategy
```

### Testing Candles
Now we can move to test the candles with the following command:
```
php artisan bowhead:test_candles
```
Should show you green,red for BTC-USD pair.

### Test Strategies
> This now uses the FX data for fiat currency will be rebuild for crypto

Test strategies and set here what is your best strategy. Using without test parameter will use live trading.
```
php artisan bowhead:test_strategies test
```

### GDAX trading BOT
> This is still under heavy development and a lot of changes, for now the real time trading is off

Now we are at the part what everyone wants to make money. Now this BOT is tied up with GDAX account 
and he will to there the tradings. In .env while testing SET THE SANDBOX API URL in CBURL like this:
```
CBURL=https://api-public.sandbox.gdax.com
```
When all is working as you expected then you can change to the live trading url. Now to start this bot run the
following command:
```
php artisan bowhead:gdax_scalper
```
There is a synchronisation for the data and if they are too old it will stop working.



#TODO list

> This is important in order to get the correct data and sort all
2. TODO check the multidimensional array handler not to use only first from array
 the getRecentData should have exchange ID in order to pull the data 

> TODO 2 is related to 3 task in tight and should create a normal view and make the bot to work
3. TODO GDAX autoamted trader BOT setup
3.1 

4. TODO create historic importer script in other hand it will not work


TODO test strategies uses FX streaming try to change to crypto