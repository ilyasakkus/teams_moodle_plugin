# mod_teamsmeeting - Microsoft Teams Meeting for Moodle

Moodle activity module that creates Microsoft Teams meetings directly from your courses. **Works on ALL browsers** including Chrome on university/corporate networks.

## Why This Plugin?

The existing [`mod_msteams`](https://moodle.org/plugins/mod_msteams) plugin loads the meeting creation app inside an **iframe**. This breaks on Chrome due to:

- **Third-party cookie blocking** — Chrome blocks cookies in iframes, so Microsoft OAuth login fails
- **PNA (Private Network Access)** — Chrome restricts cross-origin iframe communication on private networks (university/corporate LANs)
- **CORS errors** — reported by users on Moodle 5.1.1 ([source](https://moodle.org/plugins/mod_msteams))

This plugin solves all of these by using a **popup window** instead of an iframe.

## How It Works

```
┌─────────────────────────────────────────────────────┐
│  Moodle Course → Add Activity → Teams Meeting       │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │  "Teams Toplantısı Oluştur" button            │  │
│  └────────────────────┬──────────────────────────┘  │
│                       │ window.open()                │
│                       ▼                              │
│  ┌───────────────────────────────────────────────┐  │
│  │  POPUP WINDOW (popup.php)                     │  │
│  │  ┌─────────────────────────────────────────┐  │  │
│  │  │  Enovation Meeting App (iframe)         │  │  │
│  │  │  • Sign in with Microsoft account       │  │  │
│  │  │  • Create meeting                       │  │  │
│  │  │  • Copy meeting link                    │  │  │
│  │  └─────────────────────────────────────────┘  │  │
│  │  ┌─────────────────────────────────────────┐  │  │
│  │  │  Click here after copying the link      │  │  │
│  │  │  → reads clipboard → captures URL       │  │  │
│  │  └─────────────────────────────────────────┘  │  │
│  │  → sends URL to Moodle form via postMessage   │  │
│  │  → popup closes automatically                 │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  Meeting URL is saved. Students see "Join" button.  │
└─────────────────────────────────────────────────────┘
```

**Why the popup approach works:**

| Problem | iframe (mod_msteams) | Popup (this plugin) |
|---------|---------------------|---------------------|
| Cookie context | Third-party (blocked by Chrome) | **First-party** (works) |
| OAuth login | Fails in Chrome | **Works everywhere** |
| PNA restriction | Affected | **Not affected** |
| CORS | Can break postMessage | **No issues** |

## Screenshots

### Activity Chooser
The plugin appears in the activity chooser with the Microsoft Teams icon on a purple background.

### Meeting Creation (Popup)
A popup window opens with the Enovation meeting app. Teachers sign in and create meetings. After copying the meeting link, one click captures the URL.

### Student View
Students see a clean card with meeting info and a "Join Teams Meeting" button that opens Teams in a new tab.

## Requirements

| Requirement | Version |
|-------------|---------|
| Moodle | 4.5+ / 5.x (tested on 5.1.1) |
| PHP | 8.1+ |
| Browser | Chrome, Firefox, Safari, Edge |

## Installation

### Option 1: Git Clone

```bash
cd /path/to/moodle/mod
git clone https://github.com/YOUR_USERNAME/moodle-mod_teamsmeeting.git teamsmeeting
```

### Option 2: Download ZIP

1. Download the latest release ZIP
2. Extract to `/path/to/moodle/mod/teamsmeeting`

### Option 3: Manual Copy

```bash
cp -r mod/teamsmeeting /path/to/moodle/mod/teamsmeeting
```

### Finalize

1. Log in to Moodle as admin
2. Go to **Site Administration → Notifications**
3. Follow the upgrade prompts
4. Go to **Site Administration → Plugins → Activity modules → Teams Meeting** to configure

## Configuration

### Plugin Settings

Navigate to **Site Administration → Plugins → Activity modules → Teams Meeting**

| Setting | Description | Default |
|---------|-------------|---------|
| Meeting App URL | URL of the Enovation meeting creation app | `https://enomsteams.z16.web.core.windows.net` |
| Open in New Tab | Open meeting links in a new tab for students | Yes |

> **Note:** The default Meeting App URL uses the Enovation-hosted app on Azure. The older URL `https://www.enovation.ie/msteams/` also works but may have SSL certificate issues. You can self-host the meeting app if needed.

### No Azure AD Setup Required

Unlike plugins that use the Microsoft Graph API directly, this plugin delegates authentication to the Enovation meeting app. Teachers sign in with their own Microsoft accounts through the app. **No Azure AD app registration, client secrets, or admin consent required.**

## Usage

### For Teachers

1. Navigate to your course
2. Turn editing on → **Add an activity or resource** → **Teams Meeting**
3. Enter a meeting name
4. Click **"Teams Toplantısı Oluştur"** (Create Teams Meeting)
5. A popup opens with the Enovation meeting app:
   - Sign in with your Microsoft account
   - Create the meeting
   - Click **Copy** to copy the meeting link
6. Click the bottom bar in the popup → URL is captured → popup closes
7. The meeting URL appears in the form → Click **Save**

> **Manual alternative:** Click "Or enter the meeting URL manually" to paste a URL directly without using the popup.

### For Students

1. Click on the Teams Meeting activity in the course
2. Click **"Join Teams Meeting"** button
3. Teams opens in a new tab — join via web or desktop app

## Comparison with mod_msteams

| Feature | mod_msteams | mod_teamsmeeting |
|---------|-------------|------------------|
| Meeting creation | iframe (broken on Chrome) | **Popup window (all browsers)** |
| Chrome on university networks | ❌ Fails (PNA/cookies) | ✅ Works |
| Moodle 5.1.1 | ❌ CORS errors reported | ✅ Tested and working |
| Requires tiny_teamsmeeting | Yes (Moodle 5.0+) | **No** |
| Azure AD setup | No | **No** |
| Manual URL entry | No | **Yes (fallback)** |
| Turkish language | No | **Yes** |
| Backup/Restore | Partial | **Full** |
| GDPR/Privacy | N/A | **Compliant (null provider)** |

## File Structure

```
mod/teamsmeeting/
├── version.php              # Plugin version
├── lib.php                  # Core library functions
├── mod_form.php             # Activity creation form
├── popup.php                # Meeting creation popup (PNA bypass)
├── view.php                 # Student view page
├── index.php                # Course module index
├── settings.php             # Admin settings
├── styles.css               # CSS styles
├── classes/
│   ├── privacy/provider.php # GDPR compliance
│   └── event/               # Event classes
├── db/
│   ├── install.xml          # Database schema
│   └── access.php           # Capabilities
├── backup/moodle2/          # Backup and restore
├── lang/
│   ├── en/teamsmeeting.php  # English strings
│   └── tr/teamsmeeting.php  # Turkish strings
└── pix/                     # Icons (SVG + PNG)
```

## Troubleshooting

### Popup is blocked
Your browser's popup blocker may prevent the meeting creation window from opening. Allow popups for your Moodle site.

### URL not auto-captured
After creating the meeting, copy the link in the Enovation app, then click the bottom bar in the popup. If clipboard reading fails, paste the URL manually with Ctrl+V.

### Enovation app not loading
Check the Meeting App URL in plugin settings. The default `https://enomsteams.z16.web.core.windows.net` should work. If not, try `https://www.enovation.ie/msteams/`.

### Icon not showing in activity chooser
Run **Site Administration → Development → Purge all caches** after installation. Moodle caches icons aggressively.

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

## Credits

- Inspired by [mod_msteams](https://moodle.org/plugins/mod_msteams) by Robert Schrenk, Zentrum für Lernmanagement, and Andreas Riepl
- Uses the [Enovation Teams Meeting App](https://enomsteams.z16.web.core.windows.net) for meeting creation

## License

This plugin is licensed under the [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html).

```
Copyright (C) 2026

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
```
