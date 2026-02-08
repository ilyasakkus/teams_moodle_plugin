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
 * Display information about all teamsmeeting activities in a course.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_url('/mod/teamsmeeting/index.php', ['id' => $id]);
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_teamsmeeting'));

$teamsmeetings = get_all_instances_in_course('teamsmeeting', $course);

if (empty($teamsmeetings)) {
    notice(get_string('nomeetings', 'mod_teamsmeeting'),
        new moodle_url('/course/view.php', ['id' => $course->id]));
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_' . $course->format);
    $table->head = [$strsectionname,
        get_string('meetingname', 'mod_teamsmeeting'),
        get_string('meetingstart', 'mod_teamsmeeting'),
        get_string('status'),
    ];
    $table->align = ['center', 'left', 'left', 'center'];
} else {
    $table->head = [
        get_string('meetingname', 'mod_teamsmeeting'),
        get_string('meetingstart', 'mod_teamsmeeting'),
        get_string('status'),
    ];
    $table->align = ['left', 'left', 'center'];
}

$now = time();

foreach ($teamsmeetings as $teamsmeeting) {
    $cm = get_coursemodule_from_instance('teamsmeeting', $teamsmeeting->id, $course->id);
    $link = html_writer::link(
        new moodle_url('/mod/teamsmeeting/view.php', ['id' => $cm->id]),
        format_string($teamsmeeting->name)
    );

    $startdate = !empty($teamsmeeting->meetingstart) ?
        userdate($teamsmeeting->meetingstart, get_string('strftimedatetimeshort', 'langconfig')) : '-';

    // Status.
    $statushtml = '';
    if (!empty($teamsmeeting->externalurl)) {
        if (!empty($teamsmeeting->meetingstart) && !empty($teamsmeeting->meetingend)) {
            if ($now >= $teamsmeeting->meetingstart && $now <= $teamsmeeting->meetingend) {
                $statushtml = '<span class="badge badge-success bg-success">' .
                    get_string('statusactive', 'mod_teamsmeeting') . '</span>';
            } else if ($now > $teamsmeeting->meetingend) {
                $statushtml = '<span class="badge badge-secondary bg-secondary">' .
                    get_string('statusended', 'mod_teamsmeeting') . '</span>';
            } else {
                $statushtml = '<span class="badge badge-info bg-info">' .
                    get_string('statusupcoming', 'mod_teamsmeeting') . '</span>';
            }
        } else {
            $statushtml = '<span class="badge badge-success bg-success">&#10003;</span>';
        }
    } else {
        $statushtml = '<span class="badge badge-warning bg-warning">-</span>';
    }

    $row = [];
    if ($usesections) {
        $row[] = get_section_name($course, $teamsmeeting->section);
    }
    $row[] = $link;
    $row[] = $startdate;
    $row[] = $statushtml;

    $table->data[] = $row;
}

echo html_writer::table($table);
echo $OUTPUT->footer();
