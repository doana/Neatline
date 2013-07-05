<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

abstract class Neatline_Row_Abstract extends Omeka_Record_AbstractRecord
{


    const DATE_FORMAT = 'Y-m-d h:i:s';


    /**
     * Initialize record ownership mixin.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
    }


    /**
     * Return true if the passed user owns the row (if the id of the user
     * matches the `owner_id` on the row). Also return true if `owner_id`
     * is 0, which is used as a de-factor NULL value for records/exhibits
     * that have been migrated from earlier versions of Neatline that did
     * not track user ownerhsip.
     *
     * @param Omeka_User $user A user record.
     */
    public function isOwnedBy($user)
    {
        return $this->owner_id == $user->id || $this->owner_id == 0;
    }


    /**
     * Set a field if the passed value is not whitespace.
     *
     * @param string $key The name of the field.
     * @param boolean $value The value to set.
     */
    public function setNotEmpty($key, $val)
    {
        if (is_array($val)) $val = implode(',', $val);
        if (is_string($val) && trim($val) == '') $this->$key = null;
        else $this->$key = $val;
    }


    /**
     * Return an array of fields on the row that should filtered through
     * an HTML purifier before being saved to the database.
     *
     * @return array By default, filter nothing.
     */
    public function getPurifiedFields()
    {
        return array();
    }


    /**
     * Pass any fields returned by `getPurifiedFields` that have non-null
     * values on the row through an HTML sanitizer.
     *
     * @return array The array representation of the row.
     */
    public function purifyFields()
    {
        $purifier = new Omeka_Filter_HtmlPurifier;
        foreach ($this->getPurifiedFields() as $f) {
            if ($this->$f) $this->$f = $purifier->filter($this->$f);
        }
    }


    /**
     * Mass-assign an associative array to the row and sanitize fields.
     *
     * @param array $values The array of values.
     */
    public function setArray($values)
    {
        foreach ($values as $key => $val) $this->setNotEmpty($key, $val);
        $this->purifyFields();
    }


    /**
     * Cast the row to an array of data to be inserted into the database.
     * By default, just alias the regular `toArray` method. This is broken
     * out and used by `insertOrUpdate` in order to make it possible for
     * concrete row classes to modify or wrap field values before saving,
     * without changing the publicly-accessible values on the object (for
     * example, WKT strings need to be wrapped in `GeomFromText` database
     * expressions before being inserted).
     *
     * @return array The array representation of the record fields.
     */
    public function toArrayForSave()
    {
        return $this->toArray();
    }


    /**
     * Update the `modified` timestamp when the row is saved.
     *
     * @param array $arg Contains `post` and `insert`.
     */
    public function beforeSave($args)
    {
        $this->modified = date(self::DATE_FORMAT);
    }


    /**
     * Insert or update the record.
     *
     * @param boolean $throwIfInvalid
     */
    public function save($throwIfInvalid = true)
    {

        $args = array(
            'post'    => $this->_postData ? $this->_postData : false,
            'insert'  => !$this->exists()
        );

        // Insert/udpate the master record.
        $this->runCallbacks('beforeSave', $args);
        $this->insertOrUpdate($this->toArrayForSave());
        $this->runCallbacks('afterSave', $args);

    }


    /**
     * Insert or update the record. Approach based on:
     * https://gist.github.com/1942116
     *
     * @param array $values The record values.
     */
    public function insertOrUpdate(array $values)
    {

        $table = $this->getTable();
        $valid = $table->getColumns();
        $db = $table->getAdapter();

        $cols = array();
        $vals = array();
        $bind = array();
        $set  = array();

        // Build column / value arrays.
        foreach ($values as $col => $val) {

            // Pass if column doesn't exist.
            if (!in_array($col, $valid)) continue;

            // Register the column.
            $cols[] = $db->quoteIdentifier($col, true);

            // Register the value.
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
            } else {
                $vals[] = '?';
                $bind[] = $val;
            }

        }

        // Build update clauses.
        foreach ($cols as $i => $col) {
            $set[] = sprintf('%s = %s', $col, $vals[$i]);
        }

        // Build the query.
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s;",
            $db->quoteIdentifier($table->getTableName(), true),
            implode(', ', $cols),
            implode(', ', $vals),
            implode(', ', $set)
        );

        // Insert or update.
        $wasInserted = !$this->exists();
        $db->query($sql, array_merge($bind, $bind));

        // If record was inserted, set `id`.
        if ($wasInserted) $this->id = $db->lastInsertId();

    }


}
