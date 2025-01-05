sudo apt install apache2
sudo ufw allow 'Apache'
sudo ufw status

sudo nano /var/www/html/system-info.php
sudo nano /var/www/html/index.html

sudo systemctl status apache2
sudo systemctl start apache2
sudo systemctl reload apache2




Here is the Git markdown file (`setup-apache-virtual-host.md`) with the provided instructions:

```markdown
# Setting Up Apache Virtual Hosts on Ubuntu 20.04

This guide will show you how to set up virtual hosts on Apache to host multiple websites on a single server. Follow these steps to set up a domain `your_domain`. Be sure to replace `your_domain` with your actual domain name.

## Step 1: Create the Directory for Your Domain

Create a directory to host the files for `your_domain`:

```bash
sudo mkdir /var/www/your_domain
```

## Step 2: Set Permissions

Assign ownership of the directory to your user:

```bash
sudo chown -R $USER:$USER /var/www/your_domain
```

Ensure the correct permissions:

```bash
sudo chmod -R 755 /var/www/your_domain
```

## Step 3: Create a Sample HTML Page

Create an `index.html` page for testing:

```bash
sudo nano /var/www/your_domain/index.html
```

Add the following HTML content to the file:

```html
<html>
    <head>
        <title>Welcome to Your_domain!</title>
    </head>
    <body>
        <h1>Success! The your_domain virtual host is working!</h1>
    </body>
</html>
```

Save and close the file.

## Step 4: Create a Virtual Host Configuration File

Create a new virtual host configuration file:

```bash
sudo nano /etc/apache2/sites-available/your_domain.conf
```

Add the following content:

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName your_domain
    ServerAlias www.your_domain
    DocumentRoot /var/www/your_domain
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Make sure to replace `your_domain` with your actual domain name. Save and close the file.

## Step 5: Enable the New Site

Enable your new site configuration:

```bash
sudo a2ensite your_domain.conf
```

Disable the default site:

```bash
sudo a2dissite 000-default.conf
```

## Step 6: Test the Configuration

Test the Apache configuration for errors:

```bash
sudo apache2ctl configtest
```

If everything is correct, you should see:

```
Syntax OK
```

## Step 7: Restart Apache

Restart Apache to apply the changes:

```bash
sudo systemctl restart apache2
```

## Step 8: Test the Virtual Host

Visit your domain in the browser (replace `your_domain` with your actual domain):

```
http://your_domain
```

You should see the success page with the message:

```
Success! The your_domain virtual host is working!
```

---

By following these steps, you have successfully set up an Apache virtual host on Ubuntu 20.04 for your domain.
```

This `.md` file should be useful for anyone wanting to replicate the process of setting up Apache virtual hosts on Ubuntu 20.04. Let me know if you need any adjustments!