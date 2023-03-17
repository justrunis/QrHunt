<?php
// This file is part of Treasurehunt for Moodle
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade script for the treasurehunt module.
 *
 * @package mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro
 *            <jpdecastro@tel.uva.es>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Execute treasurehunt upgrade from the given old version
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_qrhunt_upgrade($oldversion) {
    global $DB;
    /** @var database_manager */
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    if ($oldversion < 2023030805) {

        // Define fields to be added to qrhunt.
        $table = new xmldb_table('qrhunt');
        $cluetext_field = new xmldb_field('cluetext', XMLDB_TYPE_TEXT, null, null, null, null, null, 'introformat');
        $answer_field = new xmldb_field('answer', XMLDB_TYPE_TEXT, null, null, null, null, null, 'cluetext');
        $qr_code_image_url_field = new xmldb_field('qr_code_image_url', XMLDB_TYPE_TEXT, null, null, null, null, null, 'answer');

        // Conditionally launch add field cluetext.
        if (!$dbman->field_exists($table, $cluetext_field)) {
            $dbman->add_field($table, $cluetext_field);
        }

        // Conditionally launch add field answer.
        if (!$dbman->field_exists($table, $answer_field)) {
            $dbman->add_field($table, $answer_field);
        }

        // Conditionally launch add field qr_code_image_url.
        if (!$dbman->field_exists($table, $qr_code_image_url_field)) {
            $dbman->add_field($table, $qr_code_image_url_field);
        }

        // Qrhunt savepoint reached.
        upgrade_mod_savepoint(true, 2023030805, 'qrhunt');
    }
    if ($oldversion < 2023030806) {

        // Define table qrhunt_user_activity to be created.
        $table = new xmldb_table('qrhunt_user_activity');

        // Adding fields to table qrhunt_user_activity.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('answer', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('answertimestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('starttime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('time_taken', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('activityid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table qrhunt_user_activity.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('activityid', XMLDB_KEY_FOREIGN, ['activityid'], 'qrhunt', ['id']);

        // Conditionally launch create table for qrhunt_user_activity.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Qrhunt savepoint reached.
        upgrade_mod_savepoint(true, 2023030806, 'qrhunt');
    }
    if ($oldversion < 2023030807) {

        // Define field correctanswer to be added to qrhunt_user_activity.
        $table = new xmldb_table('qrhunt_user_activity');
        $field = new xmldb_field('correctanswer', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'activityid');

        // Conditionally launch add field correctanswer.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qrhunt savepoint reached.
        upgrade_mod_savepoint(true, 2023030807, 'qrhunt');
    }
    if ($oldversion < 2023030808) {

        // Define table qrhunt_activity_completion to be created.
        $table = new xmldb_table('qrhunt_activity_completion');

        // Adding fields to table qrhunt_activity_completion.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table qrhunt_activity_completion.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for qrhunt_activity_completion.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Qrhunt savepoint reached.
        upgrade_mod_savepoint(true, 2023030808, 'qrhunt');
    }
    if ($oldversion < 2023030810) {

        // Define table qrhunt_grades to be created.
        $table = new xmldb_table('qrhunt_grades');

        // Adding fields to table qrhunt_grades.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('qrhunt', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table qrhunt_grades.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('qrhunt', XMLDB_KEY_FOREIGN, ['qrhunt'], 'qrhunt', ['id']);

        // Adding indexes to table qrhunt_grades.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch create table for qrhunt_grades.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Qrhunt savepoint reached.
        upgrade_mod_savepoint(true, 2023030810, 'qrhunt');
    }


    return true;
}
