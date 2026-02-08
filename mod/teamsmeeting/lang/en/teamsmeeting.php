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
 * English language strings for mod_teamsmeeting.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General.
$string['modulename'] = 'Teams Meeting';
$string['modulenameplural'] = 'Teams Meetings';
$string['modulename_help'] = 'The Teams Meeting activity allows teachers to schedule Microsoft Teams meetings directly from Moodle. Unlike other plugins, this one works on ALL browsers including Chrome on university/corporate networks by avoiding iframe-based meeting creation (PNA/CORS safe).';
$string['pluginname'] = 'Teams Meeting';
$string['pluginadministration'] = 'Teams Meeting administration';
$string['teamsmeeting:addinstance'] = 'Add a new Teams Meeting activity';
$string['teamsmeeting:view'] = 'View Teams Meeting';
$string['teamsmeeting:joinmeeting'] = 'Join Teams Meeting';
$string['teamsmeeting:managemeeting'] = 'Manage Teams Meeting';

// Settings.
$string['meetingappsettings'] = 'Meeting App Settings';
$string['meetingappsettings_desc'] = 'Configure the external meeting app used to create Teams meetings. The app opens in a popup window (not an iframe) to bypass Chrome PNA and third-party cookie restrictions.';
$string['meetingappurl'] = 'Meeting App URL';
$string['meetingappurl_desc'] = 'URL of the Teams meeting creation app. Default is the Enovation app hosted on Azure. You can also self-host this app. <br><strong>Default:</strong> <code>https://enomsteams.z16.web.core.windows.net</code><br><strong>Alternative:</strong> <code>https://www.enovation.ie/msteams/</code>';
$string['displaysettings'] = 'Display Settings';
$string['displaysettings_desc'] = 'How meeting links are displayed to students.';
$string['openinnewtab'] = 'Open in New Tab';
$string['openinnewtab_desc'] = 'Always open meeting links in a new browser tab (recommended for best compatibility).';
$string['pnainfo'] = 'PNA/CORS Compatibility';
$string['pnainfo_desc'] = 'This plugin uses a <strong>popup window</strong> approach instead of iframes to create meetings. This bypasses Chrome\'s Private Network Access (PNA) restrictions and third-party cookie blocking that cause other Teams plugins to fail on university/corporate networks. <br><br><strong>How it works:</strong><ul><li>Teacher clicks "Create Meeting" - a popup window opens</li><li>Inside the popup, the meeting app runs as a first-party context (cookies work normally)</li><li>After meeting creation, the URL is automatically captured via postMessage</li><li>If auto-capture fails, teachers can paste the URL manually</li></ul>';

// Form.
$string['meetingname'] = 'Meeting Name';
$string['meetinglink'] = 'Meeting Link';
$string['meetingschedule'] = 'Meeting Schedule';
$string['meetingstart'] = 'Start Time';
$string['meetingend'] = 'End Time';
$string['createmeetingbtn'] = 'Create Teams Meeting';
$string['createmeetinghelp'] = 'Opens the Teams meeting app in a popup window. Sign in with your Microsoft account, create a meeting, and the URL will be captured automatically.';
$string['enterurlmanually'] = 'Or enter the meeting URL manually...';
$string['useurl'] = 'Use This URL';
$string['changeurl'] = 'Change';
$string['urlset'] = 'Meeting URL is set!';

// Popup page.
$string['waitingforurl'] = 'Waiting for meeting URL...';
$string['urlcaptured'] = 'Meeting URL captured! This window will close automatically.';
$string['urlcapturedshort'] = 'URL Captured!';
$string['invalidteamsurl'] = 'Please enter a valid Microsoft Teams meeting URL (must contain teams.microsoft.com or teams.live.com).';
$string['errorendbeforestart'] = 'Meeting end time must be after start time.';

// View page.
$string['joinmeeting'] = 'Join Teams Meeting';
$string['copylink'] = 'Copy Link';
$string['copied'] = 'Copied!';
$string['joinhelp'] = 'Clicking the button opens Microsoft Teams in a new tab. You can join via the web app or desktop application. Works on all browsers.';
$string['nomeetingurl'] = 'The meeting link has not been set yet. Please contact your instructor.';
$string['managemeeting'] = 'Meeting Management';
$string['editmeetingurl'] = 'Edit Meeting URL';
$string['nourlyet'] = 'No meeting URL has been set. Click "Edit Meeting URL" to create or paste one.';

// Status.
$string['statusupcoming'] = 'Upcoming';
$string['statusactive'] = 'In Progress';
$string['statusended'] = 'Ended';

// Index page.
$string['nomeetings'] = 'There are no Teams Meetings in this course.';

// Reset.
$string['resetmeetings'] = 'Reset all meeting links';

// Privacy.
$string['privacy:metadata'] = 'The Teams Meeting plugin does not store any personal user data. Meeting links are stored at the activity level.';

// Errors.
$string['missingidandcmid'] = 'Missing course module ID or instance ID.';

// Events.
$string['eventcoursemoduleviewed'] = 'Teams Meeting viewed';
