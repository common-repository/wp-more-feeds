=== Plugin Name ===
Contributors: WP More Feeds
Donate link: http://www.mashget.com
Tags: rss, feed, atom, tags, categories
Requires at least: 2.5
Tested up to: 2.6.3
Stable tag: 0.17

Generate RSS feeds for category and tag archive pages.

== Description ==

Generate RSS feeds for category and tag archive pages.

[ChangeLog](http://www.mashget.com/2008/09/15/wp-more-feeds-changelog/)

**What is RSS?**

Rss is becoming more and more popular, if you’re not familar with it, you may read [this ariticle](http://www.problogger.net/what-is-rss/) by Darren Rowse, who is a very successful blogger. RSS help Darren promote his site and keep his readers, actually I’m one of them:)

**What WP More Feeds does?**

WP More Feeds just simply generate more feed links for category page and tag page, you can call it Category Feed and Tag Feed. That will give the readers more choices.

**How to find the feeds?**

WordPress supports RSS feed, if you have a look at the source page of this page , you may find some code like this in the head:

	<link rel="alternate" type="application/rss+xml" title="MashGet RSS Feed" href="http://feeds.feedburner.com/mashget/" />

That’s it, the RSS Feed, which is for the whole site. If you install WP More Feeds, you can get another feed on category or tag pages.

Actually, most browers will find this feed for you today, for Firefox, you’ll find a RSS icon on the right side of the address bar. See the [ScreenShots](http://wordpress.org/extend/plugins/wp-more-feeds/screenshots/)

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin through Settings -> WP More Feeds(see the [ScreenShots](http://wordpress.org/extend/plugins/wp-more-feeds/screenshots/))

== Frequently Asked Questions ==

= How to add a rss feed link on my category or tag page? =

You may edit your theme template archive.php, the code will be like this:

	<?php if($morefeeds->currentRssInfo):?>
	<span class='mflink'><a href="<?php echo $morefeeds->currentRssInfo['link'];?>" title="<?php echo $morefeeds->currentRssInfo['title'];?>">RSS</a></span>
        <?php endif; ?>

Ye, a little complicated, right? I'll provide some simple way to do this in the next version.

== Screenshots ==

1. The WP More Feeds Options
2. RSS Feed in Firefox
3. RSS Feed in IE
4. RSS Feed in Safari