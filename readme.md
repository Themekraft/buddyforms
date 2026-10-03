BuddyForms
==========

[ ![Codeship Status for BuddyForms/BuddyForms](https://app.codeship.com/projects/7bfa4830-793a-0136-7943-1a1745bf82cc/status?branch=master)](https://app.codeship.com/projects/300501)

BuddyForms is a commercial plugin available from [BuddyForms](https://themekraft.com/buddyforms). The plugin is hosted here on a public Github repository in order to better facilitate community contributions from developers and users alike. If you have a suggestion, a bug report, or a patch for an issue, feel free to submit it here. We do ask, however, that if you are using the plugin on a live site that you please purchase a valid license from the [website](https://themekraft.com/buddyforms). We cannot provide support to anyone that does not hold a valid license key.

## Versioning
We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/gfirem/akamai-release-node/tags). 


## Dependencies Manager 
We are using `bamarni/composer-bin-plugin` to scope the dependencies and avoid collisions with 3rd party plugins.
Any production library need to be scoped using the next command.
Example of command:
- `composer bin buddyforms [composer-command]`

## Important
If you want to use the last version of BuddyForms from the develop branch you need to install composer and run the next command from the plugin folder 
* `composer install --no-dev`

# Setup for development
If you want install buddyforms in your local for testing or develop. You need to read carefully the next sections.

### Requirements
- PHP 7
- WordPress
- Docker & Docker Composer

### Installation

* Composer
  * `composer install`
* If you need the TK Script submodule
  * `git submodule update --init --recursive`
  
#### Troubleshooting
If you face composer memory problems like in the next line.

> `PHP Fatal error: Allowed memory size of XXXXXX bytes exhausted <...>`

Use the command

> `php -d memory_limit=-1 <composer path> <...>`
> `php -d memory_limit=-1 /usr/local/bin/composer update`

Source: [https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors](https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors) 

## Testing
We use [codeception](https://codeception.com/) and webdriver.

#### First Steps
To configure all the needed code to run in your environment you need to follow the next steps:
- Run composer update to get the dependencies
- Create a copy of `.env.testing.example` as `.env.testing`
- Create the database for testing with the name `tests`
- Run Chromedriver in the default port with the base url `/wd/hub`
- Update the file `.env.testing` with the correct information of your environment
- Update your wp-config.php to point to the correct database when the test is running
- Run your first acceptance test local using composer `codecept`. Example command `vendor/bin/codecept run tests/acceptance/testFieldLabelsCest.php --steps`

#### Related commands
* Run chromedriver before start executing the test 
    * `vendor/bin/chromedriver --url-base=/wd/hub`
* Generate Class Test file
    * `vendor/bin/codecept g:cest acceptance <testName>`
* To run all the acceptance test from command line with steps
    * `vendor/bin/codecept run tests/acceptance/SiteNameCest.php --steps`
* To run specific file test from command line with steps
    * `vendor/bin/codecept run <path to the file> --steps`
* To replace the old url for a new one inside the dump.sql
    * `sed 's/buddyformsautomation.local/buddyforms.local/g' tests/_data/dump.sql`

## Contributors
* [Sven Lehnert](https://github.com/svenl77)
* [Konrad Sroka](https://github.com/konradS)
* [Guillermo Figueroa](https://github.com/gfirem)
* [Victor Marin](https://github.com/marin250189)
* [Camilo Luna](https://github.com/camilolunacom)

## License

This project is licensed under the GPLv2 or later license - see the [license.txt](LICENSE) file for details.
