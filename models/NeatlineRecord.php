<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Row class for Neatline data record.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineRecord extends Omeka_Record_AbstractRecord
{

    /**
     * The id of the parent item.
     * int(10) unsigned NULL
     */
    public $item_id;

    /**
     * The id of the parent exhibit.
     * int(10) unsigned NULL
     */
    public $exhibit_id;

    /**
     * The id of the parent record.
     * int(10) unsigned NULL
     */
    public $parent_record_id;

    /**
     * Boolean for whether to use DC output as description.
     * tinyint(1) NULL
     */
    public $use_dc_metadata;

    /**
     * Boolean for whether to show popup bubble.
     * tinyint(1) NULL
     */
    public $show_bubble;

    /**
     * The title for the record.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $title;

    /**
     * An exhibit-unique plaintext identifier for the record.
     * varchar(100) NULL
     */
    public $slug;

    /**
     * A plaintext description for the record.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $description;

    /**
     * A ISO8601 start date.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $start_date;

    /**
     * A ISO8601 end date.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $end_date;

    /**
     * A ISO8601 date for when the record should start to appear.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $start_visible_date;

    /**
     * A ISO8601 date for when the record should start to disappear.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $end_visible_date;

    /**
     * The left percent for the ambiguity gradient.
     * int(10) unsigned NULL
     */
    public $left_percent;

    /**
     * The right percent for the ambiguity gradient.
     * int(10) unsigned NULL
     */
    public $right_percent;

    /**
     * The fill color for geometries.
     * int(10) unsigned NULL
     */
    public $vector_color;

    /**
     * The line color for geometries.
     * int(10) unsigned NULL
     */
    public $stroke_color;

    /**
     * The highlight color for geometries.
     * int(10) unsigned NULL
     */
    public $highlight_color;

    /**
     * The fill opacity for geometries.
     * int(10) unsigned NULL
     */
    public $vector_opacity;

    /**
     * The selected opacity for geometries.
     * int(10) unsigned NULL
     */
    public $select_opacity;

    /**
     * The line opacity for geometries.
     * int(10) unsigned NULL
     */
    public $stroke_opacity;

    /**
     * The opacity of points rendered as images.
     * int(10) unsigned NULL
     */
    public $graphic_opacity;

    /**
     * The width of lines on geometries.
     * int(10) unsigned NULL
     */
    public $stroke_width;

    /**
     * The radius of points on geometries.
     * int(10) unsigned NULL
     */
    public $point_radius;

    /**
     * The URL for a static to represent points.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $point_image;

    /**
     * KML for geometries.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $geocoverage;

    /**
     * Default map focus position
     * varchar(100) NULL
     */
    public $map_bounds;

    /**
     * Default map zoom level.
     * int(10) unsigned NULL
     */
    public $map_zoom;

    /**
     * Boolean for whether the record is present on the map.
     * tinyint(1) NULL
     */
    public $space_active;

    /**
     * Boolean for whether the record is present on the timeline.
     * tinyint(1) NULL
     */
    public $time_active;

    /**
     * Boolean for whether the record is present on the item panel.
     * tinyint(1) NULL
     */
    public $items_active;

    /**
     * Display order for record in items panel.
     * int(10) unsigned NULL
     */
    public $display_order;

    /**
     * The record's parent record (used for caching).
     * Omeka_Record_AbstractRecord
     */
    protected $_parent;

    /**
     * The record's parent exhibit (used for caching).
     * Omeka_Record_AbstractRecord
     */
    protected $_exhibit;

    /**
     * Default attributes.
     */
    private static $defaults = array(
        'left_percent' => 0,
        'right_percent' => 100,
        'geocoverage' => ''
    );

    /**
     * Valid style attribute names.
     */
    private static $styles = array(
        'vector_color',
        'vector_opacity',
        'stroke_color',
        'stroke_opacity',
        'stroke_width',
        'select_opacity',
        'graphic_opacity',
        'point_radius',
        'highlight_color'
    );

    /**
     * DC Date regular expression.
     */
    private static $dcDateRegex =
        '/^(?P<start>[0-9:\-\s]+)(\/(?P<end>[0-9:\-\s]+))?/';


    /**
     * Instantiate and foreign keys.
     *
     * @param Omeka_record $item The item record.
     * @param Omeka_record $neatline The exhibit record.
     *
     * @return Omeka_record $this.
     */
    public function __construct($item = null, $neatline = null)
    {

        parent::__construct();

        // If defined, set the item key.
        if (!is_null($item)) {
            $this->item_id = $item->id;
        }

        // If defined, set the item key.
        if (!is_null($neatline)) {
            $this->exhibit_id = $neatline->id;
        }

        // Set defaults.
        $this->show_bubble = 1;
        $this->left_percent = 0;
        $this->right_percent = 100;
        $this->space_active = 0;
        $this->time_active = 0;
        $this->items_active = 0;

        $this->_parent = null;
        $this->_exhibit = null;

    }

    /**
     * Get the parent item record.
     *
     * @return Omeka_record $item The parent item.
     */
    public function getItem()
    {

        $item = null;

        // If record id is defined, get item.
        if (!is_null($this->item_id)) {
           $item = $this->getTable('Item')->find($this->item_id);
        }

        return $item;

    }

    /**
     * Get the parent exhibit record.
     *
     * @return Omeka_record $exhibit The parent exhibit.
     */
    public function getExhibit()
    {

        if (is_null($this->_exhibit)) {
            $this->_exhibit = $this->getTable('NeatlineExhibit')
                ->find($this->exhibit_id);
        }

        return $this->_exhibit;

    }

    /**
     * Get the parent data record.
     *
     * @return Omeka_record $record The parent record.
     */
    public function getParentRecord()
    {

        if (!is_null($this->parent_record_id) && is_null($this->_parent)) {
            $this->_parent = $this->getTable('NeatlineRecord')
                ->find($this->parent_record_id);
        }

        return $this->_parent;

    }

    /**
     * Construct a JSON representation of the attributes to be used in the
     * item edit form.
     *
     * @return JSON The data.
     */
    public function buildEditFormJson()
    {

        // Shell out the array.
        $data = array();

        // Get parent record select list.
        $_recordsTable = $this->getTable('NeatlineRecord');
        $records = $_recordsTable->getRecordsForSelect($this->getExhibit(), $this);

        // Set the array values.
        $data['title'] =                $this->getTitle();
        $data['slug'] =                 $this->getNotEmpty('slug');
        $data['description'] =          $this->getDescription();
        $data['vector_color'] =         $this->getStyle('vector_color');
        $data['stroke_color'] =         $this->getStyle('stroke_color');
        $data['highlight_color'] =      $this->getStyle('highlight_color');
        $data['vector_opacity'] =       (int) $this->getStyle('vector_opacity');
        $data['select_opacity'] =       (int) $this->getStyle('select_opacity');
        $data['stroke_opacity'] =       (int) $this->getStyle('stroke_opacity');
        $data['graphic_opacity'] =      (int) $this->getStyle('graphic_opacity');
        $data['stroke_width'] =         (int) $this->getStyle('stroke_width');
        $data['point_radius'] =         (int) $this->getStyle('point_radius');
        $data['point_image'] =          $this->getNotEmpty('point_image');
        $data['start_date'] =           (string) $this->getStartDate();
        $data['end_date'] =             (string) $this->getEndDate();
        $data['start_visible_date'] =   (string) $this->start_visible_date;
        $data['end_visible_date'] =     (string) $this->end_visible_date;
        $data['left_percent'] =         (int) $this->getLeftPercent();
        $data['right_percent'] =        (int) $this->getRightPercent();
        $data['parent_record_id'] =     $this->getParentRecordId();
        $data['use_dc_metadata'] =      $this->use_dc_metadata;
        $data['show_bubble'] =          $this->show_bubble;
        $data['geocoverage'] =          $this->getGeocoverage();
        $data['records'] =              $records;

        return $data;

    }

    /**
     * Construct a starting attribute set for an Omeka-item-based record.
     *
     * @param Omeka_record $item The item record.
     * @param Omeka_record $exhibit The exhibit record.
     *
     * @return JSON The data.
     */
    public static function buildEditFormForNewRecordJson($item, $exhibit)
    {

        // Shell out the array.
        $data = array();

        // Get parent record select list.
        $_db = get_db();
        $_recordsTable = $_db->getTable('NeatlineRecord');
        $records = $_recordsTable->getRecordsForSelect($exhibit);

        // Set the array values.
        $data['vector_color'] =         get_plugin_ini('Neatline', 'vector_color');
        $data['stroke_color'] =         get_plugin_ini('Neatline', 'stroke_color');
        $data['highlight_color'] =      get_plugin_ini('Neatline', 'highlight_color');
        $data['vector_opacity'] =       (int) get_plugin_ini('Neatline', 'vector_opacity');
        $data['select_opacity'] =       (int) get_plugin_ini('Neatline', 'select_opacity');
        $data['stroke_opacity'] =       (int) get_plugin_ini('Neatline', 'stroke_opacity');
        $data['graphic_opacity'] =      (int) get_plugin_ini('Neatline', 'graphic_opacity');
        $data['stroke_width'] =         (int) get_plugin_ini('Neatline', 'stroke_width');
        $data['point_radius'] =         (int) get_plugin_ini('Neatline', 'point_radius');
        $data['point_image'] =          '';
        $data['left_percent'] =         self::$defaults['left_percent'];
        $data['right_percent'] =        self::$defaults['right_percent'];
        $data['start_date'] =           '';
        $data['end_date'] =             '';
        $data['start_visible_date'] =   '';
        $data['end_visible_date'] =     '';
        $data['slug'] =                 '';
        $data['parent_record_id'] =     'none';
        $data['records'] =              $records;
        $data['use_dc_metadata'] =      0;
        $data['show_bubble'] =          1;

        // Get DC title default.
        $data['title'] = metadata(
            $item, array('Dublin Core', 'Title'));

        // Get DC description default.
        $data['description'] = metadata(
            $item, array('Dublin Core', 'Description'));

        // Get DC date default.
        $date = metadata(
            $item, array('Dublin Core', 'Date'));

        // Check for date format, assign pieces.
        if (preg_match(self::$dcDateRegex, $date, $matches)) {

            // Start.
            $data['start_date'] = $matches['start'];

            // End.
            if (array_key_exists('end', $matches)) {
                $data['end_date'] = $matches['end'];
            }

        }

        return $data;

    }


    /**
     * Setters.
     */


    /**
     * Set the an attribute if the passed value is not null or ''.
     *
     * @param string $attribute The name of the attribute.
     * @param boolean $value The value to set.
     *
     * @return void.
     */
    public function setNotEmpty($attribute, $value)
    {
        if ($value == '') $this[$attribute] = null;
        else $this[$attribute] = $value;
    }

    /**
     * Set the slug if it is unique.
     *
     * @param boolean $slug The slug.
     *
     * @return void.
     */
    public function setSlug($slug)
    {

        // Get records table.
        $_recordsTable = $this->getTable('NeatlineRecord');

        // Set the record value if it is unique.
        if ($_recordsTable->slugIsAvailable($this, $this->getExhibit(), $slug)) {
            $this->slug = $slug;
        }

    }

    /**
     * Set the space_active or time_active attributes. Reject non-
     * boolean parameters.
     *
     * @param string $viewport 'items', 'space', or 'time'.
     * @param boolean $value The value to set.
     *
     * @return boolean True if the set succeeds.
     */
    public function setStatus($viewport, $value)
    {

        if (!is_bool($value)) { return false; }

        // Cast the boolean to int.
        $intValue = (int) $value;

        // If items.
        if ($viewport == 'items') {
            $this->items_active = $intValue;
        }

        // If space.
        else if ($viewport == 'space') {
            $this->space_active = $intValue;
        }

        // If time.
        else if ($viewport == 'time') {
            $this->time_active = $intValue;
        }

        return true;

    }

    /**
     * Set the left_percent or right_percent attributes. Only accept integers
     * between 0 and 100, and require that the right value always be greater
     * than or equal to the left.
     *
     * @param integer $left The left-hand value.
     * @param integer $right The right-hand value.
     *
     * @return boolean True if the set succeeds.
     */
    public function setPercentages($left, $right)
    {

        if (!is_int($left) ||
            !is_int($right) ||
            !(0 <= $left && $left <= $right && $right <= 100)) {
            return false;
        }

        $this->left_percent = $left;
        $this->right_percent = $right;

        return true;

    }

    /**
     * Set the geocoverage field if the passed value is not <string>'null', which
     * is true when there was not an instantiated map when the  triggering save
     * action was performed in the editor.
     *
     * @param integer $value The value.
     *
     * @return boolean True if the set succeeds.
     */
    public function setGeocoverage($value)
    {
        if ($value == 'null') return false;
        return $this->setNotEmpty('geocoverage', $value);
    }

    /**
     * Set a style attribute. If there is an exhibit default, only set
     * if the passed value is different. If there is no exhibit default,
     * only set if the passed value is different from the system
     * default. If a non-style column name is passed, return false.
     *
     * @param string style The name of the style.
     * @param mixed $value The value to set.
     *
     * @return boolean True if the set succeeds.
     */
    public function setStyle($style, $value)
    {

        // If a non-style property is passed, return false.
        if (!in_array($style, self::$styles)) {
            return false;
        }

        // Get the exhibit.
        $exhibit = $this->getExhibit();

        // If there is a parent record.
        if (!is_null($this->parent_record_id)) {

            // If the value does not match the parent style, set.
            $parent = $this->getParentRecord();
            if ($value != $parent->getStyle($style)) {
                $this[$style] = $value;
                return true;
            }

        }

        // If there is an exhibit default.
        if (!is_null($exhibit[$style])) {

            // If the value does not match the default.
            if ($value != $exhibit[$style]) {
                $this[$style] = $value;
                return true;
            }

            // If the value matches the default and there is a non-null
            // value set on the record, null the record value.
            else if (!is_null($this[$style])) {
                $this[$style] = null;
                return true;
            }

        }

        // If the value does not match the system default.
        else if ($value != get_plugin_ini('Neatline', $style)) {
            $this[$style] = $value;
            return true;
        }

        // If the value matches the system default and there is a non-null
        // value set on the record, null the record value.
        else if (!is_null($this[$style])) {
            $this[$style] = null;
            return true;
        }

        return false;

    }

    /**
     * Set the parent record id.
     *
     * @param integer $id The id.
     *
     * @return boolean True if a new value is set.
     */
    public function setParentRecordId($id)
    {

        // Capture original value.
        $original = $this->parent_record_id;

        // If 'none' is passed, null out the key.
        if ($id == 'none') {
            $this->parent_record_id = null;
        }

        // If the id is not the self id, set.
        else if ($id != $this->id) {
            $this->parent_record_id = $id;
        }

        // Check for new value.
        return $original != $this->parent_record_id;

    }

    /**
     * Set the use_dc_metadata parameter if there is a parent item.
     *
     * @param integer $useDcMetadata 0/1.
     *
     * @return void.
     */
    public function setUseDcMetadata($useDcMetadata)
    {
        if (!is_null($this->item_id))
            $this->use_dc_metadata = (int) $useDcMetadata;
    }

    /**
     * Set all style attributes to null.
     *
     * @return void.
     */
    public function resetStyles()
    {
        $this->vector_color =       null;
        $this->stroke_color =       null;
        $this->highlight_color =    null;
        $this->vector_opacity =     null;
        $this->stroke_opacity =     null;
        $this->graphic_opacity =    null;
        $this->stroke_width =       null;
        $this->point_radius =       null;
    }


    /**
     * Getters.
     */


    /**
     * Set the an attribute if the passed value is not null or ''.
     *
     * @param string $attribute The name of the attribute.
     * @param boolean $value The value to set.
     *
     * @return void.
     */
    public function getNotEmpty($attribute)
    {
        if (is_null($this[$attribute])) return '';
        else return $this[$attribute];
    }

    /**
     * Get a style attribute. In order or priority, return the row
     * value, exhibit default, or system default.
     *
     * @param string style The name of the style.
     *
     * @return mixed The value.
     */
    public function getStyle($style)
    {

        // If there is a row value.
        if (!is_null($this[$style])) return $this[$style];

        // If there is a parent record value.
        else if (!is_null($this->parent_record_id)) {
            return $this->getParentRecord()->getStyle($style);
        }

        // If there is an exhibit default
        else {

            $exhibit = $this->getExhibit();
            if (!is_null($exhibit[$style])) {
                return $exhibit[$style];
            }

            // Fall back to system default.
            else return get_plugin_ini('Neatline', $style);

        }

    }

    /**
     * Return title.
     *
     * @return string $title The title.
     */
    public function getTitle()
    {

        // Return row-level value.
        if (!is_null($this->title)) return $this->title;

        // If there is a parent item.
        else if (!is_null($this->item_id)) {

            // Try to get DC title.
            return metadata($this->getItem(),
                array('Dublin Core', 'Title'));

        }

        else return '';

    }

    /**
     * For dropdown selects, strip HTML and truncate.
     *
     * @param integer length The max length.
     *
     * @return string $title The title.
     */
    public function getTitleForSelect($length=60)
    {

        // Get title, strip tags, truncate.
        $title = strip_tags($this->getTitle());
        $fixed = substr($title, 0, $length);

        // If the original title was longer than the max
        // length, add an elipsis to the end.
        if (strlen($title) > $length) {
            $fixed .= ' ...';
        }

        return $fixed;

    }

    /**
     * If there is a title return it; if not, try to return
     * the first portion of the description.
     *
     * @return string $title The title.
     */
    public function getTitleOrDescription()
    {

        // Return row-level value.
        $title = $this->getTitle();
        if ($title !== '') return $title;

        else {

            // Try to get a description.
            $description = $this->getDescription();
            if ($description !== '')
                return substr($description, 0, 200);

            else return __('[Untitled]');

        }

    }

    /**
     * Return slug.
     *
     * @return string $slug The slug.
     */
    public function getSlug()
    {
        if (!is_null($this->slug)) { return $this->slug; }
        else return '';
    }

    /**
     * Return description.
     *
     * @return string $description The description.
     */
    public function getDescription()
    {

        // Build item metadata.
        if ($this->use_dc_metadata == 1) {

            /*
             * This is the biggest performance killer when calling
             * buildMapDataArray below. If this becomes too big of
             * an issue, we can inline the partial and use more
             * targetted SQL queries, instead of loading the whole
             * item and pulling the data we want out. Otherwise,
             * we're stuck. -- ERR
             */

            return get_view()->partial('neatline/_dc_metadata.php', array(
                'item' => $this->getItem()
            ));

        }

        // Return row-level value.
        if (!is_null($this->description)) {
            return $this->description;
        }

        // If there is a parent item.
        else if (!is_null($this->item_id)) {

            // Try to get a DC description.
            return metadata($this->getItem(),
                array('Dublin Core', 'Description'));

        }

        else return '';

    }

    /**
     * Return left percent.
     *
     * @return integer $percent The percent.
     */
    public function getLeftPercent()
    {

        return !is_null($this->left_percent) ?
            $this->left_percent :
            self::$defaults['left_percent'];

    }

    /**
     * Return right percent.
     *
     * @return integer $percent The percent.
     */
    public function getRightPercent()
    {

        return !is_null($this->right_percent) ?
            $this->right_percent :
            self::$defaults['right_percent'];

    }

    /**
     * Return coverage.
     *
     * @return string The coverage data. If there is record-specific data,
     * return it. If not, and there is a parent Omeka item, try to get a non-
     * empty value from the DC coverage field.
     */
    public function getGeocoverage()
    {

        // Return local value if one exists.
        if (!is_null($this->geocoverage) && $this->geocoverage !== '') {
            return $this->geocoverage;
        }

        // Try to get DC value.
        else if (!is_null($this->item_id)) {

            // If Neatline Features is not installed.
            if (!plugin_is_active('NeatlineFeatures')) {

                // Get the DC coverage.
                $coverage = metadata(
                    $this->getItem(), array('Dublin Core', 'Coverage'));

                // Return if not empty, otherwise return default.
                return ($coverage !== '') ?
                    $coverage : self::$defaults['geocoverage'];

            }

            // If Neatline Features is installed.
            else {

                // Get feature records.
                $features = $this->getTable('NeatlineFeature')
                    ->getItemFeatures($this->getItem());

                // Walk features and build array.
                $wkt = array();
                foreach ($features as $feature) {

                    // Push wkt if not null or empty.
                    if (!is_null($feature->wkt) && $feature->wkt !== '') {
                        $wkt[] = $feature->wkt;
                    }

                    // If at least one feature exists, implode and return.
                    if (count($wkt)) return implode('|', $wkt);
                    else return self::$defaults['geocoverage'];

                }

            }

        }

        // Fall back on default string.
        else return self::$defaults['geocoverage'];

    }

    /**
     * Return start date.
     *
     * @return string $date The date. If there is a record-specific value,
     * return it. If not, and there is a parent Omeka item, try to get a non-
     * empty value from the DC date field.
     */
    public function getStartDate()
    {

        // If there is a record-specific date.
        if (!is_null($this->start_date)) {
            return $this->start_date;
        }

        // If not, try to get a DC date value.
        else if (!is_null($this->item_id)) {

            // Get the DC date.
            $date = metadata(
                $this->getItem(), array('Dublin Core', 'Date'));

            if (preg_match(self::$dcDateRegex, $date, $matches)) {
                return $matches['start'];
            }

        }

        // Return '' if no local or parent data.
        else return '';

    }

    /**
     * Return end date.
     *
     * @return string $date The date. If there is a record-specific value,
     * return it. If not, and there is a parent Omeka item, try to get a non-
     * empty value from the DC date field.
     */
    public function getEndDate()
    {

        // If there is a record-specific date.
        if (!is_null($this->end_date)) {
            return $this->end_date;
        }

        // If not, try to get a DC date value.
        else if (!is_null($this->item_id)) {

            // Get the DC date.
            $date = metadata(
                $this->getItem(), array('Dublin Core', 'Date'));

            if (preg_match(self::$dcDateRegex, $date, $matches)) {
                if (array_key_exists('end', $matches)) {
                    return $matches['end'];
                }
            }

        }

        // Return '' if no local or parent data.
        else return '';

    }

    /**
     * Return start visibility date.
     *
     * @return string $date The date. If there is a record-specific value,
     * return it. If not, and there is a parent data record, try to get a non-
     * empty value from the parent.
     */
    public function getStartVisibleDate()
    {

        // If there is a record-specific date.
        if (!is_null($this->start_visible_date)) {
            return $this->start_visible_date;
        }

        // If not, try to get a DC date value.
        else if (!is_null($this->parent_record_id)) {

            // Try to get the parent date.
            $parentRecord = $this->getParentRecord();
            return $parentRecord->getStartVisibleDate();

        }

        // Return '' if no local or parent data.
        else return '';

    }

    /**
     * Return end visibility date.
     *
     * @return string $date The date. If there is a record-specific value,
     * return it. If not, and there is a parent data record, try to get a non-
     * empty value from the parent.
     */
    public function getEndVisibleDate()
    {

        // If there is a record-specific date.
        if (!is_null($this->end_visible_date)) {
            return $this->end_visible_date;
        }

        // If not, try to get a DC date value.
        else if (!is_null($this->parent_record_id)) {

            // Try to get the parent date.
            $parentRecord = $this->getParentRecord();
            return $parentRecord->getEndVisibleDate();

        }

        // Return '' if no local or parent data.
        else return '';

    }

    /**
     * Get the parent record id.
     *
     * @param integer $id The id.
     *
     * @return mixed 'none' if the id is null, otherwise the id.
     */
    public function getParentRecordId()
    {

        // If 'none' is passed, null out the key.
        if (is_null($this->parent_record_id)) {
            return 'none';
        }

        // Otherwise, set integer key.
        else return $this->parent_record_id;

    }

    /**
     * On save, update the modified column on the parent exhibit.
     *
     * @return void.
     */
    public function save()
    {

        if (!is_null($this->exhibit_id)) {
            $exhibit = $this->getExhibit();
            $exhibit->save();
        }

        parent::save();

    }

    /**
     * This deletes this record and removes itself from all parent
     * relationships.
     *
     * This assumes that the caller is wrapping this in a transaction
     * somewhere up the callstack. To do this inside a transaction that
     * this manages, use `deleteTransaction`.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function delete()
    {
        if (!is_null($this->id)) {
            $db = get_db();
            $tname = $this->getTable()->getTableName();
            $query = "
                UPDATE `$tname`
                SET parent_record_id=NULL
                WHERE parent_record_id=?;";
            $db->query($query, $this->id);

            parent::delete();

        }
    }

    /**
     * This calls `delete` in a transaction.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function deleteTransaction()
    {
        $db = get_db();
        $db->beginTransaction();
        try {
            $this->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * This sets and caches the parent record.
     *
     * @param array $index An index of records.
     * @param Omeka_record $exhibit The parent exhibit.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function _setParent($index, $exhibit)
    {

        // Set parent, recurse up the inheritance chain.
        if (!is_null($this->parent_record_id)
            && array_key_exists($this->parent_record_id, $index)
        ) {
            $parent = $index[$this->parent_record_id];
            $this->_parent = $parent;
            $parent->_setParent($index, $exhibit);
        }

        // Set parent exhibit.
        $this->_exhibit = $exhibit;

    }

    /**
     * Construct map data.
     *
     * @param array $index This is the index of NeatlineRecord objects for
     * caching. Optional.
     * @param array $wmss This is an index mapping item IDs to rows from the
     * NeatlineMapsService WMS data.
     * @param Omeka_Record $exhibit The exhibit this record belongs to.
     *
     * @return array The map JSON.
     **/
    public function buildJsonData($index=array(), $wmss=array(), $exhibit=null) {

        // If not active on map, return null.
        if ($this->space_active != 1) { return null; }

        // Cache the parent record for upcoming calls to getStyle().
        $this->_setParent($index, $exhibit);

        $data = array(

            // Relations:
            'id'                  => $this->id,
            'item_id'             => $this->item_id,

            // Text:
            'title'               => $this->getTitle(),
            'description'         => $this->getDescription(),
            'slug'                => $this->getSlug(),

            // Styles:
            'vector_color'        => $this->getStyle('vector_color'),
            'stroke_color'        => $this->getStyle('stroke_color'),
            'highlight_color'     => $this->getStyle('highlight_color'),
            'vector_opacity'      => $this->getStyle('vector_opacity'),
            'select_opacity'      => $this->getStyle('select_opacity'),
            'stroke_opacity'      => $this->getStyle('stroke_opacity'),
            'graphic_opacity'     => $this->getStyle('graphic_opacity'),
            'stroke_width'        => $this->getStyle('stroke_width'),
            'point_radius'        => $this->getStyle('point_radius'),
            'point_image'         => $this->getNotEmpty('point_image'),
            'show_bubble'         => $this->show_bubble,

            // Map:
            'center'              => $this->map_bounds,
            'zoom'                => $this->map_zoom,
            'wkt'                 => $this->getGeocoverage(),
            'wmsAddress'          => null,
            'layers'              => null,

            // Timeline:
            'start_date'          => $this->getStartDate(),
            'end_date'            => $this->getEndDate(),
            'start_visible_date'  => $this->getStartVisibleDate(),
            'end_visible_date'    => $this->getEndVisibleDate()

        );

        // If the record has a parent item and Neatline Maps is present.
        if (!is_null($this->item_id) && array_key_exists($this->item_id, $wmss)) {
            $wms = $wmss[$this->item_id];
            $data['wmsAddress'] = $wms['address'];
            $data['layers']     = $wms['layers'];
        }

        return $data;

    }

    /**
     * Construct map data.
     *
     * @param array $index This is the index of NeatlineRecord objects for
     * caching. Optional.
     * @param array $wmss This is an index mapping item IDs to rows from the
     * NeatlineMapsService WMS data.
     * @param Omeka_Record $exhibit The exhibit this record belongs to.
     *
     * @return array The map JSON.
     **/
    public function buildMapDataArray($index=array(), $wmss=array(), $exhibit=null) {

        // If not active on map, return null.
        if ($this->space_active != 1) { return null; }

        // Cache the parent record for upcoming calls to getStyle().
        $this->_setParent($index, $exhibit);

        $data = array(
            'id'                  => $this->id,
            'item_id'             => $this->item_id,
            'title'               => $this->getTitle(),
            'description'         => $this->getDescription(),
            'slug'                => $this->getSlug(),
            'vector_color'        => $this->getStyle('vector_color'),
            'stroke_color'        => $this->getStyle('stroke_color'),
            'highlight_color'     => $this->getStyle('highlight_color'),
            'vector_opacity'      => $this->getStyle('vector_opacity'),
            'select_opacity'      => $this->getStyle('select_opacity'),
            'stroke_opacity'      => $this->getStyle('stroke_opacity'),
            'graphic_opacity'     => $this->getStyle('graphic_opacity'),
            'stroke_width'        => $this->getStyle('stroke_width'),
            'point_radius'        => $this->getStyle('point_radius'),
            'point_image'         => $this->getNotEmpty('point_image'),
            'center'              => $this->map_bounds,
            'zoom'                => $this->map_zoom,
            'wkt'                 => $this->getGeocoverage(),
            'start_visible_date'  => $this->getStartVisibleDate(),
            'end_visible_date'    => $this->getEndVisibleDate(),
            'show_bubble'         => $this->show_bubble,
            'wmsAddress'          => null,
            'layers'              => null,
            '_native_styles'      => array(
                'vector_color'    => $this->vector_color,
                'vector_opacity'  => $this->vector_opacity,
                'stroke_color'    => $this->stroke_color,
                'stroke_opacity'  => $this->stroke_opacity,
                'stroke_width'    => $this->stroke_width,
                'graphic_opacity' => $this->graphic_opacity,
                'point_radius'    => $this->point_radius,
            )
        );

        // If the record has a parent item and Neatline Maps is present.
        if (!is_null($this->item_id) && array_key_exists($this->item_id, $wmss)) {
            $wms = $wmss[$this->item_id];
            $data['wmsAddress'] = $wms['address'];
            $data['layers']     = $wms['layers'];
        }

        return $data;

    }

    /**
     * Construct timeline data.
     *
     * @return array The timeline JSON.
     **/
    public function buildTimelineDataArray() {

        $event = array(
            'eventID'           => $this->id,
            'title'             => trim($this->getTitle()),
            'description'       => $this->getDescription(),
            'color'             => $this->getStyle('vector_color'),
            'left_ambiguity'    => $this->getLeftPercent(),
            'right_ambiguity'   => $this->getRightPercent(),
            'show_bubble'       => $this->show_bubble,
            'textColor'         => '#000000'
        );

        // Get start and end dates.
        $startDate = $this->getStartDate();

        // If there is a start date.
        if ($startDate !== '') {
            $event['start'] = $startDate;
            $endDate = $this->getEndDate();
            if ($endDate !== '') { $event['end'] = $endDate; }
        }

        return $event;

    }

}