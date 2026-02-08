<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Restore task for mod_teamsmeeting.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/teamsmeeting/backup/moodle2/restore_teamsmeeting_stepslib.php');

/**
 * Restore task for mod_teamsmeeting.
 */
class restore_teamsmeeting_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings.
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_teamsmeeting_activity_structure_step('teamsmeeting_structure', 'teamsmeeting.xml'));
    }

    /**
     * Define the contents in the activity that must be processed by the link decoder.
     *
     * @return array of restore_decode_content
     */
    public static function define_decode_contents() {
        $contents = [];
        $contents[] = new restore_decode_content('teamsmeeting', ['intro'], 'teamsmeeting');
        return $contents;
    }

    /**
     * Define the decoding rules for links belonging to the activity to be executed.
     *
     * @return array of restore_decode_rule
     */
    public static function define_decode_rules() {
        $rules = [];
        $rules[] = new restore_decode_rule('TEAMSMEETINGVIEWBYID', '/mod/teamsmeeting/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('TEAMSMEETINGINDEX', '/mod/teamsmeeting/index.php?id=$1', 'course');
        return $rules;
    }

    /**
     * Define the restore log rules.
     *
     * @return array of restore_log_rule
     */
    public static function define_restore_log_rules() {
        $rules = [];
        $rules[] = new restore_log_rule('teamsmeeting', 'add', 'view.php?id={course_module}', '{teamsmeeting}');
        $rules[] = new restore_log_rule('teamsmeeting', 'update', 'view.php?id={course_module}', '{teamsmeeting}');
        $rules[] = new restore_log_rule('teamsmeeting', 'view', 'view.php?id={course_module}', '{teamsmeeting}');
        return $rules;
    }

    /**
     * Define the restore log rules for course.
     *
     * @return array of restore_log_rule
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];
        $rules[] = new restore_log_rule('teamsmeeting', 'view all', 'index.php?id={course}', null);
        return $rules;
    }
}
