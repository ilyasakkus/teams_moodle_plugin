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
 * Library of interface functions and constants.
 *
 * This plugin works like the URL module but captures the meeting URL
 * from the Enovation Teams meeting app via a popup window approach
 * (bypassing Chrome PNA and third-party cookie issues).
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Supported features.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know.
 */
function teamsmeeting_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COLLABORATION;
        default:
            return null;
    }
}

/**
 * Add a new teamsmeeting instance.
 *
 * @param stdClass $data Form data
 * @param mod_teamsmeeting_mod_form $mform The form
 * @return int The instance id of the new teamsmeeting
 */
function teamsmeeting_add_instance($data, $mform = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();

    // The externalurl field contains the Teams meeting URL
    // captured from the popup or entered manually.
    if (empty($data->externalurl)) {
        $data->externalurl = '';
    }

    $data->id = $DB->insert_record('teamsmeeting', $data);

    return $data->id;
}

/**
 * Update a teamsmeeting instance.
 *
 * @param stdClass $data Form data
 * @param mod_teamsmeeting_mod_form $mform The form
 * @return bool True on success
 */
function teamsmeeting_update_instance($data, $mform = null) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    if (empty($data->externalurl)) {
        // Preserve existing URL if form doesn't have one.
        $existing = $DB->get_record('teamsmeeting', ['id' => $data->id], 'externalurl');
        if ($existing && !empty($existing->externalurl)) {
            $data->externalurl = $existing->externalurl;
        }
    }

    return $DB->update_record('teamsmeeting', $data);
}

/**
 * Delete a teamsmeeting instance.
 *
 * @param int $id Id of the module instance
 * @return bool True on success
 */
function teamsmeeting_delete_instance($id) {
    global $DB;

    if (!$DB->get_record('teamsmeeting', ['id' => $id])) {
        return false;
    }

    $DB->delete_records('teamsmeeting', ['id' => $id]);

    return true;
}

/**
 * Given a course_module object, this function returns any "extra" information
 * that may be needed when printing this activity in a course listing.
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info|null
 */
function teamsmeeting_get_coursemodule_info($coursemodule) {
    global $DB;

    $teamsmeeting = $DB->get_record('teamsmeeting', ['id' => $coursemodule->instance],
        'id, name, intro, introformat, externalurl, meetingstart, meetingend');

    if (!$teamsmeeting) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $teamsmeeting->name;

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('teamsmeeting', $teamsmeeting, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Callback to extend navigation.
 */
function teamsmeeting_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // Nothing to extend.
}

/**
 * Callback for resetting course data.
 */
function teamsmeeting_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'teamsmeetingheader', get_string('modulenameplural', 'mod_teamsmeeting'));
    $mform->addElement('checkbox', 'reset_teamsmeeting', get_string('resetmeetings', 'mod_teamsmeeting'));
}

/**
 * Course reset handler.
 */
function teamsmeeting_reset_userdata($data) {
    global $DB;

    $status = [];

    if (!empty($data->reset_teamsmeeting)) {
        $sql = "UPDATE {teamsmeeting} SET externalurl = '' WHERE course = :courseid";
        $DB->execute($sql, ['courseid' => $data->courseid]);

        $status[] = [
            'component' => get_string('modulenameplural', 'mod_teamsmeeting'),
            'item' => get_string('resetmeetings', 'mod_teamsmeeting'),
            'error' => false,
        ];
    }

    return $status;
}
