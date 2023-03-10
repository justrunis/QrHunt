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

$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->jquery();
$PAGE->requires->js('/mod/qrhunt/JavaScript/qrscanner.js');

echo $OUTPUT->header();

// Start time
$starttimestamp = time();

// Display the clue
diplay_clue_text($moduleinstance);

// Add the id attribute to the qr-scanner div element.
?>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
<script src="JavaScript/qrcodejs-master/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<button class="btn btn-dark" id="start-camera" style="display:none;">Start Camera</button>

<div>
  <video id="video" width="300" height="225"></video>
  <canvas id="canvas" style="display:none;"></canvas>
  <div id="result"></div>
</div>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const result = document.getElementById('result');
const startCameraBtn = document.getElementById('start-camera');

let hasFinished = false;
let stream = null;

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia && hasFinished == false) {
  startCameraBtn.style.display = "block"; // show the start camera button
  startCameraBtn.addEventListener('click', function() {
    startCameraBtn.style.display = "none"; // hide the start camera button
    navigator.mediaDevices.getUserMedia({ video: true }).then(function(mediaStream) {
      stream = mediaStream;
      video.srcObject = stream;
      video.play();
      console.log('Displaying the camera feed')
      video.addEventListener('play', function() {
        const ctx = canvas.getContext('2d');
        setInterval(function() {
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const code = jsQR(imageData.data, imageData.width, imageData.height);
          if (code) {
            console.log('QR code detected:', code.data);
            result.innerText = "Scanned text: " + code.data;
            hasFinished = true;
            video.style.display = 'none';
            stream.getTracks().forEach(function(track) {
              track.stop();
            });
            // post the scanned text to PHP
            $.post("play.php", { scannedText: code.data });
          }
        }, 100);
      }, false);
    }).catch(function(error) {
      console.error('Failed to access device camera', error);
    });
  });
}
</script>

<?php


$hasAnsweredCorrectly = has_user_answered_correctly($DB, $USER, $moduleinstance);

if(!$hasAnsweredCorrectly){
    // Display form to submit answers
    display_user_submit_form();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_answer'])) {
        // Get the submitted answer.
        $answer = $_POST['user_answer'];
        
        // Insert the user's answer into the qrhunt_user_activity table.
        $user_activity = new stdClass();
        $user_activity->activityid = $moduleinstance->id;
        $user_activity->userid = $USER->id;
        $user_activity->answer = $answer;
        $user_activity->answertimestamp = time();
        $user_activity->starttime = $starttimestamp;
        $user_activity->time_taken = $starttimestamp - time();

        // Compare the user's answer with the correct answer in the qrhunt table.
        $qrhunt = $DB->get_record('qrhunt', array('id' => $moduleinstance->id));
        if ($answer == $qrhunt->answer) {
            $user_activity->correctanswer = 1;
            $DB->insert_record('qrhunt_user_activity', $user_activity);
        } else {
            $user_activity->correctanswer = 0;
            $DB->insert_record('qrhunt_user_activity', $user_activity);
            $_SESSION['message'] = get_string('incorrectanswermessage', 'mod_qrhunt');
        }
        
        // Redirect the user to the same page after the form has been submitted.
        redirect(new moodle_url('/mod/qrhunt/play.php', array('id' => $cm->id)));
    }

    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    create_button_to_home(false);
}
else{
    echo "<div class='alert alert-success' role='alert'>".get_string('correctanswermessage', 'mod_qrhunt')."</div>";
    create_button_to_home(false);
}

echo $OUTPUT->footer();