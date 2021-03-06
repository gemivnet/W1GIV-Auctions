# W1GIV Auctions

W1GIV Auction is a web based management system for real life auctions. W1GIV Auctions was originally developed for two Amateur Radio clubs in Southestern Connecticut to use on their yearly auctions (hence the name W1GIV Auctions).

This program is no longer being activly developed. The current version is stable and is what is being used with the clubs.

## Features

### Organizations

Organizations do not have their own accounts, instead contacts, or members of the organization, have accounts associated with that organization. Users can be associated with multiple organizations, and upon login they will be prompted to choose which organization they would
like to log in as.

An auction created under an organization will be viewable and editable by any member of that organization.

### Auctions

This program is set up to handle commission auctions where a certain percentage of the sale is given to organization. There is also a minimum selling price where if an item sells below that price, all proceeds of that sale are given to the organization.

### Attendees

Attendees can either be registered by a member of the organization, or self registering kiosks can be setup at the action site. An additional field can be added to the registration form which will prompt the user for an additional identifier such as a member number.

Attendees can be added quickly by asking for a unique identifier and then using the lookup feature to add an attendee from a previously attended auction by that organization. If multiple results appear, the person registering can choose which one is intended.

### Items

Items are registered with an item name, price, buyer number, and seller number. Multiple people can enter items at the same time to make the process easier, or if there are multiple auctions going on at the same time with the same buyer and seller numbers. There is duplicate feature which will repopulate the form with the last item entered but with the buyer number blank if the same seller is selling multiple of the same items with the same price.

### Checkout

Multiple checkout stations can be setup to make checkout easier for the attendee. The buyer/seller number is entered and their invoice in generated with everything they bought and sold. A subtotal is then calculated and the amount owed or due is displayed. The invoice can be printed and will be emailed if the attendee selected that option upon registration. The payment method can be either cash or check, and then the attendee is mark as paid. A list of attendees that have not paid, and the total number of items sold by the organization can be displayed.

### Email

After the auction, the organization can send out promotional emails to the attendees who opted to receive emails upon registration. Attendees can unsubscribe from emails at any time.

## Installation

```sudo apt-get update```

Install Apache

```sudo apt-get install apache2```

Install MySQL

```sudo apt-get install mysql-server```

Take note of the password chosen for the MySQL database.

Next, set the default sql mode.

```sudo nano /etc/mysql/my.cnf```

Add the following to the end of the file

```
[mysqld]
sql-mode=""
```

Save and exit.

Install PHP

```sudo apt-get install php7.0 libapache2-mod-php7.0 php7.0-cli php7.0-common php7.0-mysql```

Install PHPMyAdmin

```sudo apt-get install phpmyadmin```

During install select apache2, choose yes, and then enter a password for PHPMyAdmin.

Restart the server.

```sudo shutdown -r now```

If you connect to the IP of the server with a web browser, you should see the default Apache page.

Create link for PHPMyAdmin

Run

```cd /var/www```

Then run

```sudo ln -s /usr/share/phpmyadmin /var/www/html/```

Open SERVER_IP/phpmyadmin and login (username is root).

On the left hand side click 'New' to create a new database. Name it ```auction``` and click create. Next click Import near the top right. Choose the SQL template file or a SQL file if you are importing from a previous installation. Then click 'Go'.

Next open up shell. Run the following commands:

```
cd /var/www/html
sudo rm -r index.html
sudo git clone https://github.com/gemivnet/W1GIV-Auctions.git
sudo mv W1GIV-Auctions/website/* .
sudo rm -rf W1GIV-Auctions/
sudo nano scripts/connect.php
```

Set the username (probably root) and password of the MySQL database. Then press Ctrl-X, 'Y' and 'Enter'. 
