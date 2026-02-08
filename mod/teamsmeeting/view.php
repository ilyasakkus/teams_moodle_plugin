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
 * Displays the Teams meeting activity.
 *
 * Shows a join button that opens the Teams meeting in a new tab.
 * No iframes, no client-side API calls - fully compatible with all browsers.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course module ID.
$t = optional_param('t', 0, PARAM_INT);   // Teams meeting instance ID.

if ($id) {
    $cm = get_coursemodule_from_id('teamsmeeting', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $teamsmeeting = $DB->get_record('teamsmeeting', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($t) {
    $teamsmeeting = $DB->get_record('teamsmeeting', ['id' => $t], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $teamsmeeting->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('teamsmeeting', $teamsmeeting->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingidandcmid', 'mod_teamsmeeting');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/teamsmeeting:view', $context);

// Trigger course_module_viewed event.
$event = \mod_teamsmeeting\event\course_module_viewed::create([
    'objectid' => $teamsmeeting->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('teamsmeeting', $teamsmeeting);
$event->trigger();

// Completion.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Page setup.
$PAGE->set_url('/mod/teamsmeeting/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($teamsmeeting->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('mod-teamsmeeting-view');

echo $OUTPUT->header();

// Meeting info card.
echo '<div class="teamsmeeting-container">';
echo '<div class="teamsmeeting-card">';

// Card header.
echo '<div class="teamsmeeting-card-header">';
echo '<h2 class="teamsmeeting-title">' . format_string($teamsmeeting->name) . '</h2>';
echo '</div>';

echo '<div class="teamsmeeting-card-body">';

// Description.
if (!empty($teamsmeeting->intro)) {
    echo '<div class="teamsmeeting-description">';
    echo format_module_intro('teamsmeeting', $teamsmeeting, $cm->id);
    echo '</div>';
}

// Meeting schedule info.
if (!empty($teamsmeeting->meetingstart) || !empty($teamsmeeting->meetingend)) {
    echo '<div class="teamsmeeting-schedule">';

    if (!empty($teamsmeeting->meetingstart)) {
        echo '<div class="teamsmeeting-info-row">';
        echo '<span class="teamsmeeting-label">' . get_string('meetingstart', 'mod_teamsmeeting') . ':</span>';
        echo '<span class="teamsmeeting-value">';
        echo userdate($teamsmeeting->meetingstart, get_string('strftimedatetimeshort', 'langconfig'));
        echo '</span>';
        echo '</div>';
    }

    if (!empty($teamsmeeting->meetingend)) {
        echo '<div class="teamsmeeting-info-row">';
        echo '<span class="teamsmeeting-label">' . get_string('meetingend', 'mod_teamsmeeting') . ':</span>';
        echo '<span class="teamsmeeting-value">';
        echo userdate($teamsmeeting->meetingend, get_string('strftimedatetimeshort', 'langconfig'));
        echo '</span>';
        echo '</div>';
    }

    // Meeting status.
    $now = time();
    if (!empty($teamsmeeting->meetingstart) && !empty($teamsmeeting->meetingend)) {
        $statusclass = 'teamsmeeting-status-upcoming';
        $statustext = get_string('statusupcoming', 'mod_teamsmeeting');

        if ($now >= $teamsmeeting->meetingstart && $now <= $teamsmeeting->meetingend) {
            $statusclass = 'teamsmeeting-status-active';
            $statustext = get_string('statusactive', 'mod_teamsmeeting');
        } else if ($now > $teamsmeeting->meetingend) {
            $statusclass = 'teamsmeeting-status-ended';
            $statustext = get_string('statusended', 'mod_teamsmeeting');
        }

        echo '<div class="teamsmeeting-info-row">';
        echo '<span class="teamsmeeting-label">' . get_string('status') . ':</span>';
        echo '<span class="teamsmeeting-status ' . $statusclass . '">' . $statustext . '</span>';
        echo '</div>';
    }

    echo '</div>'; // End schedule.
}

// Join meeting section.
if (has_capability('mod/teamsmeeting:joinmeeting', $context)) {
    echo '<div class="teamsmeeting-join-section">';

    if (!empty($teamsmeeting->externalurl)) {
        // Direct link to Teams meeting - opens in new tab.
        // This is a simple <a> tag - NO iframes, NO fetch, NO PNA issues.
        echo '<a href="' . s($teamsmeeting->externalurl) . '" target="_blank" rel="noopener noreferrer" ' .
             'class="btn btn-primary btn-lg teamsmeeting-join-btn">';
        echo '<svg class="teamsmeeting-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">';
        echo '<path d="M19.27 7.04L15.5 9.5V7.5C15.5 6.95 15.05 6.5 14.5 6.5H7.5C6.95 6.5 6.5 6.95 ';
        echo '6.5 7.5V14.5C6.5 15.05 6.95 15.5 7.5 15.5H14.5C15.05 15.5 15.5 15.05 15.5 14.5V12.5L19.27 ';
        echo '14.96C19.72 15.24 20.29 14.92 20.29 14.39V7.61C20.29 7.08 19.72 6.76 19.27 7.04Z"/>';
        echo '</svg> ';
        echo get_string('joinmeeting', 'mod_teamsmeeting');
        echo '</a>';

        // Copy link.
        echo '<div class="teamsmeeting-copy-section mt-3">';
        echo '<div class="input-group" style="max-width:600px;margin:0 auto;">';
        echo '<input type="text" class="form-control" value="' . s($teamsmeeting->externalurl) . '" ';
        echo 'id="teamsmeeting-joinurl" readonly onclick="this.select()">';
        echo '<button class="btn btn-outline-secondary" type="button" id="copyBtn" ';
        echo 'onclick="navigator.clipboard.writeText(document.getElementById(\'teamsmeeting-joinurl\').value)';
        echo '.then(function(){document.getElementById(\'copyBtn\').textContent=\'';
        echo get_string('copied', 'mod_teamsmeeting') . '\';setTimeout(function(){document.getElementById(\'copyBtn\').textContent=\'';
        echo get_string('copylink', 'mod_teamsmeeting') . '\';},2000);})">';
        echo get_string('copylink', 'mod_teamsmeeting');
        echo '</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="teamsmeeting-help mt-3">';
        echo '<small class="text-muted">' . get_string('joinhelp', 'mod_teamsmeeting') . '</small>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">';
        echo get_string('nomeetingurl', 'mod_teamsmeeting');
        echo '</div>';
    }

    echo '</div>'; // End join section.
}

// Teacher management section.
if (has_capability('mod/teamsmeeting:managemeeting', $context)) {
    echo '<div class="teamsmeeting-manage-section mt-4">';
    echo '<h4>' . get_string('managemeeting', 'mod_teamsmeeting') . '</h4>';

    if (empty($teamsmeeting->externalurl)) {
        echo '<p class="text-warning">' . get_string('nourlyet', 'mod_teamsmeeting') . '</p>';
    }

    // Edit link.
    $editurl = new moodle_url('/course/modedit.php', ['update' => $cm->id, 'return' => 1]);
    echo '<a href="' . $editurl . '" class="btn btn-warning">';
    echo get_string('editmeetingurl', 'mod_teamsmeeting');
    echo '</a>';

    echo '</div>';
}

echo '</div>'; // End card body.
echo '</div>'; // End card.
echo '</div>'; // End container.

echo $OUTPUT->footer();
