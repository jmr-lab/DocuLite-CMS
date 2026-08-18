# DocuLite CMS

DocuLite CMS is a lightweight, Documentum-inspired content management system. It was initially developed in 2005, maintained and regularly upgraded until 2016, and is now archived on GitHub in read-only form.

The core component responsible for database querying—**JMFC**—remains relevant and is intended to be preserved as a stable backend layer. However, the user interface requires a major rework to become mobile friendly (e.g., using Bootstrap and/or Angular). Additional work is also required to secure the application by applying modern security best practices and integrating a suitable security approach/framework where appropriate.

## Repository status

- **Project status:** Archived (read-only)
- **Last active maintenance:** 2016
- **Core kept:** JMFC (querying/backend layer)
- **Needs work (future effort):**
  - UI modernization (mobile friendly)
  - Application hardening / security improvements

## Key concepts

- **JMFC:** Core database querying component (Documentum DFC-like concept).
- **UI layer:** Requires modernization and responsive redesign.
- **Security:** Requires additional hardening to meet modern baseline security expectations.

## Getting started (archive)

This repository is provided as an archive. There is no guarantee that it will run on modern PHP/MySQL stacks without changes.

If you decide to resume development, typical next steps would include:
1. Review current codebase and dependencies
2. Determine a target runtime (PHP version, web server, DB version)
3. Modernize the UI to support mobile devices
4. Apply security hardening (authentication/authorization, input validation, CSRF protection, session hardening, and other standard protections)

## Screenshots

<div align="center">
  <img src="screenshots/clipboard.jpg" width="80%" />
  <div>Clipboard view / copy-like functionality.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/contextual_menu.jpg" width="80%" />
  <div>Contextual (right-click) menu.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/customize.jpg" width="80%" />
  <div>Customization screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/details.jpg" width="80%" />
  <div>Details panel / item details view.</div>
</div>
<br>

<!--div align="center">
  <img src="screenshots/doclist.jpg" width="80%" />
  <div>Document list view.</div>
</div>
<br-->

<div align="center">
  <img src="screenshots/documentum6.jpg" width="80%" />
  <div>Documentum integration / Documentum 6-related UI.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/drag.jpg" width="80%" />
  <div>Drag-and-drop interaction.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/import.jpg" width="80%" />
  <div>Import screen.</div>
</div>
<br>

<!--div align="center">
  <img src="screenshots/java.JPG" width="80%" />
  <div>Java-related configuration or screen.</div>
</div>
<br-->

<div align="center">
  <img src="screenshots/jmfc-dfc.jpg" width="80%" />
  <div>JMFC vs DFC mapping / related UI view.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/lightweight.jpg" width="80%" />
  <div>Lightweight mode / lightweight interface.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/logout.jpg" width="80%" />
  <div>Logout screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/mails.jpg" width="80%" />
  <div>Mails / email-related functionality.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/modal.jpg" width="80%" />
  <div>Modal dialog UI.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/multiple_selection.jpg" width="80%" />
  <div>Multiple selection behavior.</div>
</div>
<br>

<!--div align="center">
  <img src="screenshots/php.JPG" width="80%" />
  <div>PHP-related configuration or screen.</div>
</div>
<br-->

<div align="center">
  <img src="screenshots/security.jpg" width="80%" />
  <div>Security settings / security screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/templates.jpg" width="80%" />
  <div>Templates management screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/templates_new.jpg" width="80%" />
  <div>New templates creation/editing screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/thumbnails.jpg" width="80%" />
  <div>Thumbnail view.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/user_access.jpg" width="80%" />
  <div>User access permissions.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/user_management.jpg" width="80%" />
  <div>User management screen.</div>
</div>
<br>

<div align="center">
  <img src="screenshots/workflows.jpg" width="80%" />
  <div>Workflows screen.</div>
</div>

## License

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at:

- https://www.apache.org/licenses/LICENSE-2.0
