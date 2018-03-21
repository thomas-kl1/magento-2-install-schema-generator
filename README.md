# Install Schema Generator

**An extension for Magento 2 by Blackbird Agency**

## Synopsis

This project is a developer tool destined to speed up the tables creation's scripts of a Magento 2 module.
The purpose of this project is to make easier to create extra tables for your Magento 2 modules.
For example, you love the Phpmyadmin UI and have designed your tables with it. But now you have to write the entire 
setup script for your Magento 2 module... If only you were allowed to generate this setup script from your tables...
And here we are! That's why we offer you this module: it allows you to generate your InstallSchema.php setup file 
throught your database tables.

## How to use it

Requirements:

- You should have initialized a databasse and created your table(s)

You can generate the setup file via two methods:

- CLI command
- UI Backend

### CLI Command

Allowed CLI commands:

php magento isg:generate [tables...]

options:

-n : custom namespace name for the file
-l : location where to generate the file

### UI Backend

- Connect to your Magento 2 admin panel, then go to System => Install Schema Generator
- Insert your custom namespace
- Select the tables to generate into a InstallSchema.php setup file
- Download your file and enjoy it

![alt tag](https://black.bird.eu/media/wysiwyg/images/screen_backend_isg.jpg)

## Installation

Run the following command in your Magento 2 root path:

```
composer require blackbird/installschemagenerator
```

```
php bin/magento setup:upgrade
```

## Contributors

Thomas Klein ([Blackbird](https://black.bird.eu) team member)

Feel free to contribute to the project: many issues exists and the generation is not always correct. Also, some part should be refactored to be more compliant with Magento 2 rules.

## License

[MIT License](LICENSE)
