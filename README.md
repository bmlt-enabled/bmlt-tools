DESCRIPTION
-----------

The Basic Meeting List Toolbox (BMLT, hereafter) is a very powerful client/server system
that has been written for a very specific purpose, for a very specific clientele.

It is designed to track and locate Narcotics Anonymous meetings, which are regularly-
scheduled weekly, recurring events.

The intended clientele is Narcotics Anonymous Service bodies. They implement a BMLT
server, and provide the server to other NA Service bodies.

You can find out way too much about the BMLT here: http://bmlt.app

You can follow us on Twitter for release announcements: http://twitter.com/BMLT_NA

This is a set of scripts and tools that we make available for messing with the BMLT.

CAVEAT EMPTOR. These scripts are dangerous, which is exactly why we don't release them
as general use scripts.

CHANGELIST
----------

***bmlt_translator Version 1.1.1* ** *- December 16, 2018*

- Updated Google API response code messages.

***bmlt_translator Version 1.1.1* ** *- December 16, 2018*

- Longitude/Latitude for NAWS dump translation table should be uppercased.
- Added support for Google API keys to be able to properly geocode.

***bmlt_translator Version 1.1.0* ** *- May 15, 2016*

- Added support for default decoding of standard NAWS dump.
- Added 4 levels of report:
    - bmlt_import.php?log=minimal
    - bmlt_import.php?log=medium (This is default)
    - bmlt_import.php?log=verbose
    - bmlt_import.php?log=prolix

***bmlt_translator Version 1.0.2* ** *-July 31, 2014*

- Fixed a couple of bugs in the import script, with regards to the postal (zip) code.
    
***bmlt_translator Version 1.0.1* ** *-June 18, 2014*

- A few bug fixes
- Set the password for all users in the example database to "showmethemoney" (Silly Jerry Maguire reference).
- Added a sample HTML report
- Added some existing long/lat fields to some of the meetings, so that we can see what happens when ones are provided.

***bmlt_translator Version 1.0.0* ** *-June 16, 2014*

- Initial Release
