# Install Schema Generator

**An extension for Magento 2**

## Synopsis

This project is a developer tool destined to speed up the tables creation's scripts of a Magento 2 module.

## How to use it

You've just to create your table(s) through your favorite UI SQL manager (like PhpMyAdmin) with all requirements your module need, and select it (them) with ISG to generate the PHP script for your module (InstallSchema.php or UpgradeSchema.php)

## Installation

Including this dependency in your Magento project is the more convenient way to integrate ISG.

In order to be able to install it, you'll need to be sure that your root composer.json file contains a reference to the bitbucket repository.  To do so you'll need to add the following to `composer.json`:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://bitbucket.org/blackbirdagency/magento2-extensions-isgenerator/"
        }
    ]
```

The above can also be added via the composer cli with the command: 

    composer config repositories.blackbird_isg vcs https://bitbucket.org/blackbirdagency/magento2-extensions-isgenerator/


Once the repository added, run the two following commands:

    composer require blackbird/installschemagenerator
    php bin/magento setup:upgrade

## Usage

You can access to the extension by the following access menu : "SYSTEM" => "Install Schema Generator".

Select the table(s) and click on "Generate and download file" button to generate the InstallSchema.php file.

![alt tag](https://black.bird.eu/media/wysiwyg/images/screen_backend_isg.jpg)

## Contributors

Thomas Klein ([Blackbird](https://black.bird.eu) team member)

## License

Blackbird Policy (https://store.bird.eu/license)
