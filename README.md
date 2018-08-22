# Context Aware Upload

[![Build Status](https://travis-ci.org/silbinarywolf/silverstripe-contextawareupload.svg?branch=master)](https://travis-ci.org/silbinarywolf/silverstripe-contextawareupload)
[![Latest Stable Version](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/version.svg)](https://github.com/silbinarywolf/silverstripe-contextawareupload/releases)
[![Latest Unstable Version](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/v/unstable.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-contextawareupload)
[![Total Downloads](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/downloads.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-contextawareupload)
[![License](https://poser.pugx.org/silbinarywolf/silverstripe-contextawareupload/license.svg)](https://github.com/silbinarywolf/silverstripe-contextawareupload/blob/master/LICENSE.md)

**This is not stable. This will most likely have its Git history re-written and be tagged 1.0.0 by the 26th August 2018**

This module will make the default upload folder match the URL structure of the page you're uploading in, rather than just uploading
to the `Uploads` folder or the `default-site` folder if you're using Multisites.

ie. If you upload a file to a page that can be visited at http://www.mysite.com/information/about-us, then that file will be available at http://www.mysite.com/assets/information/about-us/filename.jpg by default, rather than http://www.mysite.com/assets/Uploads.

## Composer Install

```
composer require silbinarywolf/silverstripe-contextawareupload:~1.0
```

## Requirements

* SilverStripe 4.1+
* (Optional) Multisites

## Documentation

* [Quick Start](docs/en/quick-start.md)
* [License](LICENSE.md)
* [Contributing](CONTRIBUTING.md)

## Known limitations

Uploading files via a WYSIWYG field's "Insert Media" button or anything else using the AssetAdmin view will not be affected by this module. I'm hoping this issue will be fixed in the future, and it's been logged here: https://github.com/silverstripe/silverstripe-assets/issues/159

In the meantime however, you can optionally apply an extension so that any files uploaded to the root folder of assets, gets moved into the desired folder.
ie. if File::ParentID == 0, move to default folder.
```yml
SilverStripe\Assets\File:
  extensions:
    - Symbiote\ContextAwareUpload\ForceRootToDefaultExtension
```

## Credits

* [Jake Bentvelzen](https://github.com/SilbinaryWolf) for the initial build.
