<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_qrhunt configuration form.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/lib.php');

class custom_completion_condition extends completion_condition {

    public static function get_name() {
        // return the name of your completion condition
        return get_string('custom_completion_condition_name', 'qrhunt');
    }

    public static function get_description() {
        // return a description of your completion condition
        return get_string('custom_completion_condition_description', 'qrhunt');
    }

    function is_complete($userid, $courseid, $activityid) {
        global $DB, $USER;

        $moduleinstance = $DB->get_record('qrahunt', array('id' => $activityid), '*', MUST_EXIST);

        if (!has_user_answered_correctly($DB, $USER, $moduleinstance)) {
            return false;
        }

        $completion = new \completion_info($DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST));
        return $completion->is_enabled($moduleinstance) && $completion->is_complete($userid, $moduleinstance);
    }

    function qrhunt_completion_criteria() {
        return COMPLETION_CRITERIA_MANUAL | COMPLETION_CRITERIA_AND;
    }
}
