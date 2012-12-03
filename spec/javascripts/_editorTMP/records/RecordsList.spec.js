
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Record browser tests.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

describe('Records List', function() {

  var recordRows;

  // Get fixtures.
  beforeEach(function() {

    _t.loadEditor();

    // Get record rows.
    recordRows = _t.getRecordRows();

  });

  it('should list records', function() {

    // --------------------------------------------------------------------
    // When the editor application starts, the record listings should be
    // rendered in the left editing pane.
    // --------------------------------------------------------------------

    expect(recordRows.length).toEqual(3);
    expect($(recordRows[0]).text()).toEqual('Record 1');
    expect($(recordRows[1]).text()).toEqual('Record 2');
    expect($(recordRows[2]).text()).toEqual('Record 3');

  });

});