
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2; */

/**
 * Form view.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Editor.Views.Form = Backbone.View.extend({

  getTemplate: function() {
    return _.template($('#edit-form').html());
  },

  /*
   * Render form template, get components.
   *
   * @return void.
   */
  initialize: function() {

    // Render template.
    this.form = $(this.getTemplate()());

    // UX.
    this.tabs =           this.form.find('ul.nav a');

    // Text.
    this.head =           this.form.find('h3.head');
    this.title =          this.form.find('textarea[name="title"]');
    this.body =           this.form.find('textarea[name="body"]');

    // Styles.
    this.vectorColor =    this.form.find('input[name="vector-color"]');
    this.strokeColor =    this.form.find('input[name="stroke-color"]');
    this.selectColor =    this.form.find('input[name="select-color"]');
    this.vectorOpacity =  this.form.find('input[name="vector-opacity"]');
    this.strokeOpacity =  this.form.find('input[name="stroke-opacity"]');
    this.selectOpacity =  this.form.find('input[name="select-opacity"]');
    this.graphicOpacity = this.form.find('input[name="graphic-opacity"]');
    this.strokeWidth =    this.form.find('input[name="stroke-width"]');
    this.pointRadius =    this.form.find('input[name="point-radius"]');
    this.pointGraphic =   this.form.find('input[name="point-image"]');
    this.mapFocus =       this.form.find('button[name="map-focus"]');

    // Buttons.
    this.saveButton =     this.form.find('button[name="save"]');
    this.closeButton =    this.form.find('button[name="close"]');
    this.delButton =      this.form.find('button[name="del"]');

    // Bind form listeners.
    this.bindEvents();

  },

  /*
   * Bind event listeners to form elements.
   *
   * @return void.
   */
  bindEvents: function() {

    // Close button.
    // -------------
    this.closeButton.click(_.bind(function(e) {
      e.preventDefault();
      this.close();
    }, this));

    // Save button.
    // -------------
    this.saveButton.click(_.bind(function(e) {
      e.preventDefault();
      this.save();
    }, this));

  },

  /*
   * Show the form.
   *
   * @param {Object} model: The record model.
   *
   * @return void.
   */
  show: function(model) {
    this.model = model;
    this.$el.html(this.form);
    this.render();
  },

  /*
   * Close the form.
   *
   * @return void.
   */
  close: function(model) {
    this.form.detach();
    Editor.vent.trigger('form:close');
  },

  /*
   * Render form values.
   *
   * @return void.
   */
  render: function() {

    // Activate "Text".
    $(this.tabs[0]).tab('show');

    // Text.
    this.head.            text(this.model.get('title'));
    this.title.           val(this.model.get('title'));
    this.body.            val(this.model.get('description'));

    // Styles.
    this.vectorColor.     val(this.model.get('vector_color'));
    this.strokeColor.     val(this.model.get('stroke_color'));
    this.selectColor.     val(this.model.get('select_color'));
    this.vectorOpacity.   val(this.model.get('vector_opacity'));
    this.strokeOpacity.   val(this.model.get('stroke_opacity'));
    this.selectOpacity.   val(this.model.get('select_opacity'));
    this.graphicOpacity.  val(this.model.get('graphic_opacity'));
    this.strokeWidth.     val(this.model.get('stroke_width'));
    this.pointRadius.     val(this.model.get('point_radius'));
    this.pointGraphic.    val(this.model.get('point_image'));

  },

  /*
   * Save form to record model.
   *
   * @return void.
   */
  save: function() {

    // Commit model.
    this.model.save({

      // Text.
      title:              this.title.val(),
      description:        this.body.val(),

      // Styles.
      vector_color:       this.vectorColor.val(),
      stroke_color:       this.strokeColor.val(),
      select_color:       this.selectColor.val(),
      vector_opacity:     this.vectorOpacity.val(),
      stroke_opacity:     this.strokeOpacity.val(),
      select_opacity:     this.selectOpacity.val(),
      graphic_opacity:    this.graphicOpacity.val(),
      stroke_width:       this.strokeWidth.val(),
      point_radius:       this.pointRadius.val(),
      point_image:        this.pointGraphic.val()

    });

    // Update head.
    this.head.text(this.model.get('title'));

  }

});
