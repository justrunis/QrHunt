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
 * Prints an instance of mod_qrhunt.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->libdir.'/phpqrcode/qrlib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$q = optional_param('q', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('qrhunt', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('qrhunt', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('qrhunt', array('id' => $q), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('qrhunt', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

if (!is_siteadmin()) {
    redirect(new moodle_url('/mod/qrhunt/play.php', array('id' => $cm->id)));
}

$modulecontext = context_module::instance($cm->id);

$event = \mod_qrhunt\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('qrhunt', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/qrhunt/view.php', array('id' => $cm->id));
$courseid = $PAGE->course->id;

$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['answer'])) {
        // Get the submitted answer.
        $answer = $_POST['answer'];

        // Update the answer field in the qrhunt table.
        $update = new stdClass();
        $update->id = $moduleinstance->id;
        $update->answer = $answer;
        $DB->update_record('qrhunt', $update);

        // Redirect to the current page to avoid form resubmission on refresh.
        redirect(new moodle_url('/mod/qrhunt/view.php', array('id' => $cm->id)));
    } elseif (isset($_POST['cluetext'])) {
        // Get the submitted clue text.
        $cluetext = $_POST['cluetext'];

        // Update the cluetext field in the qrhunt table.
        $update = new stdClass();
        $update->id = $moduleinstance->id;
        $update->cluetext = $cluetext;
        $DB->update_record('qrhunt', $update);

        // Redirect to the current page to avoid form resubmission on refresh.
        redirect(new moodle_url('/mod/qrhunt/view.php', array('id' => $cm->id)));
    }
}

echo $OUTPUT->header();

// Get QR code data.
$qrCodeData = $moduleinstance->answer;
// Name of generated QR code file
$imageName = $moduleinstance->name;

// Generate and save QR code image.
$imagePath = generate_qr_code_image($qrCodeData, $imageName);
// Display QR code image.
display_qr_code_image($imagePath);

// Display answer input forms for admin user
if(is_siteadmin()){
    display_answer_update_form($courseid, $moduleinstance, $cm);
    //display_clue_update_form($moduleinstance);
}

echo $OUTPUT->footer();
