<?php

namespace Symbiote\ContextAwareUpload;

use SilverStripe\Assets\Upload;
use SilverStripe\Core\Extension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;
use Symbiote\Multisites\Model\Site;
use Symbiote\Multisites\Multisites;

class CMSPageEditControllerExtension extends Extension
{
    public function onAfterInit()
    {
        // Set upload folder
        $controller = $this->getOwner();

        // Check if `uploads_folder` config is the default value
        $uploadFolder = Upload::config()->uploads_folder;
        $isDefaultValue = $uploadFolder === '' || $uploadFolder === 'Uploads';
        if (class_exists(Site::class)) {
            // NOTE(Jake): 2018-08-16
            //
            // Check if Multisites has fiddled with the `uploads_folder` config.
            // ie. If `Upload::config()->uploads_folder` matches the current Site::Folder()
            //     then technically this is still the "default value"
            //
            // Source: vendor/symbiote/silverstripe-multisites/src/Extension/MultisitesControllerExtension.php
            //
            $site = Multisites::inst()->getCurrentSite();
            if ($site) {
                $defaultFolder = $site->Folder();
                if ($defaultFolder) {
                    $isDefaultValue = $isDefaultValue || $uploadFolder === $defaultFolder->Name;
                }
            }
        }

        // Only change the uploads folder if core or Multisites has touched it
        if ($isDefaultValue) {
            $record = $controller->getRecord($controller->currentPageID());
            if ($record &&
                $record instanceof SiteTree) {
                Injector::inst()->get(ContextAwareUploadService::class)->setDefaultUploadFolderFromPage($record);
            }
        }
    }
}
