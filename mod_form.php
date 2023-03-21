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

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qrhunt_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('qrhuntname', 'mod_qrhunt'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'qrhuntname', 'mod_qrhunt');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_qrhunt settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        // Availability
        $mform->addElement('header', 'qrhuntfieldset', get_string('qrhuntfieldset', 'mod_qrhunt'));

        $name = get_string('allowattemptsfromdate', 'qrhunt');
        $options = array('optional' => true, 'step' => 1);
        $mform->addElement('date_time_selector', 'allowattemptsfromdate', $name, $options);
        $mform->addHelpButton('allowattemptsfromdate', 'allowattemptsfromdate', 'qrhunt');

        $name = get_string('cutoffdate', 'qrhunt');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, $options);
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'qrhunt');

        $name = get_string('alwaysshowdescription', 'qrhunt');
        $mform->addElement('checkbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'qrhunt');
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $mform->addElement('header', 'gradeheader', get_string('grading', 'qrhunt'));

        $mform->addElement('text', 'grade', get_string('gradetopass', 'qrhunt'), array('value' => 10));
        $mform->setType('grade', PARAM_INT);
        $mform->addRule('grade', get_string('required'), 'required', null, 'client');

        $mform->addElement('header', 'completionheader', get_string('completionheader', 'qrhunt'));

        $mform->addElement('advcheckbox', 'completionansweredcorrectly', get_string('completionansweredcorrectly', 'qrhunt'), get_string('completionansweredcorrectlydesc', 'qrhunt'));
        $mform->setDefault('completionansweredcorrectly', 1);
        $mform->addHelpButton('completionansweredcorrectly', 'completionansweredcorrectly', 'qrhunt');

        // Add standard elements.
        $this->standard_coursemodule_elements();

        $mform->disabledIf('completion', '1', 'eq');

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
