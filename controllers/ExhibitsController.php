<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Add/Edit/Delete exhibits.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Neatline_ExhibitsController
    extends Omeka_Controller_AbstractActionController
{


    /**
     * Set default model and pagination page length.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NeatlineExhibit');
        $this->_table = $this->_helper->db->getTable('NeatlineExhibit');
        $this->_browseRecordsPerPage = get_option('per_page_admin');
    }


    /**
     * Create a new exhibit.
     */
    public function addAction()
    {

        $exhibit = new NeatlineExhibit;
        $form = $this->_getNeatlineDetailsForm($exhibit);

        // If form was submitted.
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $exhibit->saveForm($form->getValues());
                $this->_redirect('neatline');
            }
        }

        $this->view->form = $form;

    }


    /**
     * Edit exhibit settings.
     */
    public function editAction()
    {

        $exhibit = $this->_helper->db->findById();
        $form = $this->_getNeatlineDetailsForm($exhibit);

        // If form was submitted.
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $exhibit->saveForm($form->getValues());
                $this->_redirect('neatline');
            }
        }

        $this->view->neatline_exhibit = $exhibit;
        $this->view->form = $form;

    }


    /**
     * Edit exhibit.
     */
    public function editorAction()
    {
        $this->view->exhibit = $this->_helper->db->findById();
    }


    /**
     * Show exhibit.
     */
    public function showAction()
    {
        $exhibit = $this->_table->findBySlug($this->_request->slug);
        $this->view->neatline_exhibit = $exhibit;
    }


    /**
     * Sets the add success message.
     */
    protected function _getAddSuccessMessage($exhibit)
    {
        return __('The Neatline "%s" was successfully added!',
            $exhibit->title);
    }


    /**
     * Sets the edit success message.
     */
    protected function _getEditSuccessMessage($exhibit)
    {
        return __('The Neatline "%s" was successfully changed!',
            $exhibit->title);
    }


    /**
     * Sets the delete success message.
     */
    protected function _getDeleteSuccessMessage($exhibit)
    {
        return __('The Neatline "%s" was successfully deleted!',
            $exhibit->title);
    }


    /**
     * Sets the delete confirm message.
     */
    protected function _getDeleteConfirmMessage($exhibit)
    {
      return __('This will delete "%s" and its associated metadata.',
        $exhibit->title);
    }


    /**
     * Construct the details form.
     */
    private function _getNeatlineDetailsForm($exhibit)
    {
        return new Neatline_Form_NeatlineDetails(array(
            'neatline' => $exhibit
        ));
    }


}