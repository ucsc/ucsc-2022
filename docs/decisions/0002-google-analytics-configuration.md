---
parent: Architecture decisions
---

# Use one Google Analytics tracking code on every site

Date: 2022-03-21

## Status

Proposed and testing

## Context

All of our current sites use the Google Analytics Universal Analytics tag to track statistics. Site owners are given access to the Google Analytics dashboard to see their traffic reports. Each site has a custom filter and view based on their \*.ucsc.edu domain name.

Universal Analytics tags will stop collecting site data in 2023. Google Analytics 4 (GA4) is replacing the UA tag.

- ### Analysis

  - While every site owner has access to a GA dashboard, we regularly hear from clients that they struggle to understand their site traffic and don't use GA.
  - The move to GA4 presents an opportunity to change our analytics configuration.
  - Google Tag Manager lets us use both tags at the same time, for a smooth transition.
  - Traffic research for all \*.ucsc.edu websites is difficult and requires moving across multiple profiles and views in the GA dashboard.

- ### Desired End State

  - GA4 is implemented in one reporting dashboard with the ability for users to filter data and generate reports based on their site. Additionally, site by site comparisons are easy because data from every site is available in one reporting dashboard.
  - Comprehensive statistics for \*.ucsc.edu sites is easier and possible for all users.

- ### The following options were considered
  - Consider another statistics provider? Fathom Analytics?

## Decision

## Consequences

- Site admins will need to be taught the new setup and shown how to run reports in the GA4 dashboard.
- Long-term traffic comparisions will require site admins to move between the GA4 dashboard and the old UA dashboards. However, this consequence is inevitable since UA will stop collecting data in 2023.
