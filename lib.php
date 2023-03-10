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

function qrhunt_supports($feature) {
    switch ($feature) {
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
            echo "<h1>Generated QR Code</h1>";
            echo "<div><img src='data:image/png;base64,{$imageData}'></div>";
            echo "<a href='download.php?file={$imagePath}'><button type=\"button\" class=\"btn btn-dark custom-button\">Download QR Code</button></a><p></p>";
        }
    } else {
        echo '<h1>No QR code image found.</h1>';
    }
}

function insert_user_activity_data($DB, $moduleinstance, $answer) {
    // Get the user ID and activity ID.
    global $USER;
    $userid = $USER->id;
    $activityid = $moduleinstance->id;

    // Insert the user activity data into the database.
    $qrhunt_user_activity = new stdClass();
    $qrhunt_user_activity->userid = $userid;
    $qrhunt_user_activity->activityid = $activityid;
    $qrhunt_user_activity->answer = $answer;
    $qrhunt_user_activity->answertimestamp = time();

    // Compare the submitted answer with the QR code data.
    if ($answer == $moduleinstance->intro) {
        $qrhunt_user_activity->correctanswer = 1;
        $DB->insert_record('qrhunt_user_activity', $qrhunt_user_activity);
        echo '<br>Congratulations, your answer is correct!';
    } else {
        $qrhunt_user_activity->correctanswer = 0;
        $DB->insert_record('qrhunt_user_activity', $qrhunt_user_activity);
        echo '<br>Sorry, your answer is incorrect.';
    }

}

function display_answer_update_form(){
    // Form attributes.
    $form_attributes = array(
        'method' => 'post',
        'class' => 'form-horizontal'
    );
    
    // Textarea attributes.
    $textarea_attributes = array(
        'name' => 'answer',
        'id' => 'answer',
        'placeholder' => 'Enter QR answer',
        'rows' => '5',
        'cols' => '50'
    );
    
    // Submit button attributes.
    $input_attributes = array(
        'type' => 'submit',
        'value' => 'Refresh QR',
        'class' => 'btn btn-dark'
    );
    
    // Form start.
    echo html_writer::start_tag('form', $form_attributes);

    // Textarea div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::tag('textarea', '', $textarea_attributes);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    
    // Button div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::empty_tag('input', $input_attributes);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    
    // Form end.
    echo html_writer::end_tag('form');     
    
}

function display_clue_update_form(){

    // Form attributes.
    $form_attributes = array(
        'method' => 'post',
    );

    // Textarea attributes.
    $textarea_attributes = array(
        'name' => 'cluetext',
        'id' => 'cluetext',
        'placeholder' => 'Enter the new clue text',
        'rows' => '5',
        'cols' => '50',
    );

    // Submit button attributes.
    $button_attributes = array(
        'type' => 'submit',
        'value' => 'Update Clue Text',
        'class' => 'btn btn-dark',
    );

    // Form start.
    echo html_writer::start_tag('form', $form_attributes);

    // Textarea div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::tag('textarea', '', $textarea_attributes);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    // Button div.
    echo html_writer::start_tag('div', array('class' => 'form-group row'));
    echo html_writer::start_tag('div', array('class' => 'col-md-12'));
    echo html_writer::empty_tag('input', $button_attributes);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    // Form end.
    echo html_writer::end_tag('form');

}

function submit_answer_form(){
    echo '<form method="post">';
    echo '<input type="text" name="answer" placeholder="Enter your answer">';
    echo '<button type="submit">Update Clue Text</button>';
    echo '</form>';
}
