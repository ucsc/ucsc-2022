---
description: The official theme for UC Santa Cruz WordPress sites.
nav_order: 1
nav_exclude: true
---

# WordPress for UC Santa Cruz websites

{: .fs-5 }
Preconfigured so you can communicate effectively to your UCSC audience

{: .fs-7 .fw-300 }
[Get started](get-started.md){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 } [View it on GitHub]({{ site.github.repository_url }}){: .btn .fs-5 .mb-4 .mb-md-0 }

---

## A tool for you to tell the UCSC story to your site visitors

The UCSC WordPress theme has been built from the very beginning to be:

- ### Ready for the Block Editor

  All of the blocks in WordPress core are tested and supported. Additional blocks are available via plugins to support UCSC web services like the campus directory, class schedules, and (coming soon) the campus events calendar.

- ### Branded to campus design standards

  Version 1.0 of the theme uses the visual design of campus WCMS websites. Future versions of the theme will evolve the design while maintaining backwards compatibility.

## Contributors

The UCSC WordPress theme is maintained by the Digital Strategies team in the campus [Communications & Marketing](https://communications.ucsc.edu) office. If you have any questions about this project, you can contact [Rob Knight](https://campusdirectory.ucsc.edu/cd_detail?uid=raknight), [Jason Chafin](https://campusdirectory.ucsc.edu/cd_detail?uid=jchafin), or [submit an issue]({{ site.github.issues_url }}) here on Github.

{% for contributor in site.github.contributors -%}
[![{{ contributor.login }}]({{ contributor.avatar_url }}){: width="48" }]({{ contributor.html_url }})
{% endfor %}
