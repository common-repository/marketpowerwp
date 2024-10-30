=== MarketPowerWP ===
Contributors: dine@multisoft.com
Tags: marketpowerpro, multisoft
Requires at least: 3.4+
Stable tag: 2.2.9
Version: 2.2.9
Tested up to: 5.5
License: Commercial

This is a plugin that communicates with the MarketPowerPRO web services API to be used in your Wordpress site.

== Description ==

[youtube https://www.youtube.com/watch?v=nSZgYj94Gmk]

This is a plugin that communicates with the [MarketPowerPRO][1] web services API to be used in your Wordpress site. The plugin generates shortcodes that you can use in building pages in your Wordpress site. Here's an example:

[mppe]Hello my name is MPPE\_FIRST\_NAME![/mppe]

Available shortcodes are:

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_FIRSTNAME
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_LASTNAME
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_BIRTHDAY
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_EMAIL
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_ADDRESS1
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_ADDRESS2
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_CITY
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_COUNTY
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_POSTALCODE
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_HOMEPHONE
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_BUSINESSPHONE
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_CELL
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_FAX
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_REGIONNAME
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_COUNTRYNAME
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_SITENAME
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_WEBSITE
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_DISTRIBUTORID
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
  MPPE_COMMONID
</div>

<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_YAHOOID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_MSNID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_ICQID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_AIMID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_SKYPEID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_FACEBOOKID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_TWITTERID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_LINKEDINID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_GOOGLEPLUSID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_YOUTUBEID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_RSSFEEDID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_PINTERESTID
</div>
<div style="color: #222222; font-family: arial, sans-serif; font-size: 13px;">
    MPPE_INSTAGRAMID
</div>



 [1]: http://www.marketpowerpro.com "f"

== Installation ==

[youtube https://www.youtube.com/watch?v=BkHmPWjUt4M]

1. Install and activate the plugin to your wordpress site.

2. Go to the MarketPowerPRO > Settings menu in your wordpress admin.

3. Set your Base MarketPowerPRO Web address, where your MarketPowerPRO system is located.

4. Contact a MarketPowerPRO technical assistant to know your applicationID and set it to the applicationID field.

5. Set (or empty) a default replicated site name to be used in case none was provided in the URL (the default is "admin")

5. Check "Enable auto-prefix replication site name to URLs" option to enable replication of URLs in your website.

6. Select a page (or empty) to redirect to in case a replicated site name is non-existing.

6. Go to Settings > Permalinks then set your URL Structure to Post Name then click Save Changes
