.. include:: /Includes.rst.txt

=========
Dark Mode
=========

The extension fully supports TYPO3's dark mode.

Automatic Derivation
====================

When no dark mode overrides are configured, colors are automatically
adjusted using the CSS ``light-dark()`` function:

*  Header/sidebar: ``light-dark(hsl(h 40% 20%), hsl(h 20% 10%))``
*  Icon accents: ``light-dark(hsl(h 80% 70%), hsl(h 60% 60%))``

Manual Override
===============

For fine-grained control, configure explicit dark mode colors:

Primary Color (Dark Mode)
   Overrides ``--token-color-primary-base`` in dark mode.

Secondary Color (Dark Mode)
   Overrides header and sidebar backgrounds in dark mode.
