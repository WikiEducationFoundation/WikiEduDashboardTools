# WikiEduDashboardTools
A set of PHP endpoints for pulling revision and article data from the Replica databases in the Wikimedia Cloud environment

## Setup on Toolforge
* Clone the git repo in the tool's home directory
* `webservice start`

## Setup on Wikimedia Cloud VM
* Create a Debian server with the `web` security group
* Add DNS for an external URL (like dashboard-replica-endpoint.wmcloud.org)
* `sudo apt install apache2 php libapache2-mod-php php-mysql`
* Clone the git repo into /var/www/
* Disable the default site and add a new one (dashboard-too.conf)
```
<VirtualHost *:80>
    ServerName dashboard-replica-endpoint.wmcloud.org
    ServerAdmin sage@wikiedu.org   
    DocumentRoot /var/www/WikiEduDashboardTools/public_html
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
* Increase the PHP memory_limit in `/etc/php/7.4/apache2/php.ini` or similar location (to 4096M)
  * This ensures that queries that return very large amounts of data won't run PHP out of memory while converting query results to JSON.
* Enable the new site, and visit /index.php.
* Add Replica database credentials: copy `replica.my.cnf` from a Toolforge tool account into the tool's root directory.
* Test a query endpoint, like `/revisions.php?lang=en&project=wikipedia&usernames[]=Ragesoss&start=20140101003430&end=20171231003430`
