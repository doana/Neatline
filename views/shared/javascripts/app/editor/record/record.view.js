
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Record form.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Neatline.module('Editor.Record', function(
  Record, Neatline, Backbone, Marionette, $, _) {


  Record.View = Backbone.Neatline.View.extend({


    template:   '#record-form-template',
    className:  'form-stacked record',
    tagName:    'form',

    events: {
      'click a[name="close"]':    'close',
      'click a[name="save"]':     'save',
      'click a[name="delete2"]':  'remove',
      'shown ul.nav a':           'onTabChange',
      'change div.spatial input': 'onEditControlChange',
      'keyup div.spatial input':  'onEditControlChange'
    },

    selectors: {
      mode:   'input[name="mode"]',
      modify: 'input[name="modify"]'
    },

    ui: {
      tabs:         'ul.nav a',
      modal:        '#delete-modal',
      text: {
        tab:        'a[href="#record-form-text"]',
        region:     '#record-form-text'
      },
      spatial: {
        pan:        'input[value="pan"]',
        sides:      'input[name="sides"]',
        snap:       'input[name="snap"]',
        irreg:      'input[name="irreg"]'
      }
    },


    /**
     * Initialize state, render template.
     */
    initialize: function() {

      this.open = false;  // True when the form is displayed.
      this.hash = null;   // The `href` of the active tab.

      this.getTemplate();
      this.getUi();
      this.resetTabs();

    },


    /**
     * Show the form.
     *
     * @param {Object} model: A record model.
     */
    show: function(model) {

      // Activate map editing, bind model to form.
      Neatline.execute('editor:map:startEdit', model);
      rivets.bind(this.$el, { record: model });

      this.setBubbleStatus();
      this.resetEditMode();

      this.model = model;
      this.open  = true;

    },


    /**
     * Close the form.
     */
    close: function() {
      Neatline.execute('editor:showRecordList');
      this.deactivate();
    },


    /**
     * End the map edit session, reset the bubble.
     */
    deactivate: function() {

      // Deactivate map editing.
      Neatline.execute('editor:map:endEdit', this.model);

      // Close and activate the bubble.
      Neatline.execute('bubble:activate');
      Neatline.execute('bubble:unselect');

      this.model = null;
      this.open  = false;

    },


    /**
     * Save the record.
     */
    save: function() {
      this.model.save(null, {
        success:  _.bind(this.onSaveSuccess, this),
        error:    _.bind(this.onSaveError, this)
      });
    },


    /**
     * Delete the record.
     */
    remove: function() {
      this.model.destroy({
        success:  _.bind(this.onDeleteSuccess, this),
        error:    _.bind(this.onDeleteError, this),
      });
    },


    /**
     * When a save succeeds.
     */
    onSaveSuccess: function() {

      // Update the route.
      Neatline.execute('editor:updateRoute',
        'records/'+this.model.get('id')
      );

      // Flash success.
      Neatline.execute('editor:notifySuccess',
        STRINGS.record.save.success
      );

    },


    /**
     * When a save fails.
     */
    onSaveError: function() {
      Neatline.execute('editor:notifyError',
        STRINGS.record.save.error
      );
    },


    /**
     * When a delete succeeds.
     */
    onDeleteSuccess: function() {

      // Delete the record's layer on the map.
      Neatline.execute('editor:map:deleteLayer', this.model);
      this.__ui.modal.modal('hide');

      // FLash success.
      Neatline.execute('editor:notifySuccess',
        STRINGS.record.delete.success
      );

      this.close();

    },


    /**
     * When a delete fails.
     */
    onDeleteError: function() {
      Neatline.execute('editor:notifyError',
        STRINGS.record.delete.error
      );
    },


    /**
     * When the edit controls are changed, publish the current settings.
     */
    onEditControlChange: function() {
      Neatline.execute('editor:map:updateEdit', {
        mode:   this.getEditMode(),
        modify: this.getModifyOptions(),
        poly:   this.getPolyOptions()
      });
    },


    /**
     * Cache the current tab hash, (de)activate the bubble.
     *
     * @param {Object} event: The `shown` event.
     */
    onTabChange: function(event) {
      this.hash = event.target.hash;
      this.setBubbleStatus();
      this.resetEditMode();
    },


    /**
     * Deactivate the bubble when the "Spatial" tab is active.
     */
    setBubbleStatus: function() {
      Neatline.execute(this.spatialTabActive() ?
        'bubble:deactivate' :
        'bubble:activate'
      );
    },


    /**
     * Is the "Spatial" tab activated?
     *
     * @return {Boolean}: True if "Spatial" is active.
     */
    spatialTabActive: function() {
      return this.hash == '#record-form-spatial';
    },


    /**
     * Activate the "Text" tab.
     */
    resetTabs: function() {
      this.__ui.text.region.addClass('active');
      this.__ui.text.tab.tab('show');
    },


    /**
     * Reset the map edit mode to "Navigate".
     */
    resetEditMode: function() {
      this.__ui.spatial.pan[0].checked = true;
      this.__ui.spatial.pan.trigger('change');
    },


    /**
     * Get the map edit mode.
     *
     * @return {String}: pan|point|line|poly|regPoly|modify|remove.
     */
    getEditMode: function() {
      return $(this.selectors.mode+':checked').val();
    },


    /**
     * Get the "Modify Shape" checkboxes.
     *
     * @return {Array}: 0-3 strings: rotate|resize|drag.
     */
    getModifyOptions: function() {
      var inputs = $(this.selectors.modify+':checked');
      return _.map(inputs, function(i) { return $(i).val(); });
    },


    /**
     * Get the "Draw Regular Polygon" settings.
     *
     * @return {Object}: {sides,snap,irreg}.
     */
    getPolyOptions: function() {
      return {
        sides:  this.__ui.spatial.sides.val(),
        snap:   this.__ui.spatial.snap.val(),
        irreg:  this.__ui.spatial.irreg.is(':checked')
      };
    }


  });


});
