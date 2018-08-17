<?php

namespace Symbiote\ContextAwareUpload\Tests;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Dev\TestOnly;

class PageWithUploadField extends SiteTree implements TestOnly
{
    private static $has_one = [
        'MyUploadFieldTest' => Image::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Images', (new UploadField('MyUploadFieldTest', 'MyUploadFieldTest'))
            ->setAllowedMaxFileNumber(1)
            ->setDescription('The image displayed when a user shares the page on social media.')
        );
        return $fields;
    }
}
