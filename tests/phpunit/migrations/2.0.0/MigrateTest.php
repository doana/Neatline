<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Migrate200Test extends Neatline_Case_Migrate200
{


    /**
     * The original exhibits, records, and base layers tables should be
     * preserved with `_migrate` extensions.
     */
    public function testBackupOldTables()
    {

        $this->_upgrade();

        $tables = $this->db->listTables();
        $p = "{$this->db->prefix}neatline_";

        $this->assertContains("{$p}exhibits_migrate", $tables);
        $this->assertContains("{$p}data_records_migrate", $tables);
        $this->assertContains("{$p}base_layers_migrate", $tables);

    }


    /**
     * The new `neatline_exhibits` and `neatline_records` tables with the
     * 2.x schema should be installed.
     */
    public function testInstallNewTables()
    {

        $this->_upgrade();

        $tables = $this->db->listTables();
        $p = "{$this->db->prefix}neatline_";

        $this->assertContains("{$p}exhibits", $tables);
        $this->assertContains("{$p}simile_exhibit_expansions", $tables);
        $this->assertContains("{$p}records", $tables);

        $exhibits = $this->db->describeTable("{$p}exhibits");
        $records = $this->db->describeTable("{$p}records");

        $this->assertArrayHasKey('widgets', $exhibits);
        $this->assertArrayHasKey('widgets', $records);

    }


    /**
     * When the plugin is upgraded, the background process to migrate the
     * data should be added to the queue.
     */
    public function testStartBackgroundJob()
    {

        $this->_upgrade();

        $jobs = $this->db->select()
            ->from("{$this->db->prefix}processes")
            ->where("class='Omeka_Job_Process_Wrapper'")
            ->where("args LIKE '%Neatline_Job_UpgradeFrom1x%'")
            ->where("status='starting'")
            ->where("PID IS NULL")
            ->query()->fetchAll();

        $this->assertNotEmpty($jobs);

    }


    /**
     * All exhibit rows should be migrated into the new table.
     */
    public function testMigrateAllExhibits()
    {

        $this->_loadFixture('Hotchkiss.exhibits.json');

        $this->_upgrade();
        $this->_migrate();

        $c1sql = <<<SQL
        SELECT COUNT(*) FROM
        {$this->db->prefix}neatline_exhibits_migrate
SQL;

        $c2sql = <<<SQL
        SELECT COUNT(*) FROM
        {$this->db->prefix}neatline_exhibits
SQL;

        $c1 = $this->db->query($c1sql)->fetch();
        $c2 = $this->db->query($c2sql)->fetch();

        $this->assertEquals($c1, $c2);

    }


    /**
     * All record rows should be migrated into the new table.
     */
    public function testMigrateAllRecords()
    {

        $this->_loadFixture('Hotchkiss.records.json');

        $this->_upgrade();
        $this->_migrate();

        $c1sql = <<<SQL
        SELECT COUNT(*) FROM
        {$this->db->prefix}neatline_data_records_migrate
SQL;

        $c2sql = <<<SQL
        SELECT COUNT(*) FROM
        {$this->db->prefix}neatline_records
SQL;

        $c1 = $this->db->query($c1sql)->fetch();
        $c2 = $this->db->query($c2sql)->fetch();

        $this->assertEquals($c1, $c2);

    }


}