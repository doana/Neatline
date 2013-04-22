<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class FixturesTest_SearchInit extends Neatline_FixtureCase
{


    /**
     * `SearchInit.records.json`
     */
    public function testSearchInit()
    {

        $record = $this->__record($this->exhibit);
        $record->title = 'title';
        $record->save();

        $this->writeFixtureFromRoute('neatline/records',
            'SearchInit.records.json'
        );

    }


}
