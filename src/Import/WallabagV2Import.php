<?php

namespace Universal Reading\Import;

class UniversalReadingV2Import extends Universal ReadingImport
{
    public function getName()
    {
        return 'universalreading v2';
    }

    public function getUrl()
    {
        return 'import_universalreading_v2';
    }

    public function getDescription()
    {
        return 'import.universalreading_v2.description';
    }

    protected function prepareEntry($entry = [])
    {
        return [
            'html' => $entry['content'],
            'headers' => [
                'content-type' => $entry['mimetype'],
            ],
            'is_archived' => (bool) ($entry['is_archived'] || $this->markAsRead),
            'is_starred' => (bool) $entry['is_starred'],
        ] + $entry;
    }

    protected function setEntryAsRead(array $importedEntry)
    {
        $importedEntry['is_archived'] = 1;

        return $importedEntry;
    }
}
