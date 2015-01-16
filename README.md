# Reverb

A child theme for Reactor designed for The Denver Post's HeyReverb.com music site, focusing on video and photos.

## Setting up for dev, beta or prod

#### Required parent theme

[Reactor (Customized DP version)](http://extras.denverpost.com/media/wp/reactor.zip)

#### Required Plugins

* [Flexible Posts Widget](https://wordpress.org/plugins/flexible-posts-widget/)
* [SEM Author Image](https://wordpress.org/plugins/sem-author-image/)
* [Jetpack](https://wordpress.org/plugins/jetpack/)
* [WP-Email](https://wordpress.org/plugins/wp-email/)
* [WP SEO](https://wordpress.org/plugins/wordpress-seo/)

## Setup

### Site settings

1. Go to Settings -> General
	* Set the Site Title to "Hey Reverb"
	* Set the 

### Theme Customizations

All found in **Appearance -> Customize**

1. General
	* Set *Title* to **Reverb**
	* Set *Tagline* to **Music news and analysis from Reverb**
	* Uncheck *Show Title & Tagline*
2. Navigation
	* Set *Top Bar Title* to **Reverb**
	* Set *Top Bar Title Link* to the appropriate fully-qualified URL (localhost, beta, etc.)
	* Uncheck *Fixed Top Bar*
	* Check *Sticky Top Bar*
	* Check *Contain Top Bar Width*
	* Check *Enable Top Bar Search*
3. Posts & Pages
	* Set *Default Layout* to **Two Columns, Left**
	* Uncheck *Show Breadcrumbs*
4. Fonts & Colors
	* Set *Title Font* to **Alegreya Sans**
	* Set *Content Font* to **Alegreya Sans**
5. Front Page
	* Check *Exclude From Blog*
	* Set *Post Columns* to **1 Column**
	* Set *Number of Posts* to **10** or adjust as desired
	* Check *Link Post Titles*
	* Check *Show Page Links*

Theme-based widgets, found in **Appearance -> Widgets**:

1. Add the **Newsletter Signup** widget to the *Primary Sidebar* in the third position or where desired.

### Plugin Setup

1. WP-Email
	* **E-Mail -> E-Mail Options**
	* Set *E-Mail Text Link Style* to **Custom** and paste in `<a href="%EMAIL_URL%" rel="nofollow" title="%EMAIL_TEXT%"><span class="fi-mail">Email</span></a>`
2. Jetpack
	* If on beta/prod, connect Jetpack to Wordpress using the DPO account.
3. Flexible Posts Widget
	* **Appearance -> Widgets**
	* Add *Flexible Post Widget* widgets to **Front Page Secondary** sidebar
	* Each one needs the following settings:
		* Set *Title* to something that makes sense -- it isn't displayed but will help you keep track when reordering widgets.
		* *Get posts by* -> *Taxonomy & Term*
		* Set *Select a taxonomy:* to **Categories**
		* Check **one** appropriate category under *Select terms:*
		* Set *Number of posts to show* to **1**
		* Check *Display thumbnails?*
		* Set *Select a thumbnail size to show:* to **medium**
		* Set *Template filename* to **Widget**
		* Click *Save*
	* You can add, remove or rearrange these as needed. They appear on the left edge of the home page.
4. Activate the **Author Image** plugin

### Set up Pages

#### Front Page

1. Create a blank page called **Music news and analysis from The Denver Post**
	* Select the template **Front Page** from the *Page Template* dropdown at right
	* Don't put any content in the page body
2. Head to Settings -> Reading and set *Front page displays*  to **A static page**
	* Select the **Music news and analysis from The Denver Post** page you just created under "Front page"
	* While you're here, don't forget to set the RSS feeds to "Summary"

#### About Page

1. Create a blank page called **About**
	* Select the template **About Page** from the *Page Template* dropdown at right
	* Write or paste in the About content you want. Authors are added automatically.
2. Add to the **footer links** menu (see below)

#### Advertise Page

1. Create a blank page called **Advertise**
	* Write or paste in the Advertise content you want.
2. Add to the **footer links** menu (see below)

### Menus

When adding items to menus, you can add or edit the *Navigation label* (displayed text) and *Title Attribute* (displayed on hover, used for SEO and Accessibility) under *Menu Structure* after the item is added.

#### Top Bar menu

* Name the menu something like **Top Bar** or **Main Navigation**
* Assign to the *Top Bar Left* position
* Add 6 top-level categories and place sub categories beneath them as desired

#### Footer Links menu

* Must be named **footer links**
* Assign to the *Footer Menu* position

1. From the *Pages* tab, add the **About** page you created above
2. From the *Links* tab, add the following links:
	* Ethics Policy
		* URL: **http://www.denverpost.com/ethics**
		* Navigation label: **Ethics Policy**
		* Title Attribute: **Denver Post Ethics Policy**
	* Terms of Use
		* URL: **http://www.denverpost.com/termsofuse**
		* Navigation label: **Terms of Use**
		* Title Attribute: **Denver Post Terms of Use**
	* Privacy Policy
		* URL: **http://www.denverpost.com/privacypolicy**
		* Navigation label: **Privacy Policy**
		* Title Attribute: **Denver Post Privacy Policy**
3. From the *Pages* tab, add the **Advertise** page
