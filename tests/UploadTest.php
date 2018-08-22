<?php

namespace Symbiote\ContextAwareUpload\Tests;

use Page;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Control\Director;
use SilverStripe\Control\Session;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Upload_Validator;
use Silverstripe\Assets\Dev\TestAssetStore;
use SilverStripe\Security\SecurityToken;
use Symbiote\ContextAwareUpload\ForceRootToDefaultExtension;

class UploadTest extends FunctionalTest
{
    protected static $use_draft_site = true;

    protected static $extra_dataobjects = [
        PageWithUploadField::class
    ];

    protected $usesDatabase = true;

    /**
     * @var Session
     */
    private $session = null;

    public function setUp()
    {
        parent::setUp();

        File::remove_extension(ForceRootToDefaultExtension::class);

        TestAssetStore::activate('AssetAdminTest');
        $memberID = $this->logInWithPermission('ADMIN');
        $this->session = new Session([ 'loggedInAs' => $memberID ]);

        // Override FunctionalTest defaults
        SecurityToken::enable();
        $this->session->set('SecurityID', SecurityToken::inst()->getValue());

        // Disable is_uploaded_file() in tests
        Upload_Validator::config()->set('use_is_uploaded_file', false);
    }

    public function tearDown()
    {
        TestAssetStore::reset();
        parent::tearDown();
    }

    public function testCreationOfFolder()
    {
        //
        $parentRecord = new PageWithUploadField();
        $parentRecord->Title = 'Top Level';
        $parentRecord->write();
        $parentRecord->doPublish();

        $record = new PageWithUploadField();
        $record->Title = 'Test Page';
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

    public function testUploadingWYSIWYGWithForceRootToDefault()
    {
        File::add_extension(ForceRootToDefaultExtension::class);

        $record = new PageWithUploadField();
        $record->Title = 'Test Empty Page';
        $record->write();
        $record->doPublish();
        $pageID = $record->ID;

        // NOTE(Jake): 2018-08-22
        //
        // We need to hit the page we're editing first
        // so that 'currentPage' can be put into the session.
        //
        // It's later read from Session data in ForceRootToDefaultExtension.
        //
        $response = Director::test(
            'admin/pages/edit/show/' . $pageID,
            [],
            $this->session,
            'GET'
        );
        $this->assertFalse($response->isError());

        // Upload file
        $fileData = array('Upload' => $this->getUploadFile('Upload', 'testItCreatesFile.txt'));
        $_FILES = $fileData;
        $postedData = array_merge(
            $fileData,
            [
                'ParentID' => 0,
                'SecurityID' => SecurityToken::inst()->getValue(),
            ]
        );
        $response = Director::test(
            'admin/assets/api/createFile',
            $postedData,
            $this->session,
            'POST'
        );
        $this->assertFalse($response->isError(), 'Response: '.$response->getBody());

        // Get file ID from response and confirm its using the page context
        // as the parent folder name.
        $responseData = json_decode($response->getBody(), true);
        $newFile = File::get()->byID($responseData[0]['id']);
        $this->assertNotNull($newFile);
        $this->assertNotEquals(0, $newFile->ParentID);
        $this->assertEquals('test-empty-page', $newFile->Parent()->Name);
    }

    /**
     * Taken from "vendor/silverstripe/asset-admin/tests/php/Controller/AssetAdminTest.php"
     */
    private function getUploadFile($paramName, $tmpFileName = 'AssetAdminTest.txt')
    {
        $tmpFilePath = TEMP_PATH . DIRECTORY_SEPARATOR . $tmpFileName;
        $tmpFileContent = '';
        for ($i = 0; $i < 10000; $i++) {
            $tmpFileContent .= '0';
        }
        file_put_contents($tmpFilePath, $tmpFileContent);
        // emulates the $_FILES array
        return array(
            'name' => $tmpFileName,
            'type' => 'text/plaintext',
            'size' => filesize($tmpFilePath),
            'tmp_name' => $tmpFilePath,
            'error' => UPLOAD_ERR_OK,
        );
    }
}
