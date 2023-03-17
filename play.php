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
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("$CFG->dirroot/mod/qrhunt/lib.php");
require_once($CFG->libdir . '/completionlib.php');

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

$PAGE->requires->jquery();
$PAGE->requires->js('/mod/qrhunt/JavaScript/qrscanner.js');

echo $OUTPUT->header();

// Start time
$starttimestamp = time();

$hasSubmitedQR = false;

$hasAnsweredCorrectly = has_user_answered_correctly($DB, $USER, $moduleinstance);

if(!$hasAnsweredCorrectly){

  display_camera();
  // Display form to submit answers
  display_user_submit_form();

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_answer'])) {
    // Get the submitted answer.
    $answer = $_POST['user_answer'];
    
    insert_user_activity_data($DB, $moduleinstance, $answer, $USER, $starttimestamp, $cm);
  }


  if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
  }
  //create_button_to_home(false);
  $completion = new completion_info($PAGE->course);
  if ($completion->is_enabled($cm)) {
      $completion->update_state($cm, COMPLETION_INCOMPLETE, $USER->id);
  }
  create_button_to_course($courseid, false);
}
else{
    echo "<div class='alert alert-success' role='alert'>".get_string('correctanswermessage', 'mod_qrhunt')."</div>";


    create_button_to_course($courseid, false);

    qrhunt_mod_instance_can_be_completed($cm, $USER->id);


    insert_grades_to_grade_table($moduleinstance->id, $USER->id, 10);



    $temp = qrhunt_update_grades($moduleinstance, $USER->id);
    set_final_grade($moduleinstance->id, $USER->id, 10);
    //var_dump($temp);
}

echo $OUTPUT->footer();