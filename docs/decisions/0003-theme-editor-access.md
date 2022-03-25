---
parent: Architecture decisions
---

# Prevent theme editor access for site admins

Date: 2022-03-24

## Status

Accepted

## Context

The site editor in WordPress 5.9 allows site admins to modify their site theme templates using a block editor interface. Theme modifications are stored **locally** in the site's database.

- ### Analysis

  In our testing we found:

  - Local theme changes take precedence over theme file changes. If we update the theme files and release them to a site, the updates won't take effect on the site because of a local theme edit.
  - If the site owner clears their local theme edits so an updated theme file can take effect on their site, they lose ALL customizations they made to that file.

  In short, allowing site owners to make local theme modifications prevents us from updating their theme template files in future releases of the theme.

- ### Desired End State

  Theme updates provided to the network through official releases _must_ take precendence over local theme edits made by site admins. Theme updates may include accessibility improvements, usability improvements, branding updates, and new features enabled by WordPress core.

- ### The following options were considered

  We tested a local theme update followed by a code update to a theme file. The code update was ignored in favor of the local theme edit. No other options are available to mitigate this issue.

## Decision

Site admins will not be allowed to use the theme editor until there is a mechanism in WordPress core to update the theme and keep local edits.

## Consequences

- Pages with areas for customization, like news home pages (right column widgets), need to be created ahead of time for new sites and included in the site template we copy for new sites.
