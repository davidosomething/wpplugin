DKO WP Plugin Framework
=======================

Ideal setup 
-----------

```git submodule add``` this repo into /wp-content/mu-plugins/wpplugin
Then add a file that includes the individual files you need.
mu-plugins (must use plugins) are loaded before all other plugins so these
classes will be available to them.

Alternative setup
------------------

Use the plugins folder instead of the mu-plugins folder.

Backup method
-------------

This folder and its contents should be placed into your plugin's folder.
The preferred way is as a git submodule or just through copying it to a folder
called ```framework```. Then, require ```framework/base.php``` and make your
plugin class extend the DKOWPPlugin class.

The problem with this method is if you want to use multiple plugins that use
the framework they'll each have their own version of the framework and may
conflict or be out of date. Whichever plugin loads first gets its bundled
version of the framework loaded.
