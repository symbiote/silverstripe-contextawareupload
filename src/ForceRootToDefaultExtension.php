<?php

namespace Symbiote\ContextAwareUpload;

use SilverStripe\Assets\Upload;
use SilverStripe\ORM\DataExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Controller;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Assets\Folder;

//
// NOTE(Jake): 2018-08-22
//
// This is an optional workaround for this Github Issue:
// - https://github.com/silverstripe/silverstripe-assets/issues/159
//
// For more information on use, check the docs/en/quick-start.md documentation.
//
class ForceRootToDefaultExtension extends DataExtension
{
    public function onBeforeWrite()
    {
        $record = $this->getOwner();
        if (!$record->ParentID) {
            if (Controller::has_curr()) {
                // If we're in the AssetAdmin
                // (ie. the folder view you see when you click "Insert Media" on a WYSIWYG field )
                $controller = Controller::curr();
                if ($controller instanceof AssetAdmin) {
                    // NOTE(Jake): 2018-08-22
                    //
                    // Figure out the page we're currently via the session.
                    //
                    // Ideally it wouldn't be the session as if someone has two tabs open,
                    // this could put the file in an unexpected location.
                    //
                    $session = $controller->getRequest()->getSession();
                    $data = $session->getAll();
                    if (isset($data[CMSMain::class]['currentPage']) &&
                        $data[CMSMain::class]['currentPage']) {
                        $currentPageID = $data[CMSMain::class]['currentPage'];

                        $page = SiteTree::get()->byID($currentPageID);

                        if ($page) {
                            Injector::inst()->get(ContextAwareUploadService::class)->setDefaultUploadFolderFromPage($page);
                            $folderName = Upload::config()->uploads_folder;
                            if ($folderName) {
                                $folder = Folder::find_or_make($folderName);
                                if ($folder) {
                                    $record->ParentID = $folder->ID;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
