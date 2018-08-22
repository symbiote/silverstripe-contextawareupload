<?php

namespace Symbiote\ContextAwareUpload;

use SilverStripe\Assets\Upload;
use SilverStripe\CMS\Model\Page;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\CMS\Model\SiteTree;
use Symbiote\Multisites\Model\Site;

class ContextAwareUploadService
{
    use Injectable;

    /**
     * @var SiteTree
     */
    private $record;

    /**
     * @return void
     */
    public function setDefaultUploadFolderFromPage(SiteTree $record)
    {
        if ($this->record !== $record) {
            $newUploadsFolder = $this->determineFolderPathFromPage($record);
            if ($newUploadsFolder) {
                Upload::config()->uploads_folder = $newUploadsFolder;
                $this->record = $record;
            }
        }
    }

    /**
     * @return string Return blank string if an error occurred
     */
    private function determineFolderPathFromPage(SiteTree $record)
    {
        // Get parts from URLSegments.
        $parts = [];
        while ($record &&
            $record->exists()) {
            $parts[] = $record;
            $record = $record->getParent();
        }

        // Build folder path
        $folderPath = '';
        foreach (array_reverse($parts) as $i => $record) {
            $urlSegment = $record->URLSegment;
            if (!$urlSegment) {
                return '';
            }
            if ($i != 0) {
                $folderPath .= '/';
            }
            $folderPath .= $urlSegment;
        }
        return $folderPath;
    }
}
