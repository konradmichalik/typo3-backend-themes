.. include:: /Includes.rst.txt

=============
Configuration
=============

Theme Records
=============

Theme records are created at the root level (pid=0) in the TYPO3 list module.

Each theme has the following fields:

Title
   The name of the theme (e.g. "Corporate Blue").

Primary Color
   The main accent color. Controls icon colors, button accents, and
   the ``--token-color-primary-base`` CSS variable.

Derive secondary from primary
   When enabled, the secondary color (header/sidebar) is automatically
   derived from the primary color using CSS ``hsl()`` functions.

Secondary Color
   Only visible when automatic derivation is disabled. Sets the
   header and sidebar background color directly.

Default Theme
   Mark one theme as the global default. All users without a personal
   selection will see this theme. Only one theme can be the default.

Dark Mode Override
   Optional primary and secondary color overrides for dark mode. When
   empty, the standard ``light-dark()`` CSS derivation is used.
