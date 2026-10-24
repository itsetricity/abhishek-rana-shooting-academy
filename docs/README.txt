ABHISHEK RANA SHOOTING ACADEMY — WEBSITE & SUPERADMIN MVP

LOCAL WEBSITE
https://shooting-academy.test/
https://shooting-academy.test/superadmin

The project root is the document root. There is no nested public_html folder.
PHP 8.2+ is required, with PDO SQLite, mbstring, fileinfo and GD with WebP support.
No Node build, Composer installation or external database server is required.

ADMINISTRATOR
Username and password are in the protected root .env file, under ADMIN_USERNAME
and ADMIN_PASSWORD. This project's initial username is superadmin; the password
was randomly generated. Passwords may be plaintext in the protected .env or a
PHP password_hash() value. Never commit or expose .env.
To recover access, edit those variables on the server. No email reset service is
configured. Existing credentials are not changed by application startup.

WEBSITE CMS
Open Website CMS to edit:
- Homepage: hero, image, text, buttons, academy introduction and training journey.
- Brand & settings: academy identity, logo, contacts, social links, SEO and footer.
- Pages: editorial content, headings, introductions, SEO, privacy and website terms.
- Training programs, branches, coaches, achievements, gallery, notices and FAQs.
- Media library: JPG, PNG and WebP uploads, re-encoded to randomly named WebP files.

Use Draft for unfinished content. Choose Published and Save content to make an
item public. Saving homepage or brand settings publishes immediately. Lower
display-order numbers appear first. A stale content edit is rejected if another
window has already changed that record. Public-facing prose is plain text with
paragraph breaks; arbitrary HTML is escaped for safety.

Six draft branch records, one draft coach and one draft achievement are included.
Replace these with approved details. Add further branches without code changes.
Generic program descriptions are illustrative outlines, not confirmed offerings.
No fabricated athlete results, qualifications, contact numbers or addresses are
published. The website uses four real shooting-sport reference photos from Wikimedia Commons,
replaceable in the CMS. Sources and Creative Commons licences appear at /photo-credits.
The target symbol is a temporary identity, not an official academy logo.

ENQUIRIES AND DEMOS
Public forms save requests to SQLite and show a unique confirmation reference.
Requests do not create student accounts or confirm admission.
The inbox has newest-first expandable rows, status filters, search, unread counts,
Viewed-on-open, Contacted/Unread actions, per-recipient notification results,
follow-up notes, 15-row pagination and CSV export. CSV export includes all records
of the chosen request kind and neutralises spreadsheet formulas.
Demo requests support Requested, Confirmed, Rescheduled, Attended, No-show and
Cancelled. Confirmed/rescheduled bookings need a date, time and coach. The same
coach cannot be confirmed in two bookings at the exact same time. Durations and
broader overlapping-interval scheduling are for a later operations module.
Changes, including follow-up/booking notes, are preserved in the activity log.
Contact the applicant separately to communicate changes or confirm a session.

EMAIL
Email is disabled initially. Forms remain fully functional and show honest
Not configured / Disabled notification results in the inbox.
To enable server mail, set MAIL_ENABLED=true, MAIL_FROM and comma-separated
ENQUIRY_RECIPIENTS in .env, and configure the hosting server's PHP mail transport.
Only administrators receive new-request notifications; applicant acknowledgements,
SMS, WhatsApp and booking-update emails are not implemented in this MVP.
An accepted mail() result means accepted by the local mail server, not proven
delivery to a recipient. A dedicated SMTP/provider integration can be added later.

FILES AND DATA
app/                  Protected PHP application, templates and seed content
assets/               Public styles, scripts, self-hosted fonts and images
uploads/              Public re-encoded images; script execution denied
storage/academy.sqlite Protected content, inbox, audit, media and rate-limit data
storage/*.log         Protected runtime logs
docs/, tests/         Denied to web requests
.env                  Protected runtime configuration and administrator credentials
.htaccess             Apache routing and access restrictions
index.php             Application front controller
router.php            Optional PHP development-server router

DEPLOYMENT TO SHARED HOSTING
Copy this directory's application contents directly into the host's document root,
including hidden .htaccess rules. Do not add another nested public_html directory.
Keep app, storage, uploads and .env at the same relative paths as locally.
Require Apache 2.4 with mod_rewrite and AllowOverride All, or translate these access
rules into the hosting server's equivalent configuration before serving traffic.
Set APP_URL to the HTTPS production origin and APP_ENV=production after all
generic content, privacy terms, real contacts and email delivery are reviewed.
Until then, the site emits noindex and robots.txt blocks crawling.
storage/ and uploads/ must be writable by PHP. The code and .env should not be
world-writable; .env must be readable only by its owner / intended PHP process.
Do not upload the proposal, tests or documentation to a production site unless
needed. Their web access is blocked locally and by the supplied Apache rules.

Before launch, verify /.env, /app/bootstrap.php, /storage/academy.sqlite and the
proposal PDF return 403. Test an enquiry and check the inbox. Configure hosting
backups for the entire SQLite database and uploads together, plus .env separately
in an encrypted backup. Exercise a restore before collecting real student data.
Avoid copying a database during writes; use SQLite's backup API or a maintenance
window. Do not reset, delete or reseed the live database when updating code.

LOCAL DEVELOPMENT
php -S 127.0.0.1:8094 router.php
The local shooting-academy.test Apache virtual host points directly to this
project root and uses the existing local PHP-FPM service at port 9000. Its
machine-specific configuration and installer scripts are excluded from Git.
When cloning elsewhere, configure a vhost whose document root is the checkout,
enable the supplied .htaccess rules, and connect the host’s PHP runtime. The
existing local vhost requires local connections and redirects HTTP to HTTPS.

TESTING
Run the isolated integration test with the bundled runtime (or Python with Pillow):
python3 tests/integration.py
It starts a temporary HTTP server, uses a temporary database, checks authentication,
CMS publishing, draft visibility, form persistence, inbox actions, safe CSV export,
demo conflicts, upload processing, and private-file denial. It removes its image
uploads. It must be run with MAIL_ENABLED=false. It does not alter the live database.
Current validated run: 75 passing HTTP checks, plus desktop/mobile browser review.

MVP BOUNDARY
This release is the public website and superadmin CMS/inbox foundation requested
first. Student and coach accounts, branch-manager permissions, admission approval,
fees/payments, biometric attendance and performance portals remain subsequent
phases from the proposal. No inactive future portal links masquerade as working
features. Publication to an external hosting provider is not part of the local vhost.

Research sources and image-generation brief are preserved alongside this file.
