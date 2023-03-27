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
 * Library of interface functions and constants.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
require_once($CFG->libdir . '/gradelib.php');
function qrhunt_supports($feature) {
    switch ($feature) {
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_qrhunt into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_qrhunt_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function qrhunt_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('qrhunt', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_qrhunt in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_qrhunt_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function qrhunt_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('qrhunt', $moduleinstance);
}

/**
 * Removes an instance of the mod_qrhunt from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function qrhunt_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('qrhunt', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('qrhunt', array('id' => $id));

    return true;
}

function generate_qr_code_data($data, $size = 10, $margin = 1, $errorCorrection = 'L') {
    $tempDir = sys_get_temp_dir();
    $filename = tempnam($tempDir, 'qr');
    QRcode::png($data, $filename, $errorCorrection, $size, $margin);
    $data = file_get_contents($filename);
    unlink($filename);
    return $data;
}

/**
 * @param $qrCodeData
 * @param $imageName
 * @return string
 */
function generate_qr_code_image($qrCodeData, $imageName) {
    $imagePath = __DIR__ . '/qrcodes/' . $imageName . '.png';

    // Generate and save QR code image.
    QRcode::png($qrCodeData, $imagePath, QR_ECLEVEL_L, 10);

    return $imagePath;
}

function display_qr_code_image($imagePath) {
    if (file_exists($imagePath)) {
        // Display the image and add download button.
        $imageContent = file_get_contents($imagePath);
        $imageData = base64_encode($imageContent);

        if (is_siteadmin()) {
            echo html_writer::tag('h1', get_string('generatedqrcode', 'mod_qrhunt'), array('class' => 'h1-text'));
            echo html_writer::tag('div', html_writer::empty_tag('img', array('src' => 'data:image/png;base64,' . $imageData)), array('class' => 'image-container'));
            $linkAttributes = array(
                'href' => 'download.php?file=' . $imagePath,
                'class' => 'btn-big',
            );
            echo html_writer::start_tag('div', array('class' => 'image-container'));
            echo html_writer::start_tag('a', $linkAttributes);
            echo get_string('downloadqrcode', 'mod_qrhunt');
            echo html_writer::end_tag('a');
            echo html_writer::end_tag('div');
            echo html_writer::tag('br', '');
        }
    } else {
        echo html_writer::tag('h1', get_string('noqrimagefound', 'mod_qrhunt'), array('class' => 'h1-text'));
    }
}

function display_camera(){
    ?>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
    <script src="JavaScript/qrcodejs-master/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <h1 class="h1-user-text"><?php echo get_string('gametitle', 'mod_qrhunt'); ?></h1>

    <button class="btn-big" id="start-camera" style="display:none; margin-left: 130px; margin-top:15px; margin-bottom: 15px">
        <?php echo get_string('startcamera', 'mod_qrhunt'); ?>
    </button>
    <button class="btn-big" id="stop-camera" style="display:none; margin-left: 130px; margin-top:15px; margin-bottom: 15px">
        <?php echo get_string('stopcamera', 'mod_qrhunt'); ?>
    </button>

    <div id='video-container' style="display: none;">
      <video id="video"></video>
      <canvas id="canvas"></canvas>
      <h3 style="display: none;" id="result"></h3>
    </div>

    <script src="JavaScript/qrscanner.js"></script>

    <?php
}

function insert_user_activity_data($DB, $moduleinstance, $answer, $USER, $starttimestamp, $cm, $PAGE) {
      
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

        $rawgrade = 100;
        write_qrhunt_user_grade($moduleinstance, $USER, $PAGE, $rawgrade);
    } else {
        $user_activity->correctanswer = 0;
        $DB->insert_record('qrhunt_user_activity', $user_activity);
        $_SESSION['message'] = get_string('incorrectanswermessage', 'mod_qrhunt');
    }

}

function display_answer_update_form($courseid, $moduleinstance, $cm){
    // Form attributes.
    $form_attributes = array(
        'method' => 'post',
        'class' => 'form-horizontal'
    );
    
    // Textarea attributes.
    $textarea_attributes = array(
        'name' => 'answer',
        'id' => 'answer',
        'placeholder' => get_string('enterqranswer', 'mod_qrhunt'),
        'rows' => '5',
        'cols' => '87',
        'class' => 'input-textarea'
    );
    
    // Submit button attributes.
    $input_attributes = array(
        'type' => 'submit',
        'value' => get_string('refreshqr', 'mod_qrhunt'),
        'class' => 'btn-big d-inline-block',
        'style' => 'margin-right: 20px',
    );
    
    // Form start.
    echo html_writer::start_tag('form', $form_attributes);

    // Textarea div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::tag('textarea', $moduleinstance->answer, $textarea_attributes);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    // Button div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::start_tag('div', array('class' => 'button-wrapper'));
    echo html_writer::empty_tag('input', $input_attributes);
    create_button_to_play($cm);
    create_button_to_course($courseid, true);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    // Form end.
    echo html_writer::end_tag('form');     
    
}

function display_user_submit_form($courseid){
    $form_attributes = array(
        'method' => 'post'
    );

    $input_attributes = array(
        'type' => 'text',
        'name' => 'user_answer',
        'id' => 'user_answer',
        'placeholder' => get_string('enteranswer', 'mod_qrhunt'),
        'rows' => '5',
        'cols' => '70',
        'class' => 'input-textarea'
    );

    $submit_attributes = array(
        'type' => 'submit',
        'name' => 'submit_answer',
        'value' => get_string('submitanswer', 'mod_qrhunt'),
        'class' => 'btn-big d-inline-block',
        'style' => 'margin-right: 15px'
    );

    echo html_writer::start_tag('form', $form_attributes);
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::start_tag('textarea', $input_attributes);
    echo html_writer::end_tag('textarea');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::empty_tag('input', $submit_attributes);
    create_button_to_course($courseid, false);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    echo html_writer::end_tag('form');
}


function create_button_to_play($cm) {
    global $PAGE;
    
    $url = new moodle_url('/mod/qrhunt/play.php', array('id' => $PAGE->cm->id));
    $link_attributes = array(
        'href' => $url->out(),
        'class' => 'btn-big d-inline-block',
    );
    
    echo html_writer::start_tag('a', $link_attributes);
    echo get_string('play', 'mod_qrhunt');
    echo html_writer::end_tag('a');
}

function create_button_to_home($needMargin) {
    global $CFG;

    $url = new moodle_url($CFG->wwwroot);
    if($needMargin){
        $link_attributes = array(
            'href' => $url->out(),
            'class' => 'btn-big',
            'style' => 'margin-left: 20px;',
        );
    }
    else{
        $link_attributes = array(
            'href' => $url->out(),
            'class' => 'btn-big',
        );
    }
    
    echo html_writer::start_tag('a', $link_attributes);
    echo get_string('back', 'mod_qrhunt');
    echo html_writer::end_tag('a');
}

function create_button_to_course($courseid, $needMargin) {
    global $CFG;

    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    if($needMargin){
        $link_attributes = array(
            'href' => $url->out(),
            'class' => 'btn-big d-inline-block',
            'style' => 'margin-left: 20px;',
        );
    }
    else{
        $link_attributes = array(
            'href' => $url->out(),
            'class' => 'btn-big d-inline-block',
        );
    }
    
    echo html_writer::start_tag('a', $link_attributes);
    echo get_string('back', 'mod_qrhunt');
    echo html_writer::end_tag('a');
}


function has_user_answered_correctly($DB, $USER, $moduleinstance){
    $records = $DB->get_records('qrhunt_user_activity', array('userid' => $USER->id));
    $qrhunt = $DB->get_record('qrhunt', array('id' => $moduleinstance->id));

    foreach ($records as $record) {
        if($record->correctanswer == 1 && $record->answer == $qrhunt->answer){
            return true;
        }
    }
    return false;
}

function completion_info_course($course, $user, $modinfo, $cmid) {
    global $DB, $USER;
    $completion = new completion_info($course);
    $mod = $modinfo->get_cm($cmid);
    $moduleinstance = $modinfo->get_instance($mod->id);

    $is_completed = false;

    // Check if user has viewed the task
    if ($completion->is_enabled($mod) && $completion->is_viewed($mod)) {
        // Check if user has answered the question correctly
        if (has_user_answered_correctly($DB, $USER, $moduleinstance)) {
            $is_completed = true;
        }
    }

    $output = '';

    if ($is_completed) {
        $output .= '<i class="fa fa-check"></i> Completed';
    } else {
        $output .= '<i class="fa fa-times"></i> Incomplete';
    }

    return $output;
}

function qrhunt_mod_instance_can_be_completed($cm, $id) {

    // Set the completion status for this user
    $completion = new completion_info(get_course($cm->course));
    $completion->update_state($cm, COMPLETION_COMPLETE, intval($id));
}

function write_qrhunt_user_grade($moduleInstance, $USER, $PAGE, $rawgrade){
    $item = array(
        'itemname' => $moduleInstance->name,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax' => 100,
        'grademin' => 0
    );

    $grade = array(
        'userid' => $USER->id,
        'rawgrade' => $rawgrade,
        'dategraded' => (new DateTime())->getTimestamp(),
        'datesubmitted' => (new DateTime())->getTimestamp(),
    );
    $grades = [$USER->id => (object)$grade];
    return grade_update('mod_qrhunt', $PAGE->course->id, 'mod', 'qrhunt', $moduleInstance->id, 0, $grades, $item);
}

function qrhunt_grade_item_update($qrhunt, $grades=NULL) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    $params = array('itemname'=>$qrhunt->name, 'idnumber'=>$qrhunt->id);

    if ($qrhunt->answer == NULL) {
        $params['gradetype'] = GRADE_TYPE_NONE;

    } else if ($qrhunt->answer != NULL) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = 10;
        $params['grademin']  = 0;
        $params['rawgrade']  = 10;
        $params['finalgrade']  = 10;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }
    $grades_modified = array();
    foreach ($grades as $grade) {
        $grades_modified[$grade->userid] = array('userid' => $grade->userid, 'grade' => (int) $grade->grade);
    }
    //var_dump($grades_modified);
    return grade_update('mod/qrhunt', $qrhunt->course, 'mod', 'qrhunt', $qrhunt->id, 0, $grades_modified, $params);

}

function qrhunt_update_grades($qrhunt, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if ($qrhunt->answer == NULL) {
        qrhunt_grade_item_update($qrhunt);

    } else if ($grades =  qrhunt_get_user_grades($qrhunt, $userid)) {
        qrhunt_grade_item_update($qrhunt, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        qrhunt_grade_item_update($qrhunt, $grade);

    } else {
        qrhunt_grade_item_update($qrhunt);
    }
}

function qrhunt_get_user_grades($qrhunt, $userid) {
    global $DB;

    $sql = "SELECT * 
            FROM {qrhunt_grades} 
            WHERE userid = ? 
              AND qrhunt = ?";

    $userid = intval($userid); // make sure $userid is an integer
    $qrhunt = intval($qrhunt->id); // make sure $qrhunt->id is an integer

    if (!$userid || !$qrhunt) {
        return false; // return false if either $userid or $qrhuntid is not defined or has an invalid value
    }

    $params = array($userid, $qrhunt);

    $records = $DB->get_records_sql($sql, $params);

    if (!$records) {
        return array(); // return an empty array if no records were found
    }
    return $records;
}

function insert_grades_to_grade_table($qrhunt, $userid, $grade){
    global $CFG, $DB;

    $existing_record = $DB->get_records('qrhunt_grades', array('qrhunt' => $qrhunt, 'userid' => $userid));
    if ($existing_record) {
        // Record already exists, do not insert new data
        return;
    }

    $sql = 'INSERT INTO {qrhunt_grades} (qrhunt, userid, grade, timemodified)
            VALUES (?, ?, ?, ?)';
    $params = array($qrhunt, $userid, $grade, time());
    $DB->execute($sql, $params);
}

/**
 * Set final grade for a given user and item.
 *
 * @param int $item_id ID of the grade item.
 * @param int $user_id ID of the user.
 * @param float $final_grade Final grade to set for the user and item.
 */
function set_final_grade($item_id, $user_id, $final_grade) {
    global $DB;

    $item = $DB->get_record('grade_items', array('id' => $item_id));
    if (!$item) {
        throw new moodle_exception('Invalid grade item ID');
    }

    $user = $DB->get_record('user', array('id' => $user_id));
    if (!$user) {
        throw new moodle_exception('Invalid user ID');
    }

    // Check if the user already has a grade for the item.
    $grade = $DB->get_record('grade_grades', array('itemid' => $item_id, 'userid' => $user_id));
    if (!$grade) {
        throw new moodle_exception('Grade record not found');
    }

    // Verify that the user's grade is not empty
    if (empty($grade->rawgrade)) {
        //return false; // User's grade hasn't been graded yet
        $grade->rawgrade = $final_grade;

        // Set the final grade for the user and item.
        $grade->finalgrade = $final_grade;
        $grade->finalmodified = time();
        $grade->hidden = 0;
        $grade->locked = 0;
        $DB->update_record('grade_grades', $grade);

        // Recalculate the user's course grade.
        $course = $DB->get_record('course', array('id' => $item->courseid));

        if (!$course) {
            throw new moodle_exception('Invalid course ID');
        }
    }
    //var_dump($grade);

}

function qrhunt_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $qrhunts = $DB->get_records_sql("
            SELECT q.*, cm.idnumber as cmidnumber, q.course as courseid
            FROM {modules} m
            JOIN {course_modules} cm ON m.id = cm.module
            JOIN {quiz} q ON cm.instance = q.id
            WHERE m.name = 'qrhunt' AND cm.course = ?", array($courseid));

    foreach ($qrhunts as $qrhunt) {
        qrhunt_grade_item_update($qrhunt, 'reset');
    }
}