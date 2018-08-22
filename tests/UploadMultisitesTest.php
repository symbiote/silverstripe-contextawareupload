<?php

namespace Symbiote\ContextAwareUpload\Tests;

use Page;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Control\Director;
use SilverStripe\Assets\Folder;
use Symbiote\Multisites\Model\Site;

class UploadMultisitesTest extends FunctionalTest
{
    protected static $use_draft_site = true;

    protected static $extra_dataobjects = [
        PageWithUploadField::class
    ];

    protected $usesDatabase = true;

    public function setUp()
    {
        parent::setUp();
        // NOTE(Jake): 2018-08-09
        //
        // If we can't find "Site" class, skip all tests
        // in this class.
        //
        if (!class_exists(Site::class)) {
            $this->markTestSkipped(sprintf('Skipping %s ', static::class));
            return;
        }
    }

    public function testCreationOfFolder()
    {
        $this->logInWithPermission('ADMIN');

        //
        $parentRecord = new Site();
        $parentRecord->Title = 'Test Site';
        $parentRecord->write();
        $parentRecord->doPublish();

        $record = new PageWithUploadField();
        $record->Title = 'Test Page';
        $record->SiteID = $parentRecord->ID;
        $record->ParentID = $parentRecord->ID;
        $record->write();
        $record->doPublish();

        $pageID = $record->ID;

        $oldFolderCount = Folder::get()->count();
        $response = $this->get('admin/pages/edit/show/' . $pageID);

        // NOTE(Jake): 2018-08-16
        //
        // Simply visiting a page with an UploadField configured to have certain
        // folders will trigger the creation of those folders.
        //
        $this->assertEquals(
            Folder::get()->count(),
            $oldFolderCount + 2,
            "PageWithUploadField should create an UploadField. That UploadField should call getFolderID() which in-turn calls Folder::find_or_make(). It seems it didn't automatically create the folder structure based on the Page hierarchy as expected. Did something change in framework/cms?"
        );
    }
}
