# Bootstrap Basis
## Requirements
* NPM v 10.1.0
* NodeJS v20.9.0

## Development
```npm run watch```

## Production
```npm run prod```

## Critical CSS
```npm run critical```

For the generation of critical css, chromium is used. It may be the case that some packages are missing.
In this case the generation process exits with the following message:
```
error while loading shared libraries: libX11-xcb.so.1: cannot open shared object file: No such file or directory
```
You can fix this by installing the libs needed:
```
sudo apt-get update
```
Full list of dependencies:
```
sudo apt-get install gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1  libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget
```
Reduced and sufficient list of dependencies:
```
sudo apt-get install libasound2 libasound2-data libatk1.0-0 libc6 libcairo2 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libx11-xcb1 libxss1 fonts-liberation libappindicator1 libnss3 xdg-utils
```

# Flyout-Navigation
## Usage
Integrate the CSS- and JS-file into your project. Make sure jQuery is included.
Then init the menu with
```
document.querySelectorAll('.js-flyout-toggle').forEach((el) => {
  new Madj2kFlyoutMenu(el, { animationDuration: 800 });
});
```
## Basic HTML
```
<div class="siteheader" id="siteheader">

    [...]

    <nav>
        <button class="js-flyout-toggle"
            aria-label="Open menu"
            aria-controls="nav-mobile"
            aria-haspopup="true"
            aria-expanded="false">
            <span class="icon-bars"></span>
        </button>
        <div class="flyout js-flyout"
             id="nav-mobile"
             data-position-ref="siteheader">
            <div class="flyout-container js-flyout-container">
                <div class="nav-mobile-inner js-flyout-inner">
                    CONTENT HERE
                </div>
            </div>
        </div>
    </nav>
</div>
```
IMPORTANT: If the siteheader is positioned with ```position:fixed```, you have to switch that to ```position:absolute``` when the menu is opened.
Otherwise in the opened menu the scrolling won't work.
```
.flyout-open {
    .siteheader {
        position:absolute;
    }
}
```
## Special: blur/gray effect for background
* In order to achieve a blur/gray-effect for the background we add the following DIV to the main-content section:
```
<div class="background-blur"></div>
```
Then we deactivate the fullHeight of the flyout menu and make the blurry background clickable
```
document.querySelectorAll('.js-flyout-toggle').forEach((el) => {
  new Madj2kFlyoutMenu(el, { fullHeight: false });
});
document.querySelector('.js-background-blur').addEventListener('click', function() {
    document.dispatchEvent(new CustomEvent('madj2k-flyoutmenu-close'));
});
```
* And we need this additional CSS:
```
/**
 * Prevent jumping because of scrollbar
 */
html,body {
    min-height: 100.1%;
}

/**
 * background-blur for opened flyout
 */
.background-blur {
    position:fixed;
    top:0;
    left:0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(1px) grayscale(100%);
    background-color: rgba(255, 255, 255, 0.7);
    transition: opacity 0.3s ease-in-out;
    opacity:0;
    z-index:-1;
}

.flyout-open,
.flyout-closing {
    .background-blur {
        z-index:90;
    }
}

.flyout-open{
    .background-blur {
        opacity:1;
    }
 }


```

# Pulldown-Navigation
## Usage
Integrate the CSS- and JS-file into your project. Make sure jQuery is included.
Then init the menu with
```
document.querySelectorAll('.js-pulldown-toggle').forEach((el) => {
  new Madj2kPulldownMenu(el);
});
```
## Basic HTML
```
<div class="siteheader">

    [...]

    <nav class="pulldown-wrap js-pulldown-wrap">
        <button class="pulldown-toggle js-pulldown-toggle"
           aria-label="Open Menu"
           aria-controls="nav-language"
           aria-haspopup="true"
           aria-expanded="false">
            <span>Menu-Item Level 1</span>
        </button>

        <div class="pulldown js-pulldown" id="nav-language">
            <div class="pulldown-inner">
                <ul>
                    <!-- used to have the right padding-top of the pulldown -->
                    <li class="pulldown-hide">
                        <a href="#" tabIndex="-1"><span>Menu-Item Level 1</span></a>
                    </li>
                    <li>
                        <a href="#"><span>Menu-Item I Level 2</span></a>
                    </li>
                    <li>
                        <a href="#"><span>Menu-Item II Level 2</span></a>
                    </li>
                    <li>
                        ...
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
```

# Slide-Navigation (Off-Canvas)
## Usage
Integrate the CSS- and JS-file into your project. Make sure jQuery is included.
The menu has to be configured via JSON. Use the JsonMenuViewHelper for this.
Then init the menu with
```
$('.js-slide-nav-toggle').madj2kSlideMenu({ menuItemsJson: slideNavItems});
```

# Basic HTML
```
<div class="navbar-wrap" id="navbar-wrap">
    <nav class="navbar">
        <button href="#" class="nav-iconlink js-slide-nav-toggle"
                aria-label="Open Menu"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="slide-nav">
            <span>Open Menu</span>
        </button>
        <a href="#" class="nav-link">
            <span>Another link</span>
        </a>
    </nav>
</div>

<!-- will be dynamically filled, do not remove! -->
<nav class="slide-nav" id="slide-nav" data-position-ref="navbar-wrap"></nav>

<!--
    Templates for mobile menu. We don't want this to be loaded on default because
     it would duplicate the menu for search engines and would enlarge the body to parse
-->
<template class="js-slide-nav-tmpl" data-type="menuWrap">
    <div class="slide-nav-container js-slide-nav-container">
        <div class="slide-nav-card js-slide-nav-card %levelClass%" id="slide-card-%uid%">
            <div class="slide-nav-inner">
                <ul class="slide-nav-list" role="none">
                    %menuItems%
                    <li class="slide-nav-item slide-nav-item-footer" role="none">
                        <ul class="slide-nav-list" role="none">
                            <li class="slide-nav-item" role="none">
                                <a href="#" class="slide-nav-link">Lorem Ipsum</a>
                            </li>
                            <li class="slide-nav-item">
                                <a href="#" class="slide-nav-link">Lorem Ipsum</a>
                            </li>
                            <li class="slide-nav-item">
                                <a href="#" class="slide-nav-link">Lorem Ipsum</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<template class="js-slide-nav-tmpl" data-type="menuItem">
    <li class="slide-nav-item js-slide-nav-item %activeClass% %hasChildrenClass%" role="none">
        %ifHasNoChildrenStart%
            <a href="%link%"
               title="%titleRaw%"
               role="menuitem"
               class="slide-nav-link %activeClass% %hasChildrenClass%"
               target="%target%"
               aria-current="%ariaCurrent%">
                <span>%title%</span>
            </a>
        %ifHasNoChildrenEnd%

        %ifHasChildrenStart%
            <a class="slide-nav-link slide-nav-next js-slide-nav-next %activeClass% %hasChildrenClass%"
               href="#"
               role="button"
               title="Open Sub-Menu"
               aria-label="Open Sub-Menu"
               aria-haspopup="true"
               aria-expanded="%ariaExpanded%"
               aria-controls="slide-card-%uid%">
                <span>%title%</span><span>&gt;</span>
            </a>
        %ifHasChildrenEnd%

        %submenu%
    </li>
</template>

<template class="js-slide-nav-tmpl" data-type="subMenuWrap">
    <div class="slide-nav-card js-slide-nav-card %activeClass% %levelClass%" id="slide-card-%uid%">
        <div class="slide-nav-inner">
            <ul class="slide-nav-list" role="none">
                <li class="slide-nav-item slide-nav-item-back" role="none">
                    <button class="slide-nav-back js-slide-nav-back"
                            aria-haspopup="true"
                            aria-label="One Level Up"
                            aria-controls="slide-card-%uid%"
                            data-parent-card="slide-card-%parentUid%">&lt;<span
                        class="slide-nav-back-label">Zurück</span>
                    </button>
                </li>
                %menuItems%
            </ul>
        </div>
    </div>
</template>


<script>
    let slideNavItems = <f:format.raw><rkw:jsonMenu items="{menuItems}" /></f:format.raw>;
</script>

```
