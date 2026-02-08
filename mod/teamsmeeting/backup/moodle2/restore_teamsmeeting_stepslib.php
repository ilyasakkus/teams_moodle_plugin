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
 * Restore steps for mod_teamsmeeting.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one teamsmeeting activity.
 */
class restore_teamsmeeting_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore.
     *
     * @return array of restore_path_element
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('teamsmeeting', '/activity/teamsmeeting');
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data.
     *
     * @param array $data parsed element data
     */
    protected function process_teamsmeeting($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('teamsmeeting', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Define data processed after execution.
     */
    protected function after_execute() {
        $this->add_related_files('mod_teamsmeeting', 'intro', null);
    }
}
