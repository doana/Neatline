
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Tests for the Google layer handler.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

describe('Google Base Layer', function() {


  var layer;


  beforeEach(function() {
    _t.loadNeatline();
    _t.mockGoogleApi();
  });


  afterEach(function() {
    expect(layer.CLASS_NAME).toEqual('OpenLayers.Layer.Google');
    expect(layer.name).toEqual('Title');
  });


  it('should construct a `physical` layer', function() {

    layer = Neatline.request('LAYERS:Google', {
      title: 'Title',
      properties: {
        provider: 'physical'
      }
    });

    expect(layer.type).toEqual(google.maps.MapTypeId.TERRAIN);

  });


  it('should construct a `streets` layer', function() {

    layer = Neatline.request('LAYERS:Google', {
      title: 'Title',
      properties: {
        provider: 'streets'
      }
    });

    expect(layer.type).toEqual(google.maps.MapTypeId.ROADMAP);

  });


  it('should construct a `satellite` layer', function() {

    layer = Neatline.request('LAYERS:Google', {
      title: 'Title',
      properties: {
        provider: 'satellite'
      }
    });

    expect(layer.type).toEqual(google.maps.MapTypeId.SATELLITE);

  });


  it('should construct a `hybrid` layer', function() {

    layer = Neatline.request('LAYERS:Google', {
      title: 'Title',
      properties: {
        provider: 'hybrid'
      }
    });

    expect(layer.type).toEqual(google.maps.MapTypeId.HYBRID);

  });


});
