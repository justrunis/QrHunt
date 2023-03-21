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
use core_completion\activity_custom_completion;

/**
 * The main mod_qrhunt configuration form.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class custom_completion extends activity_custom_completion{

    public function get_state(string $rule): int {
        $completions = $this->get_custom_rule_completions();
        if (isset($completions[$rule])) {
            return $completions[$rule];
        } else {
            throw new \coding_exception('Invalid rule specified: ' . $rule);
        }
    }

    public static function get_defined_custom_rules(): array
    {
        return [
            'answeredcorrectly',
        ];
    }

    public function get_custom_rule_descriptions(): array {
        return [
            'answeredcorrectly' => get_string('answeredcorrectly', 'mod_mymodule'),
        ];
    }

    public function get_sort_order(): array {
        return [
            'answeredcorrectly' => 1,
        ];
    }

    public function get_custom_rule_completions(): array {
        global $DB, $USER;
        $completions = [];
        // Check if the user has answered correctly
        if (has_user_answered_correctly($DB, $USER, $this->cm->instance)) {
            $completions['answeredcorrectly'] = COMPLETION_COMPLETE;
        } else {
            $completions['answeredcorrectly'] = COMPLETION_INCOMPLETE;
        }
        return $completions;
    }
}