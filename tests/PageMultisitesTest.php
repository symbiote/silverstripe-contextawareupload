<?php

namespace Symbiote\ContextAwareUpload\Tests;

use Page;
use Symbiote\Multisites\Model\Site;
use SilverStripe\Core\Config\Config;
use Symbiote\SortableMenu\SortableMenuExtension;
use SilverStripe\Dev\FunctionalTest;

class PageMultisitesTest extends FunctionalTest
{
    protected static $use_draft_site = true;

    protected $usesDatabase = true;

    public function testCreationOfFolder()
    {
        $this->logInWithPermission('ADMIN');

        //
        $site = new Site();
        $site->Title = 'Test Site';
        $site->write();
        $site->doPublish();

        $record = new PageWithUploadField();
        $record->Title = 'Test Page';
        $record->SiteID = $site->ID;
        $record->ParentID = $site->ID;
        $record->write();
        $record->doPublish();

        $pageID = $record->ID;

        $oldFolderCount = Folder::count();
        $response = $this->get('admin/pages/edit/show/' . $pageID);

        // NOTE(Jake): 2018-08-16
        //
        // Simply visiting a page with an UploadField configured to have certain
        // folders will trigger the creation of those folders.
        //
        $this->assertEquals(
            $newFolderCount,
            $oldFolderCount + 2,
            "PageWithUploadField should create an UploadField. That UploadField should call getFolderID() whcih in-turn calls Folder::find_or_make(). It seems it didn't automatically create the folder structure based on the Page hierarchy as expected. Did something change in framework/cms?"
        );

        // Check that the GridFields have been created in the CMS
        $this->assertContains('<input type="hidden" name="ShowInFooter[GridState]" ', $response->getBody());
        $this->assertContains('<input type="hidden" name="ShowInSidebar[GridState]" ', $response->getBody());
    }

    public function testUploadingWYSIWYG()
    {

    }

    /**
     * Taken from "framework\tests\view\SSViewerTest.php"
     */
    private function assertEqualIgnoringWhitespace($a, $b, $message = '')
    {
        $this->assertEquals(preg_replace('/\s/', '', $a), preg_replace('/\s/', '', $b), $message);
    }
}
