# Quick Start

1. Install via composer and run dev/build.

2. Now any UploadField that hasn't explicitly called `setFolderName()` will be affected by this module. If you need to reset an UploadField's folder name you must call `UploadField::setFolderName(false)` to that it will fallback to the default upload path, at least as of [SilverStripe 4.1](https://github.com/silverstripe/silverstripe-framework/blob/d5e2d3fa67acabe63cb71a00f901759a9361718f/src/Forms/UploadReceiver.php#L159).

3. Check "Known limitations" on the main README.md and ensure you don't need to follow those steps for your use-cases.
