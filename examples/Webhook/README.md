Run in current folder

``` sh
composer install
php -S localhost:8080 Webhook.php
```

Make your server externally accessible.
For testing purpose, you can install  [ngrok](https://ngrok.com/) and run:

``` sh
ngrok http 8080
```
The output should be something similar to:
```
Session Status                online
Session Expires               7 hours, 59 minutes
Version                       2.2.8
Region                        United States (us)
Web Interface                 http://127.0.0.1:4040
Forwarding                    http://cbfcdae9.ngrok.io -> localhost:3000
Forwarding                    https://cbfcdae9.ngrok.io -> localhost:3000
```
Copy the "https" link (in this case https://cbfcdae9.ngrok.io) to your clipboard and then login to your Coinbase Commerce dashboard and paste it into the Webhook section of your settings page. Also remember to copy your shared secret from the Webhook settings into your Webhook.php configuration.
Send test request.
