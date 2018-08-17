# Context Aware Upload

[![Build Status](https://travis-ci.org/silbinarywolf/silverstripe-contextawareupload.svg?branch=master)](https://travis-ci.org/silbinarywolf/silverstripe-contextawareupload)
[![Latest Stable Version](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/version.svg)](https://github.com/silbinarywolf/silverstripe-contextawareupload/releases)
[![Latest Unstable Version](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/v/unstable.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-contextawareupload)
[![Total Downloads](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/downloads.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-contextawareupload)
[![License](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/license.svg)](https://github.com/silbinarywolf/silverstripe-contextawareupload/blob/master/LICENSE.md)

This module will make the default upload folder match the URL structure of the page you're uploading in, rather than just uploading
to the `Uploads` folder or the `default-site` folder if you're using Multisites.

ie. If you upload a file to a page that can be visited at http://www.mysite.com/information/about-us, then that file will be available at http://www.mysite.com/assets/information/about-us/filename.jpg by default, rather than http://www.mysite.com/assets/Uploads.

## Composer Install

```
composer require silbinarywolf/silverstripe-contextawareupload:~1.0
```

## Requirements

* SilverStripe 4.0+
* (Optional) Multisites

## Documentation

* [Quick Start](docs/en/quick-start.md)
* [License](LICENSE.md)
* [Contributing](CONTRIBUTING.md)

## Credits (OPTIONAL)

* [Jake Bentvelzen](https://github.com/SilbinaryWolf) for the initial build.
