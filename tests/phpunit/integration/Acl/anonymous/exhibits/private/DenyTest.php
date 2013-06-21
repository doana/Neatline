<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class AclTest_AnonymousExhibitsPrivateDeny extends Neatline_Case_Default
{


    protected $_isAdminTest = false;


    public function setUp()
    {
        parent::setUp();
        $this->exhibit = $this->__exhibit('slug', false);
        $this->logout();
    }


    /**
     * Anonymous users should not be able to view private exhibits.
     */
    public function testCannotViewPrivateExhibits()
    {
        $this->expect404();
        $this->dispatch('neatline/show/slug');
    }


    /**
     * Anonymous users should not be able to browse private exhibits.
     */
    public function testCannotBrowsePrivateExhibits()
    {

        $this->dispatch('neatline');

        // Should not list private exhibit.
        $this->assertNotXpath('//a[@class="neatline"][@href="'.
            public_url('neatline/show/slug').'"]'
        );
    }


}