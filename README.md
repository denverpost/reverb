# Reverb

A child theme for Reactor designed for The Denver Post's HeyReverb.com music site, focusing on video and photos.

## Requirements

#### Parents theme

[Reactor (Customized DP version)](http://extras.denverpost.com/media/wp/reactor.zip)

#### Plugins

* [Jetpack](https://wordpress.org/plugins/jetpack/)
* [WP-Email](https://wordpress.org/plugins/wp-email/)
* [WP SEO](https://wordpress.org/plugins/wordpress-seo/)

## Setup

#### Site settings

1. Go to Settings -> General
	* Set the Site Title to "Hey Reverb"

#### Setting up the Front Page

1. Create a blank page called "HeyReverb"
	* Select the template "Front Page" from the templates at right
	* Don't put any content in the page body
2. Head to Settings -> Reading and set "Front page displays"  to "A static page"
	* Select the HeyReverb page you just created under "Front page"
	* While you're here, don't forget to set the RSS feeds to "Summary"

#### Plugin settings

1. Under E-Mail -> E-Mail options:
	* Set "E-Mail Text Link Style" to Custom
	* Set it to: `<a href="%EMAIL_URL%" rel="nofollow" title="%EMAIL_TEXT%" target="_blank">%1$s</a>`
2. Be sure to connect Jetpack to Wordpress.com

#### Theme Customizations

Appearance -> Customize

1. General
	* Uncheck "Show Title & Tagline"
2. Navigation
	* "Top Bar Title" is not displayed (doesn't matter what it is)
	* Check "Fixed Top Bar"
	* Check "Contain Top Bar Width"
3. Posts & Pages
	* Set "Default Layout" to "One Column"
	* Delete all text from "Read More Text" and leave blank
	* Uncheck "Show Post Meta"
	* Uncheck "Show Comment Link"
	* Uncheck "Show Breadcrumbs"
4. Fonts & Colors
	* Set "Title Font" to "Yanone Kaffesatz"
	* Set "Content Font" to "Vollkorn"
	* Set link color to #EEEEEE
5. Front Page
	* Check "Exclude From Blog"
	* Set "Post Columns" to "1 Column"
	* Check "Link Post Titles"
	* Set "Number of Posts" to the number of tiles you want plus 1 (for top article). Initially we're using 7.

#### Menus

1. You will need one menu of categories. Be sure to check the box to display it in the Top Bar Right position. Should be titled "Top bar" or "top bar"
2. Your footer menu must be called "footer links"
	* Should include a link to the home page (Pages -> View All -> HeyReverb)
3. Your last link should be "Subscribe by Email"
	* Be sure the link URL is just "#subscribe"

