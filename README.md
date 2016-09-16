# cloudtrax-presence
Save data posted from your cloudtrax WiFi network

To deploy:
[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)

This will setup Heroku app with an end point to collect Cloudtrax Presence Reporting data into a ClearDB MySQL DB.  You can get (or change) your DB credentials in your Heroku App's Config Variables

You will then configure your Cloudtrax Presence Reporting 'Server Location' to your Heroku app location.

I have a Ruby/MongoDB implementation here:
[https://github.com/Skeyelab/cloudtrax-presence]
