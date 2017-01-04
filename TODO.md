## TODO
- Add a config variable to determine if tablets should be considered "mobile" devices or "desktop",
  and get it to actually work.
- Add a config variable to add non-mobile core RL modules (such as
  `mediawiki.legacy.shared`) to mobile automatically. This should be an
  array of module names, and you probably need to follow this:
  https://www.mediawiki.org/wiki/ResourceLoader/Writing_a_MobileFrontend_friendly_ResourceLoader_module#Enabling_your_existing_modules
