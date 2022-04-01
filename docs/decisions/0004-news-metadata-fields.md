---
parent: Architecture decisions
---

# Use Advanced Custom Fields Pro to add common post metadata fields

Date: 2022-03-31

## Status

Proposed and testing

## Context

Our WCMS news articles have metadata fields such as subhed and byline that do not map to existing fields in a typical WordPress post. We can use native WordPress metadata fields to add this information to a post, but we cannot create those fields programatically and they are awkward to find in the editor despite being commonly-used and generally added along side the site title.

- ### Analysis

  In our testing we found:

  - WordPress core custom fields are not visible by default in the editor. You need to turn them on before they are visible to the content author.
  - The fact that they are hidden by default seems to indicate their future may be in doubt.
  - Advanced Custom Fields (ACF) is a well-known, well-supported plugin used to add rich fields to WordPress posts and pages.
  - The pro version of ACF allows fields and their settings to be synced to text files within a theme and imported when that theme is used on another site. This gives us a reliable mechanism for managing custom metadata fields across sites.
  - The pro version of ACF is available on CampusPress.

- ### Desired End State

  Post (news article) metadata fields we have in WCMS now are available in WordPress. They are easy to find and easy to use. We can manage them as we manage other elements of the theme. And site admins or content managers cannot delete them or modify the preconfigured fields.

- ### The following options were considered

  We tested WordPress core custom fields. We discovered that they need to be created on a site-by-site basis, and that the interface to add custom fields to a post was hidden by default. Additionally, we noted that the placement of the custom fields interface is separated from the block editor interface.

## Decision

We are still testing this decision, but given the alternatives, it seems like we will use Advanced Custom Fields Pro to manage and synchronize metadata fields across our WordPress sites.

## Consequences

- Sites _outside_ of CampusPress that intend to use the UCSC theme will also need to [pay for Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/#pricing-table).
- Should we ever choose to leave the CampusPress platform, we will need to pay for ACF Pro.
- We will need to devote resources to testing the theme when there is an update to the Advanced Custom Fields Pro plugin.
