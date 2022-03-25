---
parent: Architecture decisions
---

# Launch on WordPress v5.8 with Gutenberg plugin

Date: 2022-03-11

## Status

Accepted

## Context

CampusPress is currently running WordPress version 5.8.x. They plan to upgrade to v5.9 in the summer of 2022.

- ### Analysis

  To enable the theme editor and some of the v5.9 features we're developing for, we need to build the theme for v5.8 with the Gutenberg plugin enabled.

- ### Desired End State

  Seemlessly transition to WordPress 5.9 and support it's native theme editor features.

- ### The following options were considered

  - We asked CampusPress to upgrade our network to 5.9. They indicated it was not possible before they are ready to upgrade the rest of their networks.

## Decision

We will launch the theme for WordPress 5.8 and have the Gutenberg plugin enabled on our CampusPress networks.

## Consequences

Some differences exist between the theme editor in v5.9 and the v5.8 configuration described above. We've run into a couple of small issues. We will need to test the theme throroughly before our networks are upgraded to 5.9.
