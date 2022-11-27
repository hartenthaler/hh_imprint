# webtrees module hh_imprint

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)

![webtrees major version](https://img.shields.io/badge/webtrees-v2.1.x-green)

![Latest Release](https://img.shields.io/github/v/release/hartenthaler/hh_imprint)

This [webtrees](https://www.webtrees.net) module creates an imprint notice in the footer of the web page.

<a name="Contents"></a>
## Contents

This Readme contains the following main sections

* [Description](#description)
* [Screenshots](#screenshots)
* [Requirements](#requirements)
* [Installation](#installation)
* [Upgrade](#upgrade)
* [Translation](#translation)
* [Contact Support](#support)
* [License](#license)

<a name="description"></a>
## Description

This module adds an imprint notice to all pages of a webtrees site.

The admin can define the following data fields in the control panel for the responsible person
* name
* address
* phone and fax numbers
* E-Mail address

<a name="screenshots"></a>
## Screenshots

Screenshot of control panel page
<p align="center"><img src="docs/screenshot.png" alt="Screenshot of control panel menu" align="center" width="80%"></p>

<a name="requirements"></a>
## Requirements

This module requires **webtrees** version 2.1 or later.
This module has the same requirements as [webtrees#system-requirements](https://github.com/fisharebest/webtrees#system-requirements).

This module was tested with **webtrees** 2.1.7 version
and all available themes and all other custom modules.

<a name="installation"></a>
## Installation

This section documents installation instructions for this module.

1. Make database backup
2. Download the [latest release](https://github.com/hartenthaler/hh_imprint/releases/latest)
3. Unzip the package into your `webtrees/modules_v4` directory of your web server
4. Rename the folder to `hh_imprint`
5. Login to **webtrees** as administrator, go to <span class="pointer">Control Panel/Modules/Website/Footers</span>, and find the module. It will be called "Imprint". Check if it has a tick for "Enabled".
6. Click at the wrench symbol and add all desired information fields
7. Finally, click SAVE, to complete the installation.

<a name="upgrade"></a>
## Upgrade

To update simply replace the hh_imprint files
with the new ones from the latest release.

<a name="translation"></a>
## Translation

You can help to translate this module.
You can do this via a pull request (if you know how) or by e-mail.
Updated translations will be included in the next release of this module.

There are now, beside English and German, no other translations.

<a name="support"></a>
## Support

<span style="font-weight: bold;">Issues: </span>you can report errors raising an issue in this GitHub repository.

<span style="font-weight: bold;">Forum: </span>general webtrees support can be found at the [webtrees forum](http://www.webtrees.net/).

<a name="license"></a>
## License

* Copyright (C) 2022 Hermann Hartenthaler
* Derived from **webtrees** - Copyright 2022 webtrees development team.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

* * *