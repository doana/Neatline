/*
 * Item filter dropdown in the Neatline editor.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

(function($, undefined) {


    $.widget('neatline.itemfilter', {

        options: {

            // Markup hooks.
            container_id: 'item-browser',
            tab_li_id: 'filter-items-tab',

            // Durations and CSS constants.
            bottom_padding: 30,
            fade_duration: 300,

            // Hexes.
            colors: {
            }

        },

        _create: function() {

            // Getters.
            this._window = $(window);
            this._body = $('body');
            this.container = $('#' + this.options.container_id);
            this.tab = $('#' + this.options.tab_li_id);

            // Measure.
            this._getDimensions();

            // Bind events to the tab.
            this._glossTab();

        },

        _getDimensions: function() {

            // Get total height of stack.
            this.totalHeight = this.element.height();

            // Get static css.
            this.topOffset = this.element.css('top');

        },

        _glossTab: function() {

            var self = this;

            this.tab.bind({

                'mouseenter': function() {
                    self.show();
                },

                'mouseleave': function() {
                    self.hide();
                }

            });

        },

        show: function() {

            // Get the current window height.
            var windowHeight = this._window.height();

            // Calculate the maximum height given the current size
            // of the window.
            var maxHeight = windowHeight - this.topOffset -
                this.options.bottom_padding;

            // Set the height based on the amount of space available.
            var height = (maxHeight < this.totalHeight) ? maxHeight :
                this.totalHeight;

            // Show and animate.
            this.element.css({
                'display': 'block',
                'height': 0
            }).animate({
                'height': height
            }, this.options.fade_duration);

            // this._addScrollbar();

        },

        hide: function() {

            var self = this;

            // Hide.
            this.element.animate({
                'height': 0
            }, this.options.fade_duration, function() {
                self.element.css('display', 'none');
            });

        },

        _addScrollbar: function() {

            console.log('scrollbar');

        }

    });


})( jQuery );
