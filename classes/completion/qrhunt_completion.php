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
// Define the completion criteria
$completioncriteria = array(
    'viewed' => COMPLETION_COMPLETE,
    // Add any additional completion criteria specific to your activity
);

require_once($CFG->libdir . '/completionlib.php');

// Implement the completion_info interface
class mod_qrhunt_completion_info implements completion_info {
    public function is_enabled($cm) {
        global $DB;
        $instance = $DB->get_record('qrhunt', array('id' => $cm->instance), '*', MUST_EXIST);
        return !empty($cm->completion) && $instance->completionenabled;
    }
    

    public function is_viewed($cm, $userid) {
        global $DB;
        $record = $DB->get_record('yourplugin_completion', array('userid' => $userid, 'activity_id' => $cm->instance), 'completed');
        return ($record && $record->completed == 1);
    }
    

    public function is_complete($cm, $userid) {
        global $DB;
        // Check if the activity is complete for the user
        $completion = $DB->get_record('qrhunt_activity_completion', array('userid' => $userid, 'activity_id' => $cm->instance), 'completed');
        if ($completion && $completion->completed) {
            return true;
        }
        return false;
    }

    public function get_view_url($cm) {
        // Return the URL to view the activity
        return new moodle_url('/mod/yourplugin/view.php', array('id' => $cm->id));
    }

    public function get_completion_state($cm, $userid) {
        global $DB;
        // Get the completion state for the user
        $completion = $DB->get_record('qrhunt_activity_completion', array('userid' => $userid, 'activity_id' => $cm->instance), 'completed');
        if ($completion) {
            return $completion->completed;
        }
        return COMPLETION_UNKNOWN;
    }

    public function get_completion_criteria_info($cm) {
        // Get the completion criteria for the activity
        $criteria = array(
            'completed' => COMPLETION_COMPLETE
        );
        return $criteria;
    }

    public function update_completion_state($cm, $userid) {
        global $DB;
        $completion = $DB->get_record('qrhunt_activity_completion', array('userid' => $userid, 'activity_id' => $cm->instance), 'id, completed');
        if ($completion) {
            $completion->completed = COMPLETION_COMPLETE;
            $DB->update_record('qrhunt_activity_completion', $completion);
        } else {
            $completion = new stdClass();
            $completion->userid = $userid;
            $completion->activity_id = $cm->instance;
            $completion->completed = COMPLETION_COMPLETE;
            $DB->insert_record('qrhunt_activity_completion', $completion);
        }
    }
}

// Update the completion status
$cmid = $PAGE->cm->id; // ID of the activity instance
$userid = $USER->id; // ID of the user who completed the activity

$completion = new mod_qrhunt_completion_info();

$cm = get_coursemodule_from_id('yourplugin', $cmid);
$completion = new completion_info($cm->course);
$completion->set_module_viewed($cm);
$completion->update_state($userid, COMPLETION_COMPLETE);

/*
// Update the completion status
$cmid = $PAGE->cm->id; // ID of the activity instance
$userid = $USER->id; // ID of the user who completed the activity

$completion = new mod_yourplugin_completion_info();

$cm = get_coursemodule_from_id('yourplugin', $cmid);
$completion->set_module_viewed($cm, $userid);
$completion->update_state($userid, COMPLETION_COMPLETE, $cmid);
*/