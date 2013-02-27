
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Spatial tab form.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Neatline.module('Editor.Record.Style', { startWithParent: false,
  define: function(Style, Neatline, Backbone, Marionette, $, _) {


  Style.View = Backbone.Neatline.View.extend({

    events: {

      // Set map-derived styles.
      'click a[name="set-min-zoom"]':   'onSetMinZoom',
      'click a[name="set-max-zoom"]':   'onSetMaxZoom',
      'click a[name="set-focus"]':      'onSetFocus',

      // Preview styles.
      'change input.preview':           'onStyleChange',
      'keyup input.preview':            'onStyleKeyup',

      // Tab changes.
      'shown ul.nav a':                 'buildUi'

    },

    ui: {
      style: {
        minZoom:  'input[name="min-zoom"]',
        maxZoom:  'input[name="max-zoom"]',
        mapFocus: 'input[name="map-focus"]',
        mapZoom:  'input[name="map-zoom"]'
      }
    },


    /**
     * Get inputs.
     */
    initialize: function() {
      this.getUi();
    },


    /**
     * Instantiate color pickers and draggers.
     */
    buildUi: function() {

      // INTEGERS
      this.$('input.integer').draggableInput({
        type: 'integer', min: 0, max: 1000
      });

      // OPACITIES
      this.$('input.opacity').draggableInput({
        type: 'integer', min: 0, max: 100
      });

    },


    /**
     * Populate "Min Zoom" with current map value.
     */
    onSetMinZoom: function() {
      var zoom = Neatline.request('MAP:getZoom');
      this.__ui.style.minZoom.val(zoom).change();
    },


    /**
     * Populate "Max Zoom" with current map value.
     */
    onSetMaxZoom: function() {
      var zoom = Neatline.request('MAP:getZoom');
      this.__ui.style.maxZoom.val(zoom).change();
    },


    /**
     * Populate default focus and zoom with current map center.
     */
    onSetFocus: function() {
      var center  = Neatline.request('MAP:getCenter');
      var zoom    = Neatline.request('MAP:getZoom');
      this.__ui.style.mapFocus.val(center.lon+','+center.lat).change();
      this.__ui.style.mapZoom.val(zoom).change();
    },


    /**
     * Forward `keyup` events to `change` to trigger a model bind.
     *
     * @param {Object} e: The keyup event.
     */
    onStyleKeyup: function(e) {
      $(e.target).trigger('change');
    },


    /**
     * Preview new style settings on the map edit layer.
     */
    onStyleChange: function() {
      Neatline.execute(
        'MAPEDIT:updateStyles', Neatline.request('RECORD:getModel')
      );
    }


  });


}});
