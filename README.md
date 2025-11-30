# Scheduler 1.0

Easily schedule draft blog posts to be published at a later date.

## Usage

The **Blog** and **Draft** extensions are required for scheduler to work.

To change the publish interval add `SchedulerPublishInterval: 6` to your yellow-system.ini. You can skip this to use the default 6 hours.

The number is in hours, 6 hours is the default. If you're a busy blogger, you may want to set it to 1 (hour). Or if you're a occasional blogger maybe 48 (hours) makes more sense. Whatever works for you. Keep in mind that the longer you set the interval, if you add a file right after the next check it'll take a while for your blog post to appear.

To schedule a post use the `Status` page setting and the `Publish` Page setting.
You'll set the `status` to `draft` and the `Published` date to some future date, as shown below. This can be just the date or include a time.

```
---
Title: New scheduled post
Description: Some kind of scheduled description
Tag: tag, tag, tag
Status: draft
Published: 2025-09-02
Author: Arnan de Gans
Language: en
Layout: blog
---

A load of blabla that is scheduled to appear at the Published date. So much blabla in fact, that it requires to be scheduled so your readers can digest the full weight of your blabla in several posts spread over a period of time!
```

Or with the time, if you want to schedule posts for certain hours of the day.
```
---
Title: New scheduled post
Description: Some kind of scheduled description
Tag: tag, tag, tag
Status: draft
Published: 2025-09-02 22:33:00
Author: Arnan de Gans
Language: en
Layout: blog
---

A load of blabla that is scheduled to appear at the Published date. So much blabla in fact, that it requires to be scheduled so your readers can digest the full weight of your blabla in several posts spread over a period of time!
```

Scheduler runs on a 'dumb' timer, a time comparison really. Once the timer runs out it schecks if posts need to be published using built-in functions. If it finds an eligible post, that post file is edited and the Status is set from draft to public.

The next time your blog is loaded the new post should show up as if you just uploaded it yourself.

## Troubleshooting
The extension has no visual output and does not show errors on your website.
It silently fails and writes errors to the log.

## Changelog

1.0 - 2025-11-29
* Initial version

## How to install an extension

[Download ZIP file](https://github.com/adegans/yellow-scheduler/archive/refs/heads/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## Developer

Arnan de Gans
