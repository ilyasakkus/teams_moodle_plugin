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
 * Popup page - embeds Teams meeting creation app.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_login();
require_sesskey();

$meetingappurl = get_config('mod_teamsmeeting', 'meetingappurl');
if (empty($meetingappurl)) {
    $meetingappurl = 'https://enomsteams.z16.web.core.windows.net';
}
$meetingappurl = clean_param($meetingappurl, PARAM_URL);

$parsedurl = parse_url($CFG->wwwroot);
$moodleorigin = $parsedurl['scheme'] . '://' . $parsedurl['host'];
if (!empty($parsedurl['port'])) {
    $moodleorigin .= ':' . $parsedurl['port'];
}

?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teams Meeting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f0f0f0;
        }

        /* Top bar */
        .topbar {
            background: #6264A7;
            color: #fff;
            padding: 7px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            font-size: 13px;
        }
        .topbar-left { font-weight: 600; }

        /* iframe */
        .frame-wrap {
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        .frame-wrap iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* Bottom capture bar - BIG click target */
        .capture-bar {
            flex-shrink: 0;
            border-top: 3px solid #6264A7;
            cursor: pointer;
            transition: background 0.2s;
            user-select: none;
        }

        /* State 1: Waiting - big click target */
        .capture-waiting {
            background: #f8f9fa;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .capture-waiting:hover {
            background: #e8f0fe;
        }
        .capture-waiting:active {
            background: #d2e3fc;
        }
        .capture-icon {
            width: 40px;
            height: 40px;
            background: #6264A7;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .capture-icon svg { fill: #fff; }
        .capture-text h4 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 0 0 2px 0;
        }
        .capture-text p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        .capture-pulse {
            width: 10px;
            height: 10px;
            background: #6264A7;
            border-radius: 50%;
            margin-left: auto;
            animation: pulse 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        /* State 2: URL detected - confirmation */
        .capture-found {
            background: #d4edda;
            padding: 12px 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .capture-found.show { display: flex; }
        .capture-found input {
            flex: 1;
            padding: 8px 12px;
            border: 2px solid #28a745;
            border-radius: 6px;
            font-size: 12px;
            background: #fff;
        }
        .capture-found .btn-confirm {
            padding: 8px 20px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }
        .capture-found .btn-confirm:hover { background: #218838; }

        /* Success overlay */
        .success-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(40, 167, 69, 0.96);
            color: #fff;
            z-index: 1000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .success-overlay.show { display: flex; }
        .checkmark {
            width: 80px; height: 80px;
            border: 4px solid #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: scaleIn 0.3s ease;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
        .success-overlay h2 { font-size: 24px; margin-bottom: 8px; }
        .success-overlay p { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>

    <div class="topbar">
        <span class="topbar-left">Teams Meeting</span>
    </div>

    <div class="frame-wrap">
        <iframe id="meetingFrame" src="<?php echo s($meetingappurl); ?>"
                allow="camera; microphone; display-capture; clipboard-write; clipboard-read"></iframe>
    </div>

    <!-- Bottom bar: click to capture -->
    <div class="capture-bar" id="captureBar">
        <!-- State 1: Waiting for URL -->
        <div class="capture-waiting" id="stateWaiting" onclick="captureClick()">
            <div class="capture-icon">
                <svg viewBox="0 0 24 24" width="22" height="22">
                    <path d="M19 20H5V4h2v7l2.5-1.5L12 11V4h3.17L19 7.83V20zm0-14.17L16.17 2H5C3.9 2 3 2.9 3 4v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7.83c0-.53-.21-1.04-.59-1.41L19 5.83z"/>
                </svg>
            </div>
            <div class="capture-text">
                <h4>Toplantı linkini kopyaladıktan sonra buraya tıklayın</h4>
                <p>Enovation uygulamasında "Copy" butonuna basın, ardından bu alana tıklayın</p>
            </div>
            <div class="capture-pulse"></div>
        </div>

        <!-- State 2: URL found -->
        <div class="capture-found" id="stateFound">
            <input type="text" id="foundUrl" readonly>
            <button class="btn-confirm" onclick="confirmUrl()">
                ✓ Kullan ve Kapat
            </button>
        </div>
    </div>

    <!-- Success overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="checkmark">
            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#fff" stroke-width="3">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <h2>URL Yakalandı!</h2>
        <p>Pencere kapanıyor...</p>
    </div>

    <script>
    (function() {
        var captured = false;
        var openerOrigin = <?php echo json_encode($moodleorigin); ?>;
        var initialClipboard = '';

        // Save initial clipboard to ignore old URLs.
        if (navigator.clipboard && navigator.clipboard.readText) {
            navigator.clipboard.readText().then(function(t) {
                initialClipboard = (t || '').trim();
            }).catch(function() {});
        }

        // ================================================================
        // 1) postMessage from iframe → AUTO capture + close
        // ================================================================
        window.addEventListener('message', function(event) {
            if (captured) return;
            var url = extractTeamsUrl(event.data);
            if (url) {
                sendAndClose(url);
                return;
            }
            if (typeof event.data === 'string' && event.data.length > 20) {
                var m = event.data.match(/https?:\/\/[^\s"'<>]*teams\.microsoft\.com[^\s"'<>]*/i);
                if (m) sendAndClose(m[0]);
            }
        });

        // ================================================================
        // 2) CLICK on capture bar → read clipboard → show URL
        //    This is the MAIN flow. One click after copying.
        // ================================================================
        window.captureClick = function() {
            if (captured) return;

            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    text = (text || '').trim();

                    if (isTeamsUrl(text) && text !== initialClipboard) {
                        // Found new Teams URL! Show confirmation.
                        showFound(text);
                    } else if (isTeamsUrl(text)) {
                        // Same as initial - might be same meeting, show anyway.
                        showFound(text);
                    } else {
                        // No Teams URL in clipboard. Prompt manual paste.
                        promptManual();
                    }
                }).catch(function() {
                    promptManual();
                });
            } else {
                promptManual();
            }
        };

        // ================================================================
        // 3) Confirm button → send + close
        // ================================================================
        window.confirmUrl = function() {
            var url = document.getElementById('foundUrl').value.trim();
            if (isTeamsUrl(url)) sendAndClose(url);
        };

        // ================================================================
        // Show URL found state
        // ================================================================
        function showFound(url) {
            document.getElementById('foundUrl').value = url;
            document.getElementById('stateWaiting').style.display = 'none';
            document.getElementById('stateFound').classList.add('show');
        }

        // Prompt for manual paste
        function promptManual() {
            var text = document.getElementById('stateWaiting').querySelector('.capture-text');
            text.innerHTML = '<h4 style="color:#dc3545">Clipboard\'da Teams URL\'si bulunamadı</h4>' +
                '<p>Enovation uygulamasında linki kopyalayıp tekrar tıklayın. ' +
                'Veya Ctrl+V ile aşağıya yapıştırın:</p>' +
                '<input type="text" id="manualInput" ' +
                'style="width:100%;padding:6px 10px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:6px" ' +
                'placeholder="https://teams.microsoft.com/meet/..." ' +
                'onpaste="setTimeout(function(){checkManual()},50)" ' +
                'onkeydown="if(event.key===\'Enter\'){event.preventDefault();checkManual()}">';

            // Re-enable clicking to retry.
            setTimeout(function() {
                text.querySelector('h4').style.color = '#333';
                text.querySelector('h4').textContent = 'Toplantı linkini kopyaladıktan sonra buraya tıklayın';
                var input = document.getElementById('manualInput');
                if (input) input.focus();
            }, 3000);
        }

        // Check manual input
        window.checkManual = function() {
            var input = document.getElementById('manualInput');
            if (!input) return;
            var url = input.value.trim();
            if (isTeamsUrl(url)) {
                showFound(url);
            }
        };

        // ================================================================
        // Send URL to opener + close popup
        // ================================================================
        function sendAndClose(url) {
            if (captured) return;
            captured = true;

            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: 'teamsmeeting_url',
                    url: url
                }, openerOrigin);
            }

            document.getElementById('successOverlay').classList.add('show');
            setTimeout(function() { window.close(); }, 1500);
        }

        // ================================================================
        // Helpers
        // ================================================================
        function isTeamsUrl(s) {
            if (!s) return false;
            return s.indexOf('teams.microsoft.com/meet') !== -1 ||
                   s.indexOf('teams.microsoft.com/l/meetup-join') !== -1 ||
                   s.indexOf('teams.live.com') !== -1;
        }

        function extractTeamsUrl(data) {
            if (!data) return null;
            if (typeof data === 'string') {
                if (isTeamsUrl(data)) return data.trim();
                try { data = JSON.parse(data); } catch(e) { return null; }
            }
            if (typeof data === 'object' && data !== null) {
                var fields = ['url','meetingUrl','meetingurl','joinUrl','joinurl',
                              'joinWebUrl','link','meetingLink','value','data','href'];
                for (var i = 0; i < fields.length; i++) {
                    var v = data[fields[i]];
                    if (v && typeof v === 'string' && isTeamsUrl(v)) return v.trim();
                }
                for (var key in data) {
                    if (!data.hasOwnProperty(key)) continue;
                    if (typeof data[key] === 'string' && isTeamsUrl(data[key])) return data[key].trim();
                    if (typeof data[key] === 'object') {
                        var f = extractTeamsUrl(data[key]);
                        if (f) return f;
                    }
                }
            }
            return null;
        }
    })();
    </script>
</body>
</html>
